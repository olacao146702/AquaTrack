@extends('layouts.app')
@section('title', 'Sales')
@section('content')
@if(!empty($problems))<div class="flash err" role="alert">@foreach($problems as $p)<div>{{ $p }}</div>@endforeach</div>@endif
<div class="card">
  <div class="toolbar"><h2>Sales</h2><a class="btn" href="{{ route('sales.create') }}">New sale</a></div>
  <form method="get" class="inline" style="margin-bottom:.8rem">
    <input type="date" name="from" value="{{ $f['from'] ?? '' }}" aria-label="From date">
    <input type="date" name="to" value="{{ $f['to'] ?? '' }}" aria-label="To date">
    <select name="type" aria-label="Type"><option value="">All types</option>@foreach(['Walk-in', 'Delivery'] as $t)<option @selected(($f['type'] ?? '') === $t)>{{ $t }}</option>@endforeach</select>
    <select name="payment" aria-label="Payment"><option value="">Any payment</option>@foreach(['Paid', 'Unpaid'] as $t)<option @selected(($f['payment'] ?? '') === $t)>{{ $t }}</option>@endforeach</select>
    <button class="alt">Filter</button>@if(!empty($f))<a href="{{ route('sales.index') }}">Clear</a>@endif
  </form>
  <table><tr><th>#</th><th>Date</th><th>Customer</th><th>Type</th><th>Recorded by</th><th>Total</th><th>Payment</th></tr>
  @forelse($sales as $s)
    <tr><td><a href="{{ route('sales.show', $s) }}">{{ $s->id }}</a></td><td>{{ $s->created_at->format('M j, g:i A') }}</td><td>{{ $s->customer?->customer_name ?? 'Walk-in' }}</td><td>{{ $s->sale_type }}</td><td>{{ $s->cashier->full_name }}</td><td>₱{{ number_format($s->total_amount, 2) }}</td>
    <td><span class="badge {{ $s->payment_status === 'Unpaid' ? 'warn' : 'good' }}">{{ $s->payment_status }}</span></td></tr>
  @empty<tr><td colspan="7" class="empty">{{ empty($f) ? 'No sales recorded yet.' : 'No sales match these filters.' }}</td></tr>@endforelse</table>
  {{ $sales->links() }}
</div>
@endsection
