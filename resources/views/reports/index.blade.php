@extends('layouts.app')
@section('title', 'Reports')
@section('content')
@if(!empty($problems))<div class="flash err" role="alert">@foreach($problems as $p)<div>{{ $p }}</div>@endforeach<div>Showing this month instead.</div></div>@endif
<div class="card">
  <div class="toolbar"><h2>Sales report</h2>
    <a class="btn alt" href="{{ route('reports.export', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}">Download CSV</a></div>
  <form method="get" class="inline">
    <label for="from" style="margin:0">From</label><input id="from" type="date" name="from" value="{{ $from->toDateString() }}" required>
    <label for="to" style="margin:0">To</label><input id="to" type="date" name="to" value="{{ $to->toDateString() }}" required>
    <button>Show report</button>
    <a href="{{ route('reports.index', ['from' => today()->toDateString(), 'to' => today()->toDateString()]) }}">Today</a>
    <a href="{{ route('reports.index', ['from' => now()->startOfWeek()->toDateString(), 'to' => today()->toDateString()]) }}">This week</a>
    <a href="{{ route('reports.index') }}">This month</a>
  </form>
</div>
<div class="grid">
  <div class="card stat"><span>Total sales</span><b>₱{{ number_format($r['total'], 2) }}</b></div>
  <div class="card stat"><span>Transactions</span><b>{{ number_format($r['count']) }}</b></div>
  <div class="card stat"><span>Average sale</span><b>₱{{ number_format($r['average'], 2) }}</b></div>
  <div class="card stat"><span>Unpaid</span><b class="{{ $r['unpaid'] > 0 ? 'low' : '' }}">₱{{ number_format($r['unpaid'], 2) }}</b></div>
</div>
<div class="card"><h3 style="margin-top:0">Sales per day</h3>
  <table><tr><th>Date</th><th class="right">Sales</th><th class="right">Total</th></tr>
  @forelse($r['daily'] as $d)<tr><td>{{ \Carbon\Carbon::parse($d->day)->format('D, M j, Y') }}</td><td class="right">{{ $d->n }}</td><td class="right">₱{{ number_format($d->total, 2) }}</td></tr>
  @empty<tr><td colspan="3" class="empty">No sales in this period.</td></tr>@endforelse</table></div>
<div class="card"><h3 style="margin-top:0">By product</h3>
  <table><tr><th>Product</th><th class="right">Quantity</th><th class="right">Total</th></tr>
  @forelse($r['products'] as $p)<tr><td>{{ $p->name }}</td><td class="right">{{ number_format($p->qty) }}</td><td class="right">₱{{ number_format($p->total, 2) }}</td></tr>
  @empty<tr><td colspan="3" class="empty">No products sold in this period.</td></tr>@endforelse</table></div>
<div class="card"><h3 style="margin-top:0">Delivery requests in this period</h3>
  @forelse($r['deliveries'] as $status => $n)<span class="badge">{{ $status }}: {{ $n }}</span> @empty<div class="empty">No delivery requests in this period.</div>@endforelse</div>
@endsection
