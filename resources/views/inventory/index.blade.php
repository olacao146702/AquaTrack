@extends('layouts.app')
@section('title', 'Inventory')
@section('content')
<div class="card">
  <div class="toolbar"><h2>Inventory</h2>
    <div class="actions"><a class="btn" href="{{ route('inventory.adjust') }}">Record stock movement</a><a class="btn alt" href="{{ route('inventory.movements') }}">History</a>
    @if(auth()->user()->role === 'owner')<a class="btn alt" href="{{ route('items.create') }}">Add item</a>@endif</div></div>
  <table><tr><th>Item</th><th>Type</th><th class="right">Filled</th><th class="right">Empty</th><th class="right">Low-stock level</th><th>Status</th><th></th></tr>
  @forelse($items as $i)
    <tr><td>{{ $i->item_name }}</td><td>{{ $i->item_type }}</td><td class="right {{ $i->isLow() ? 'low' : '' }}">{{ $i->filled_qty }}</td><td class="right">{{ $i->empty_qty }}</td><td class="right">{{ $i->low_stock_level }}</td>
    <td><span class="badge {{ $i->isLow() ? 'warn' : 'good' }}">{{ $i->isLow() ? 'Low stock' : 'OK' }}</span></td>
    <td><div class="actions"><a class="btn alt" href="{{ route('inventory.adjust', ['item' => $i->id]) }}">Move stock</a>
    @if(auth()->user()->role === 'owner')<a class="btn alt" href="{{ route('items.edit', $i) }}">Edit</a>@endif</div></td></tr>
  @empty<tr><td colspan="7" class="empty">No inventory items yet.@if(auth()->user()->role === 'owner') <a href="{{ route('items.create') }}">Add the first item</a>.@endif</td></tr>@endforelse</table>
</div>
@endsection
