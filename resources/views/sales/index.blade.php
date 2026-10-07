@extends('layouts.app')
@section('title', 'Sales')
@section('content')
<div class="card"><h2 style="margin-top:0">Sales</h2>
<table><tr><th>#</th><th>Date</th><th>Customer</th><th>Type</th><th>Recorded by</th><th>Total</th><th>Payment</th></tr>
@forelse($sales as $s)
  <tr><td>{{ $s->id }}</td><td>{{ $s->created_at->format('M j, g:i A') }}</td><td>{{ $s->customer?->customer_name ?? 'Walk-in' }}</td><td>{{ $s->sale_type }}</td><td>{{ $s->cashier->full_name }}</td><td>₱{{ number_format($s->total_amount, 2) }}</td><td>{{ $s->payment_status }}</td></tr>
@empty<tr><td colspan="7"><small>No sales recorded yet.</small></td></tr>@endforelse</table>
{{ $sales->links() }}</div>
@endsection
