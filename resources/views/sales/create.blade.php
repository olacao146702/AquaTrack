@extends('layouts.app')
@section('title', 'New sale')
@section('content')
<div class="card">
  <h2 style="margin-top:0">Record walk-in sale</h2>
  @if($products->isEmpty())
    <div class="empty">There are no active products to sell. @if(auth()->user()->role === 'owner')<a href="{{ route('products.create') }}">Add a product</a>.@else Ask the owner to add one.@endif</div>
  @else
  <form method="post" action="{{ route('sales.store') }}">@csrf
    <label for="customer_id">Customer <small>(optional)</small></label>
    <select id="customer_id" name="customer_id"><option value="">Walk-in (no record)</option>
      @foreach($customers as $c)<option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->customer_name }}</option>@endforeach</select>
    <small><a href="{{ route('customers.create') }}">Add a new customer</a></small>
    <table style="margin-top:.8rem"><tr><th>Product</th><th style="width:110px">Qty</th></tr>
    @for($n = 0; $n < 3; $n++)
      <tr><td><select name="lines[{{ $n }}][product_id]" aria-label="Product {{ $n + 1 }}">@if($n)<option value="">-</option>@endif
        @foreach($products as $p)<option value="{{ $p->id }}" @selected(old("lines.$n.product_id") == $p->id)>{{ $p->product_name }} - ₱{{ number_format($p->unit_price, 2) }}</option>@endforeach</select></td>
      <td><input type="number" min="1" max="500" step="1" name="lines[{{ $n }}][quantity]" aria-label="Quantity {{ $n + 1 }}" value="{{ old("lines.$n.quantity", $n ? '' : 1) }}" @if(!$n)required @endif></td></tr>
    @endfor</table>
    <label for="payment_status">Payment</label>
    <select id="payment_status" name="payment_status"><option @selected(old('payment_status') === 'Paid')>Paid</option><option @selected(old('payment_status') === 'Unpaid')>Unpaid</option></select>
    <p><button>Save sale</button> <small>Totals are computed from current prices on save.</small></p>
  </form>
  @endif
</div>
@endsection
