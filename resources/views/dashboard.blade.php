@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="grid">
  <div class="card stat"><span>Sales today</span><b>₱{{ number_format($today, 2) }}</b></div>
  <div class="card stat"><span>This week</span><b>₱{{ number_format($week, 2) }}</b></div>
  <div class="card stat"><span>This month</span><b>₱{{ number_format($month, 2) }}</b></div>
</div>
@if($lowStock->isNotEmpty())
<div class="flash err" role="alert">Low stock: {{ $lowStock->pluck('item_name')->join(', ') }}. <a href="{{ route('inventory.adjust') }}">Record a refill</a></div>
@endif
<div class="card">
  <div class="toolbar"><h3>Inventory</h3><a class="btn alt" href="{{ route('inventory.index') }}">Manage</a></div>
  <table><tr><th>Item</th><th>Filled</th><th>Empty</th><th>Low-stock level</th></tr>
  @forelse($inventory as $i)
    <tr><td>{{ $i->item_name }}</td><td class="{{ $i->isLow() ? 'low' : '' }}">{{ $i->filled_qty }}</td><td>{{ $i->empty_qty }}</td><td>{{ $i->low_stock_level }}</td></tr>
  @empty<tr><td colspan="4" class="empty">No inventory items yet.</td></tr>@endforelse</table>
</div>
<div class="card"><div class="toolbar"><h3>Recent sales</h3><a class="btn alt" href="{{ route('sales.index') }}">All sales</a></div>
  <table><tr><th>#</th><th>Customer</th><th>Total</th><th>Payment</th></tr>
  @forelse($recent as $s)
    <tr><td><a href="{{ route('sales.show', $s) }}">{{ $s->id }}</a></td><td>{{ $s->customer?->customer_name ?? 'Walk-in' }}</td><td>₱{{ number_format($s->total_amount, 2) }}</td><td>{{ $s->payment_status }}</td></tr>
  @empty<tr><td colspan="4" class="empty">No sales yet. <a href="{{ route('sales.create') }}">Record the first sale</a>.</td></tr>@endforelse</table>
</div>
<div class="card"><div class="toolbar"><h3>Deliveries by status</h3><a class="btn alt" href="{{ route('deliveries.index') }}">Open</a></div>
  @forelse($deliveries as $status => $n)<a href="{{ route('deliveries.index', ['status' => $status]) }}" class="badge">{{ $status }}: {{ $n }}</a> @empty<div class="empty">No delivery requests yet.</div>@endforelse
</div>
@endsection
