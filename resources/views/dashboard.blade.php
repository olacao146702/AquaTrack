@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="grid">
  <div class="card stat"><span>Sales today</span><b>₱{{ number_format($today, 2) }}</b></div>
  <div class="card stat"><span>This week</span><b>₱{{ number_format($week, 2) }}</b></div>
  <div class="card stat"><span>This month</span><b>₱{{ number_format($month, 2) }}</b></div>
</div>
<div class="card">
  <h3 style="margin-top:0">Inventory</h3>
  <table><tr><th>Item</th><th>Filled</th><th>Empty</th><th>Low-stock level</th></tr>
  @foreach($inventory as $i)
    <tr><td>{{ $i->item_name }}</td><td class="{{ $i->filled_qty <= $i->low_stock_level ? 'low' : '' }}">{{ $i->filled_qty }}</td><td>{{ $i->empty_qty }}</td><td>{{ $i->low_stock_level }}</td></tr>
  @endforeach</table>
  @if($lowStock->isNotEmpty())<p class="low">Low stock: {{ $lowStock->pluck('item_name')->join(', ') }}</p>@endif
</div>
<div class="card"><h3 style="margin-top:0">Deliveries by status</h3>
  @forelse($deliveries as $status => $n)<span class="badge">{{ $status }}: {{ $n }}</span> @empty<small>No delivery requests yet.</small>@endforelse
</div>
@endsection
