<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;

class SaleController extends Controller
{
    public function index()
    {
        return view('sales.index', [
            'sales' => Sale::with('customer', 'cashier')->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('sales.create', [
            'products'  => Product::where('is_active', true)->orderBy('product_name')->get(),
            'customers' => Customer::orderBy('customer_name')->get(),
        ]);
    }

    public function store(StoreSaleRequest $request, SaleService $sales)
    {
        $sale = $sales->record(
            $request->user(), 'Walk-in', $request->integer('customer_id') ?: null,
            $request->validated('lines'), $request->validated('payment_status'),
        );

        return redirect()->route('sales.index')
            ->with('ok', "Sale #{$sale->id} recorded (₱".number_format($sale->total_amount, 2).').');
    }
}
