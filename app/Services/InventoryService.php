<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /** Apply a stock change under a row lock and log it. Rejects changes that would go negative. */
    public function move(int $itemId, User $user, string $type, int $filled, int $empty = 0, ?string $remarks = null): InventoryItem
    {
        return DB::transaction(function () use ($itemId, $user, $type, $filled, $empty, $remarks) {
            $item = InventoryItem::lockForUpdate()->findOrFail($itemId);

            if ($item->filled_qty + $filled < 0 || $item->empty_qty + $empty < 0) {
                throw ValidationException::withMessages([
                    'stock' => "Not enough stock for {$item->item_name}.",
                ]);
            }

            $item->filled_qty += $filled;
            $item->empty_qty += $empty;
            $item->save();

            StockMovement::create([
                'item_id' => $item->id, 'user_id' => $user->id, 'movement_type' => $type,
                'filled_change' => $filled, 'empty_change' => $empty, 'remarks' => $remarks,
            ]);

            return $item;
        });
    }
}
