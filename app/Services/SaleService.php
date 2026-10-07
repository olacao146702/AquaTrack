<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function __construct(private InventoryService $inventory) {}

    /**
     * @param array<int, array{product_id:mixed, quantity:mixed}> $lines
     */
    public function record(User $user, string $type, ?int $customerId, array $lines, string $payment = 'Paid'): Sale
    {
        // Blank rows are dropped and repeated products are combined, so stock is checked against the real total.
        $wanted = [];
        foreach ($lines as $line) {
            if (empty($line['product_id'])) {
                continue;
            }
            $wanted[(int) $line['product_id']] = ($wanted[(int) $line['product_id']] ?? 0) + (int) $line['quantity'];
        }
        if ($wanted === []) {
            throw ValidationException::withMessages(['lines' => 'Add at least one product.']);
        }

        return DB::transaction(function () use ($user, $type, $customerId, $wanted, $payment) {
            $sale = Sale::create([
                'customer_id' => $customerId, 'user_id' => $user->id,
                'sale_type' => $type, 'payment_status' => $payment,
            ]);

            $total = 0;
            foreach ($wanted as $productId => $qty) {
                $product = Product::where('is_active', true)->find($productId);
                if (! $product) {
                    throw ValidationException::withMessages(['lines' => 'A selected product is no longer available.']);
                }
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
