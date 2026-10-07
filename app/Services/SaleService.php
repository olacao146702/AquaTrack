<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(private InventoryService $inventory) {}

    /**
     * @param array<int, array{product_id:int, quantity:int}> $lines
     */
    public function record(User $user, string $type, ?int $customerId, array $lines, string $payment = 'Paid'): Sale
    {
        return DB::transaction(function () use ($user, $type, $customerId, $lines, $payment) {
            $sale = Sale::create([
                'customer_id' => $customerId, 'user_id' => $user->id,
                'sale_type' => $type, 'payment_status' => $payment,
            ]);

            $total = 0;
            foreach ($lines as $line) {
                if (empty($line['product_id'])) {
                    continue; // blank rows in the form
                }
                $product = Product::where('is_active', true)->findOrFail($line['product_id']);
                $qty = (int) $line['quantity'];
                $subtotal = round($product->unit_price * $qty, 2);

                $sale->items()->create([
                    'product_id' => $product->id, 'quantity' => $qty,
                    'unit_price' => $product->unit_price, 'subtotal' => $subtotal,
                ]);

                if ($product->consumesStock()) {
                    $this->inventory->move($product->item_id, $user, 'Sale', -$qty, 0, "Sale #{$sale->id}");
                }
                $total += $subtotal;
            }

            $sale->update(['total_amount' => $total]);

            return $sale;
        });
    }
}
