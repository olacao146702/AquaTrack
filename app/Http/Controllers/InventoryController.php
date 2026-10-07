<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    /** key => [movement type, label, filled sign, empty sign, roles allowed] */
    private const ACTIONS = [
        'refill'        => ['Refill',     'Refill (empty containers become filled)', 1, -1, ['owner', 'counter']],
        'return'        => ['Return',     'Customer return (empty containers back in)', 0, 1, ['owner', 'counter']],
        'add_filled'    => ['Adjustment', 'Adjustment: add filled stock', 1, 0, ['owner']],
        'remove_filled' => ['Adjustment', 'Adjustment: remove filled stock (damage or loss)', -1, 0, ['owner']],
        'add_empty'     => ['Adjustment', 'Adjustment: add empty stock', 0, 1, ['owner']],
        'remove_empty'  => ['Adjustment', 'Adjustment: remove empty stock', 0, -1, ['owner']],
    ];

    public function index()
    {
        return view('inventory.index', ['items' => InventoryItem::orderBy('item_name')->get()]);
    }

    public function movements(Request $request)
    {
        $itemId = $request->integer('item') ?: null;

        return view('inventory.movements', [
            'movements' => StockMovement::with('item', 'user')
                ->when($itemId, fn ($q) => $q->where('item_id', $itemId))
                ->latest()->latest('id')->paginate(25)->withQueryString(),
            'items'  => InventoryItem::orderBy('item_name')->get(['id', 'item_name']),
            'itemId' => $itemId,
        ]);
    }

    public function adjust(Request $request)
    {
        return view('inventory.adjust', [
            'items'   => InventoryItem::orderBy('item_name')->get(),
            'actions' => $this->actionsFor($request->user()),
            'itemId'  => $request->integer('item') ?: null,
        ]);
    }

    public function storeAdjustment(Request $request, InventoryService $inventory)
    {
        $actions = $this->actionsFor($request->user());

        $data = $request->validate([
            'item_id'  => ['required', 'exists:inventory_items,id'],
            'action'   => ['required', Rule::in(array_keys($actions))],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'remarks'  => ['nullable', 'string', 'max:255', Rule::requiredIf(fn () => (self::ACTIONS[$request->input('action')][0] ?? null) === 'Adjustment')],
        ], [
            'item_id.required' => 'Choose an inventory item.',
            'action.*'         => 'Choose what you want to record.',
            'quantity.*'       => 'Quantity must be a whole number from 1 to 100,000.',
            'remarks.required' => 'Explain the reason for the adjustment.',
        ]);

        [$type, , $filledSign, $emptySign] = self::ACTIONS[$data['action']];
        $q = (int) $data['quantity'];

        $item = $inventory->move($data['item_id'], $request->user(), $type, $filledSign * $q, $emptySign * $q, $data['remarks'] ?? null);

        return redirect()->route('inventory.index')
            ->with('ok', "{$item->item_name}: now {$item->filled_qty} filled and {$item->empty_qty} empty.");
    }

    private function actionsFor(User $user): array
    {
        $out = [];
        foreach (self::ACTIONS as $key => [, $label, , , $roles]) {
            if (in_array($user->role, $roles, true)) {
                $out[$key] = $label;
            }
        }

        return $out;
    }
}
