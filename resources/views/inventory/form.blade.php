@extends('layouts.app')
@section('title', $item->exists ? 'Edit item' : 'Add item')
@section('content')
<div class="card" style="max-width:560px">
  <h2 style="margin-top:0">{{ $item->exists ? 'Edit inventory item' : 'Add inventory item' }}</h2>
  <form method="post" action="{{ $item->exists ? route('items.update', $item) : route('items.store') }}">@csrf @if($item->exists) @method('PUT') @endif
    <label for="item_name">Item name</label><input id="item_name" name="item_name" value="{{ old('item_name', $item->item_name) }}" maxlength="100" required>
    <label for="item_type">Type</label><select id="item_type" name="item_type">@foreach(\App\Models\InventoryItem::TYPES as $t)<option @selected(old('item_type', $item->item_type) === $t)>{{ $t }}</option>@endforeach</select>
    <label for="low_stock_level">Low-stock level</label><input id="low_stock_level" type="number" min="0" max="100000" name="low_stock_level" value="{{ old('low_stock_level', $item->low_stock_level) }}" required>
    <small>A warning shows when filled stock is at or below this number.</small>
    @unless($item->exists)
      <div class="cols"><div><label for="filled_qty">Opening filled stock</label><input id="filled_qty" type="number" min="0" max="100000" name="filled_qty" value="{{ old('filled_qty', 0) }}" required></div>
      <div><label for="empty_qty">Opening empty stock</label><input id="empty_qty" type="number" min="0" max="100000" name="empty_qty" value="{{ old('empty_qty', 0) }}" required></div></div>
    @else
      <p><small>Current stock: {{ $item->filled_qty }} filled, {{ $item->empty_qty }} empty. To change it, <a href="{{ route('inventory.adjust', ['item' => $item->id]) }}">record a stock movement</a> so there is a history.</small></p>
    @endunless
    <p class="actions"><button>Save item</button><a class="btn alt" href="{{ route('inventory.index') }}">Cancel</a></p>
  </form>
</div>
@endsection
