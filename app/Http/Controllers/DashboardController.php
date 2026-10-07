<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRequest;
use App\Models\InventoryItem;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $sum = fn ($from) => Sale::where('created_at', '>=', $from)->sum('total_amount');

        return view('dashboard', [
            'today'      => $sum(today()),
            'week'       => $sum(now()->startOfWeek()),
            'month'      => $sum(now()->startOfMonth()),
            'lowStock'   => InventoryItem::low()->orderBy('item_name')->get(),
            'inventory'  => InventoryItem::orderBy('item_name')->get(),
            'deliveries' => DeliveryRequest::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
        ]);
    }
}
