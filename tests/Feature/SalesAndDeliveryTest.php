<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DeliveryRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\DeliveryService;
use App\Services\SaleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SalesAndDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_sale_computes_total_and_reduces_stock(): void
    {
        $product = Product::where('product_name', 'New filled round')->first();
        $before = $product->item->filled_qty;

        $sale = app(SaleService::class)->record(User::where('username', 'counter')->first(), 'Walk-in', null,
            [['product_id' => $product->id, 'quantity' => 2]]);

        $this->assertEquals(500.00, $sale->total_amount);
        $this->assertSame($before - 2, $product->item->fresh()->filled_qty);
        $this->assertDatabaseCount('stock_movements', 1);
    }

    public function test_oversell_is_rejected_and_nothing_is_saved(): void
    {
        $product = Product::where('product_name', 'Dispenser')->first();

        try {
            app(SaleService::class)->record(User::where('username', 'counter')->first(), 'Walk-in', null,
                [['product_id' => $product->id, 'quantity' => 999]]);
            $this->fail('Expected a ValidationException.');
        } catch (ValidationException) {
            $this->assertSame(0, Sale::count());
            $this->assertSame(6, $product->item->fresh()->filled_qty);
        }
    }

    public function test_delivered_request_creates_a_sale(): void
    {
        $rider = User::where('username', 'rider')->first();
        $delivery = DeliveryRequest::create([
            'customer_id' => Customer::first()->id, 'product_id' => Product::first()->id, 'quantity' => 3,
            'delivery_address' => 'Test St.', 'preferred_date' => today(),
        ]);

        $service = app(DeliveryService::class);
        $service->assign($delivery, $rider);
        $service->updateStatus($delivery->fresh(), 'Out for Delivery', $rider);
        $service->updateStatus($delivery->fresh(), 'Delivered', $rider);

        $saleId = $delivery->fresh()->sale_id;
        $this->assertNotNull($saleId);
        $this->assertEquals(75.00, Sale::find($saleId)->total_amount);
    }

    public function test_riders_cannot_open_the_owner_dashboard(): void
    {
        $this->actingAs(User::where('username', 'rider')->first())->get('/dashboard')->assertForbidden();
    }
}
