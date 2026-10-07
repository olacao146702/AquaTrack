@extends('layouts.app')
@section('title', 'Stock history')
@section('content')
<div class="card">
  <div class="toolbar"><h2>Stock history</h2><a class="btn alt" href="{{ route('inventory.index') }}">Back to inventory</a></div>
  <form method="get" class="inline" style="margin-bottom:.8rem"><select name="item" aria-label="Item"><option value="">All items</option>@foreach($items as $i)<option value="{{ $i->id }}" @selected($itemId === $i->id)>{{ $i->item_name }}</option>@endforeach</select><button class="alt">Filter</button></form>
  <table><tr><th>Date</th><th>Item</th><th>Type</th><th class="right">Filled</th><th class="right">Empty</th><th>By</th><th>Remarks</th></tr>
  @forelse($movements as $m)
    <tr><td>{{ $m->created_at->format('M j, g:i A') }}</td><td>{{ $m->item->item_name }}</td><td>{{ $m->movement_type }}</td>
    <td class="right">{{ $m->filled_change > 0 ? '+' : '' }}{{ $m->filled_change }}</td><td class="right">{{ $m->empty_change > 0 ? '+' : '' }}{{ $m->empty_change }}</td><td>{{ $m->user->full_name }}</td><td>{{ $m->remarks ?: '-' }}</td></tr>
  @empty<tr><td colspan="7" class="empty">No stock movements recorded yet.</td></tr>@endforelse</table>
  {{ $movements->links() }}
</div>
@endsection
