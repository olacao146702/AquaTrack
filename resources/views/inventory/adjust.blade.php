@extends('layouts.app')
@section('title', 'Stock movement')
@section('content')
<div class="card" style="max-width:560px">
  <h2 style="margin-top:0">Record stock movement</h2>
  @if($items->isEmpty())
    <div class="empty">There are no inventory items yet.@if(auth()->user()->role === 'owner') <a href="{{ route('items.create') }}">Add one</a>.@endif</div>
  @else
  <form method="post" action="{{ route('inventory.adjust.store') }}">@csrf
    <label for="item_id">Item</label>
    <select id="item_id" name="item_id">@foreach($items as $i)<option value="{{ $i->id }}" @selected(old('item_id', $itemId) == $i->id)>{{ $i->item_name }} ({{ $i->filled_qty }} filled / {{ $i->empty_qty }} empty)</option>@endforeach</select>
    <label for="action">What happened?</label>
    <select id="action" name="action">@foreach($actions as $key => $label)<option value="{{ $key }}" @selected(old('action') === $key)>{{ $label }}</option>@endforeach</select>
    <label for="quantity">Quantity</label><input id="quantity" type="number" min="1" max="100000" name="quantity" value="{{ old('quantity') }}" required>
    <label for="remarks">Remarks <small>(required for adjustments)</small></label><input id="remarks" name="remarks" value="{{ old('remarks') }}" maxlength="255">
    <p class="actions"><button>Save movement</button><a class="btn alt" href="{{ route('inventory.index') }}">Cancel</a></p>
  </form>
  @endif
</div>
@endsection
