<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $v = Validator::make($request->query(), [
            'from'    => ['nullable', 'date_format:Y-m-d'],
            'to'      => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'type'    => ['nullable', 'in:Walk-in,Delivery'],
            'payment' => ['nullable', 'in:Paid,Unpaid'],
        ], ['to.after_or_equal' => 'The end date cannot be before the start date.', '*.date_format' => 'Use the YYYY-MM-DD date format.']);

        // Bad filter values are reported and ignored, never turned into an error page.
        $f = $v->fails() ? [] : $v->validated();

        $sales = Sale::with('customer', 'cashier')
            ->when($f['from'] ?? null, fn ($q, $d) => $q->where('created_at', '>=', $d.' 00:00:00'))
            ->when($f['to'] ?? null, fn ($q, $d) => $q->where('created_at', '<=', $d.' 23:59:59'))
            ->when($f['type'] ?? null, fn ($q, $t) => $q->where('sale_type', $t))
            ->when($f['payment'] ?? null, fn ($q, $p) => $q->where('payment_status', $p))
            ->latest()->latest('id')->paginate(20)->withQueryString();

        return view('sales.index', ['sales' => $sales, 'f' => $f, 'problems' => $v->errors()->all()]);
    }

    public function show(Sale $sale)
    {
        return view('sales.show', ['sale' => $sale->load('items.product', 'customer', 'cashier')]);
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

        return redirect()->route('sales.show', $sale)
            ->with('ok', "Sale #{$sale->id} recorded (₱".number_format($sale->total_amount, 2).').');
    }

    public function markPaid(Sale $sale)
    {
        if ($sale->payment_status === 'Paid') {
            return back()->with('ok', "Sale #{$sale->id} was already marked paid.");
        }
        $sale->update(['payment_status' => 'Paid']);

        return back()->with('ok', "Sale #{$sale->id} marked as paid.");
    }
}
