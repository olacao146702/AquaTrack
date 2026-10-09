@extends('layouts.app')
@section('title', 'Sale #'.$sale->id)
@section('content')
<div class="card">
  <div class="toolbar"><h2>Sale #{{ $sale->id }}</h2><div class="actions"><button type="button" class="alt" onclick="window.print()">Print</button><a class="btn alt" href="{{ route('sales.index') }}">Back to sales</a></div></div>
  <div class="cols">
    <p><small>Date</small><br>{{ $sale->created_at->format('M j, Y g:i A') }}</p>
    <p><small>Customer</small><br>{{ $sale->customer?->customer_name ?? 'Walk-in' }}</p>
    <p><small>Type</small><br>{{ $sale->sale_type }}</p>
    <p><small>Recorded by</small><br>{{ $sale->cashier->full_name }}</p>
  </div>
  <table><tr><th>Product</th><th class="right">Qty</th><th class="right">Unit price</th><th class="right">Subtotal</th></tr>
  @foreach($sale->items as $i)
    <tr><td>{{ $i->product->product_name }}</td><td class="right">{{ $i->quantity }}</td><td class="right">₱{{ number_format($i->unit_price, 2) }}</td><td class="right">₱{{ number_format($i->subtotal, 2) }}</td></tr>
  @endforeach
    <tr><td colspan="3" class="right"><b>Total</b></td><td class="right"><b>₱{{ number_format($sale->total_amount, 2) }}</b></td></tr></table>
  <div class="toolbar" style="margin-top:.8rem"><span class="badge {{ $sale->payment_status === 'Unpaid' ? 'warn' : 'good' }}">{{ $sale->payment_status }}</span>
  @if($sale->payment_status === 'Unpaid')
    <form method="post" action="{{ route('sales.paid', $sale) }}">@csrf<button>Mark as paid</button></form>
  @endif</div>
</div>
@endsection
