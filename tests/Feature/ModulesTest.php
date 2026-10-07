<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\SaleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function as(string $username): static
    {
        return $this->actingAs(User::where('username', $username)->first());
    }

    public function test_every_screen_loads_for_the_owner(): void
    {
        foreach (['/dashboard', '/sales', '/sales/create', '/deliveries', '/customers', '/customers/create', '/inventory',
                  '/inventory/movements', '/inventory/adjust', '/items/create', '/products', '/products/create',
                  '/reports', '/users', '/users/create'] as $url) {
            $this->as('owner')->get($url)->assertOk();
        }
    }

    public function test_role_boundaries(): void
    {
        $this->as('counter')->get('/customers')->assertOk();
        $this->as('counter')->get('/reports')->assertForbidden();
        $this->as('counter')->get('/users')->assertForbidden();
        $this->as('counter')->get('/products')->assertForbidden();
        $this->as('rider')->get('/customers')->assertForbidden();
        $this->as('rider')->get('/inventory')->assertForbidden();
        $this->as('rider')->get('/deliveries')->assertOk();
    }

    public function test_customer_validation_and_create(): void
    {
        $this->as('counter')->post('/customers', ['customer_name' => '', 'customer_type' => 'Nope', 'contact_number' => 'abc'])
            ->assertSessionHasErrors(['customer_name', 'customer_type', 'contact_number']);

        $this->as('counter')->post('/customers', ['customer_name' => 'Ana Reyes', 'customer_type' => 'Household', 'contact_number' => '0917 123 4567'])
            ->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', ['customer_name' => 'Ana Reyes']);
    }

    public function test_customer_with_sales_cannot_be_deleted_but_unused_one_can(): void
    {
        $used = Customer::first();
        app(SaleService::class)->record(User::where('username', 'counter')->first(), 'Walk-in', $used->id,
            [['product_id' => Product::first()->id, 'quantity' => 1]]);

        $this->as('owner')->delete("/customers/{$used->id}")->assertSessionHasErrors('customer');
        $this->assertDatabaseHas('customers', ['id' => $used->id]);

        $unused = Customer::create(['customer_name' => 'Temp', 'customer_type' => 'Other']);
        $this->as('owner')->delete("/customers/{$unused->id}")->assertRedirect('/customers');
        $this->assertDatabaseMissing('customers', ['id' => $unused->id]);

        $this->as('counter')->delete("/customers/{$unused->id}")->assertForbidden();
    }

    public function test_physical_product_must_be_linked_to_stock(): void
    {
        $this->as('owner')->post('/products', ['product_name' => 'Faucet', 'category' => 'Accessory', 'unit_price' => '150', 'is_active' => '1'])
            ->assertSessionHasErrors('item_id');
        $this->as('owner')->post('/products', ['product_name' => 'Refill - tall', 'category' => 'Refill', 'unit_price' => '30', 'is_active' => '1'])
            ->assertSessionHasNoErrors();
        $this->as('owner')->post('/products', ['product_name' => 'Refill - tall', 'category' => 'Refill', 'unit_price' => '30'])
            ->assertSessionHasErrors('product_name');
    }

    public function test_inactive_product_cannot_be_sold(): void
    {
        $p = Product::first();
        $p->update(['is_active' => false]);

        $this->as('counter')->post('/sales', ['payment_status' => 'Paid', 'lines' => [['product_id' => $p->id, 'quantity' => 1]]])
            ->assertSessionHasErrors('lines.0.product_id');
        $this->assertSame(0, Sale::count());
    }

    public function test_refill_moves_empties_to_filled_and_is_logged(): void
    {
        $item = InventoryItem::where('item_name', 'like', 'Round%')->first();   // 40 filled / 25 empty

        $this->as('counter')->post('/inventory/adjust', ['item_id' => $item->id, 'action' => 'refill', 'quantity' => 10])
            ->assertRedirect('/inventory');

        $item->refresh();
        $this->assertSame(50, $item->filled_qty);
        $this->assertSame(15, $item->empty_qty);
        $this->assertSame(1, StockMovement::where('item_id', $item->id)->count());
    }

    public function test_refill_beyond_available_empties_is_rejected(): void
    {
        $item = InventoryItem::where('item_name', 'like', 'Round%')->first();

        $this->as('counter')->post('/inventory/adjust', ['item_id' => $item->id, 'action' => 'refill', 'quantity' => 999])
            ->assertSessionHasErrors('stock');
        $this->assertSame(40, $item->fresh()->filled_qty);
    }

    public function test_only_owner_can_adjust_and_adjustments_need_a_reason(): void
    {
        $item = InventoryItem::first();

        $this->as('counter')->post('/inventory/adjust', ['item_id' => $item->id, 'action' => 'remove_filled', 'quantity' => 1, 'remarks' => 'x'])
            ->assertSessionHasErrors('action');
        $this->as('owner')->post('/inventory/adjust', ['item_id' => $item->id, 'action' => 'remove_filled', 'quantity' => 1])
            ->assertSessionHasErrors('remarks');
        $this->as('owner')->post('/inventory/adjust', ['item_id' => $item->id, 'action' => 'remove_filled', 'quantity' => 1, 'remarks' => 'Cracked container'])
            ->assertSessionHasNoErrors();
        $this->assertSame(39, $item->fresh()->filled_qty);
    }

    public function test_new_item_opening_stock_is_logged(): void
    {
        $this->as('owner')->post('/items', ['item_name' => 'Gallon cap', 'item_type' => 'Accessory', 'low_stock_level' => 5, 'filled_qty' => 20, 'empty_qty' => 0])
            ->assertRedirect('/inventory');

        $item = InventoryItem::where('item_name', 'Gallon cap')->first();
        $this->assertSame(20, $item->filled_qty);
        $this->assertSame(1, $item->movements()->count());
    }

    public function test_user_rules(): void
    {
        $this->as('owner')->post('/users', ['full_name' => 'New Staff', 'username' => 'new staff', 'role' => 'counter', 'status' => 'Active', 'password' => 'abc', 'password_confirmation' => 'abc'])
            ->assertSessionHasErrors(['username', 'password']);
        $this->as('owner')->post('/users', ['full_name' => 'New Staff', 'username' => 'counter', 'role' => 'counter', 'status' => 'Active', 'password' => 'Secret123', 'password_confirmation' => 'Secret123'])
            ->assertSessionHasErrors('username');   // already taken
        $this->as('owner')->post('/users', ['full_name' => 'New Staff', 'username' => 'newstaff', 'role' => 'counter', 'status' => 'Active', 'password' => 'Secret123', 'password_confirmation' => 'Secret123'])
            ->assertRedirect('/users');
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Secret123', User::where('username', 'newstaff')->first()->password));
    }

    public function test_owner_cannot_lock_themselves_out(): void
    {
        $owner = User::where('username', 'owner')->first();

        $this->actingAs($owner)->put("/users/{$owner->id}", ['full_name' => 'Station Owner', 'username' => 'owner', 'role' => 'owner', 'status' => 'Inactive'])
            ->assertSessionHasErrors('status');
        $this->assertSame('Active', $owner->fresh()->status);
    }

    public function test_deactivated_user_loses_access_immediately(): void
    {
        $counter = User::where('username', 'counter')->first();
        $this->actingAs($counter)->get('/sales')->assertOk();

        $counter->update(['status' => 'Inactive']);
        $this->actingAs($counter->fresh())->get('/sales')->assertRedirect('/login');
    }

    public function test_unpaid_sale_can_be_marked_paid(): void
    {
        $sale = app(SaleService::class)->record(User::where('username', 'counter')->first(), 'Walk-in', null,
            [['product_id' => Product::first()->id, 'quantity' => 1]], 'Unpaid');

        $this->as('counter')->post("/sales/{$sale->id}/paid")->assertSessionHasNoErrors();
        $this->assertSame('Paid', $sale->fresh()->payment_status);
        $this->as('counter')->get("/sales/{$sale->id}")->assertOk()->assertSee('Sale #'.$sale->id);
    }

    public function test_reports_totals_export_and_bad_ranges(): void
    {
        app(SaleService::class)->record(User::where('username', 'counter')->first(), 'Walk-in', null,
            [['product_id' => Product::where('product_name', 'New filled round')->first()->id, 'quantity' => 2]]);

        $this->as('owner')->get('/reports')->assertOk()->assertSee('500.00');
        $this->as('owner')->get('/reports?from=2026-13-45&to=oops')->assertOk()->assertSee('Showing this month instead');
        $this->as('owner')->get('/reports?from=2026-10-10&to=2026-10-01')->assertOk()->assertSee('cannot be before');

        $csv = $this->as('owner')->get('/reports/export')->assertOk()->streamedContent();
        $this->assertStringContainsString('Sale #', $csv);
        $this->assertStringContainsString('500.00', $csv);
    }

    public function test_sales_filters_ignore_garbage(): void
    {
        $this->as('owner')->get('/sales?type=Hacked&from=zzz')->assertOk();
        $this->as('owner')->get('/deliveries?status=Hacked')->assertOk();
    }
}
