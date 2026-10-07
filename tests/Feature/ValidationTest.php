<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DeliveryRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function signedIn(string $username): static
    {
        return $this->actingAs(User::where('username', $username)->first());
    }

    private function sale(array $lines, string $user = 'counter')
    {
        return $this->signedIn($user)->post('/sales', ['payment_status' => 'Paid', 'lines' => $lines]);
    }

    public function test_login_rejects_wrong_password_and_empty_fields(): void
    {
        $this->post('/login', ['username' => 'owner', 'password' => 'wrong'])->assertSessionHasErrors('username');
        $this->post('/login', ['username' => '', 'password' => ''])->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
    }

    public function test_inactive_account_cannot_log_in(): void
    {
        User::where('username', 'counter')->update(['status' => 'Inactive']);
        $this->post('/login', ['username' => 'counter', 'password' => 'password'])->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_sale_rejects_bad_quantities(): void
    {
        $pid = Product::first()->id;
        foreach ([0, -1, 501, 'abc', 1.5] as $bad) {
            $this->sale([['product_id' => $pid, 'quantity' => $bad]])->assertSessionHasErrors('lines.0.quantity');
        }
        $this->assertSame(0, Sale::count());
    }

    public function test_sale_without_a_product_is_rejected(): void
    {
        $this->sale([['product_id' => '', 'quantity' => '']])->assertSessionHasErrors('lines.0.product_id');
        $this->assertSame(0, Sale::count());
    }

    public function test_oversell_shows_an_error_and_saves_nothing(): void
    {
        $dispenser = Product::where('product_name', 'Dispenser')->first();
        $this->sale([['product_id' => $dispenser->id, 'quantity' => 500]])->assertSessionHasErrors('stock');
        $this->assertSame(0, Sale::count());
        $this->assertSame(6, $dispenser->item->fresh()->filled_qty);
    }

    public function test_same_product_on_two_lines_is_counted_together(): void
    {
        $d = Product::where('product_name', 'Dispenser')->first();   // 6 in stock
        $this->sale([['product_id' => $d->id, 'quantity' => 4], ['product_id' => $d->id, 'quantity' => 4]])
            ->assertSessionHasErrors('stock');
        $this->assertSame(6, $d->item->fresh()->filled_qty);
    }

    public function test_delivery_date_in_the_past_is_rejected(): void
    {
        $this->signedIn('counter')->post('/deliveries', [
            'customer_id' => Customer::first()->id, 'product_id' => Product::first()->id, 'quantity' => 1,
            'delivery_address' => 'Test Street 1', 'preferred_date' => today()->subDay()->toDateString(),
        ])->assertSessionHasErrors('preferred_date');
        $this->assertSame(0, DeliveryRequest::count());
    }

    private function delivery(string $status, ?User $rider): DeliveryRequest
    {
        return DeliveryRequest::create([
            'customer_id' => Customer::first()->id, 'product_id' => Product::first()->id, 'quantity' => 2,
            'delivery_address' => 'Test Street 1', 'preferred_date' => today(),
            'status' => $status, 'assigned_to' => $rider?->id,
        ]);
    }

    public function test_rider_cannot_touch_another_riders_delivery(): void
    {
        $other = User::create(['full_name' => 'Other Rider', 'username' => 'rider2', 'password' => 'password', 'role' => 'delivery']);
        $d = $this->delivery('Assigned', $other);

        $this->signedIn('rider')->post("/deliveries/{$d->id}/status", ['status' => 'Out for Delivery'])->assertForbidden();
        $this->assertSame('Assigned', $d->fresh()->status);
    }

    public function test_completing_a_delivery_twice_creates_only_one_sale(): void
    {
        $rider = User::where('username', 'rider')->first();
        $d = $this->delivery('Out for Delivery', $rider);

        $this->signedIn('rider')->post("/deliveries/{$d->id}/status", ['status' => 'Delivered']);
        $this->signedIn('rider')->post("/deliveries/{$d->id}/status", ['status' => 'Delivered'])->assertSessionHasErrors('status');

        $this->assertSame(1, Sale::count());
    }

    public function test_invalid_status_value_is_rejected(): void
    {
        $rider = User::where('username', 'rider')->first();
        $d = $this->delivery('Assigned', $rider);

        $this->signedIn('rider')->post("/deliveries/{$d->id}/status", ['status' => 'Teleported'])->assertSessionHasErrors('status');
        $this->assertSame('Assigned', $d->fresh()->status);
    }

    public function test_unknown_page_shows_the_friendly_404(): void
    {
        $this->signedIn('owner')->get('/no-such-page')->assertNotFound()->assertSee('Page not found');
    }
}
