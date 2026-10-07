@extends('layouts.app')
@section('title', 'New sale')
@section('content')
<div class="card">
  <h2 style="margin-top:0">Record walk-in sale</h2>
  <form method="post" action="{{ route('sales.store') }}">@csrf
    <label for="customer_id">Customer <small>(optional)</small></label>
    <select id="customer_id" name="customer_id"><option value="">Walk-in (no record)</option>
      @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->customer_name }}</option>@endforeach</select>
    <table style="margin-top:.8rem"><tr><th>Product</th><th style="width:110px">Qty</th></tr>
    @for($n = 0; $n < 3; $n++)
      <tr><td><select name="lines[{{ $n }}][product_id]">@if($n)<option value="">-</option>@endif
        @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->product_name }} - ₱{{ number_format($p->unit_price, 2) }}</option>@endforeach</select></td>
      <td><input type="number" min="1" name="lines[{{ $n }}][quantity]" @if(!$n)required value="1"@endif></td></tr>
    @endfor</table>
    <label for="payment_status">Payment</label>
    <select id="payment_status" name="payment_status"><option>Paid</option><option>Unpaid</option></select>
    <p><button>Save sale</button> <small>Totals are computed from current prices on save.</small></p>
  </form>
</div>
@endsection
