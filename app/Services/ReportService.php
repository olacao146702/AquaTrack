<?php

namespace App\Services;

use App\Models\DeliveryRequest;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\CarbonInterface;

class ReportService
{
    /** @return array{count:int,total:float,unpaid:float,average:float,daily:\Illuminate\Support\Collection,products:\Illuminate\Support\Collection,deliveries:\Illuminate\Support\Collection} */
    public function summary(CarbonInterface $from, CarbonInterface $to): array
    {
        $start = $from->copy()->startOfDay();
        $end = $to->copy()->endOfDay();

        $totals = Sale::whereBetween('created_at', [$start, $end])
            ->selectRaw("count(*) as n, coalesce(sum(total_amount), 0) as total, coalesce(sum(case when payment_status = 'Unpaid' then total_amount else 0 end), 0) as unpaid")
            ->first();

        $daily = Sale::whereBetween('created_at', [$start, $end])
            ->selectRaw('date(created_at) as day, count(*) as n, sum(total_amount) as total')
            ->groupBy('day')->orderBy('day')->get();

        $products = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->selectRaw('products.product_name as name, sum(sale_items.quantity) as qty, sum(sale_items.subtotal) as total')
            ->groupBy('products.id', 'products.product_name')->orderByDesc('total')->get();

        $deliveries = DeliveryRequest::whereBetween('created_at', [$start, $end])
            ->selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');

        $count = (int) $totals->n;

        return [
            'count'      => $count,
            'total'      => (float) $totals->total,
            'unpaid'     => (float) $totals->unpaid,
            'average'    => $count ? round($totals->total / $count, 2) : 0.0,
            'daily'      => $daily,
            'products'   => $products,
            'deliveries' => $deliveries,
        ];
    }
}
