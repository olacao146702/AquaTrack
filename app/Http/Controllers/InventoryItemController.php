<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryItemRequest;
use App\Models\InventoryItem;
use App\Models\DeliveryRequest;
use App\Models\Product;
use App\Models\SaleItem;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class InventoryItemController extends Controller
{
    public function create()
    {
        return view('inventory.form', ['item' => new InventoryItem(['item_type' => 'Container', 'low_stock_level' => 5, 'filled_qty' => 0, 'empty_qty' => 0])]);
    }

    public function store(InventoryItemRequest $request, InventoryService $inventory)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $inventory) {
            $item = InventoryItem::create([
                'item_name' => $data['item_name'], 'item_type' => $data['item_type'],
                'low_stock_level' => $data['low_stock_level'], 'filled_qty' => 0, 'empty_qty' => 0,
            ]);

            // Opening stock goes through the movement log so the history adds up from day one.
            if ($data['filled_qty'] || $data['empty_qty']) {
                $inventory->move($item->id, $request->user(), 'Adjustment', (int) $data['filled_qty'], (int) $data['empty_qty'], 'Opening stock');
            }
        });

        return redirect()->route('inventory.index')->with('ok', 'Inventory item added.');
    }

    public function edit(InventoryItem $item)
    {
        return view('inventory.form', compact('item'));
    }

    public function update(InventoryItemRequest $request, InventoryItem $item)
    {
        // Stock quantities are never edited directly; use a stock movement so there is a record.
        $item->update($request->safe()->only(['item_name', 'item_type', 'low_stock_level']));

        return redirect()->route('inventory.index')->with('ok', 'Inventory item updated.');
    }

    public function destroy(InventoryItem $item)
    {
        $productIds = $item->products()->pluck('id');

        // Items whose products were already sold or delivered must stay, or old receipts would break.
        if (SaleItem::whereIn('product_id', $productIds)->exists() || DeliveryRequest::whereIn('product_id', $productIds)->exists()) {
            return back()->withErrors(['item' => "{$item->item_name} has sales or deliveries on record and cannot be deleted. Deactivate its products instead."]);
        }

        DB::transaction(function () use ($item, $productIds) {
            Product::whereIn('id', $productIds)->delete();
            $item->movements()->delete();
            $item->delete();
        });

        return redirect()->route('inventory.index')->with('ok', "{$item->item_name} was deleted.");
    }
}
