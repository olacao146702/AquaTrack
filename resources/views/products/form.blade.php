@extends('layouts.app')
@section('title', $product->exists ? 'Edit product' : 'Add product')
@section('content')
<div class="card" style="max-width:560px">
  <h2 style="margin-top:0">{{ $product->exists ? 'Edit product' : 'Add product' }}</h2>
  <form method="post" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">@csrf @if($product->exists) @method('PUT') @endif
    <label for="product_name">Product name</label><input id="product_name" name="product_name" value="{{ old('product_name', $product->product_name) }}" maxlength="100" required>
    <label for="category">Category</label><select id="category" name="category">@foreach(\App\Models\Product::CATEGORIES as $c)<option @selected(old('category', $product->category) === $c)>{{ $c }}</option>@endforeach</select>
    <label for="item_id">Stock item <small>(required unless the category is Refill)</small></label>
    <select id="item_id" name="item_id"><option value="">None</option>@foreach($items as $i)<option value="{{ $i->id }}" @selected(old('item_id', $product->item_id) == $i->id)>{{ $i->item_name }}</option>@endforeach</select>
    @if($items->isEmpty())<small>No inventory items yet. <a href="{{ route('items.create') }}">Add one</a> first.</small>@endif
    <label for="unit_price">Unit price (₱)</label><input id="unit_price" type="number" step="0.01" min="0.01" max="99999.99" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" required>
    <input type="hidden" name="is_active" value="0">
    <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ? '1' : '0') == '1')> Active (can be sold)</label>
    <p class="actions"><button>Save product</button><a class="btn alt" href="{{ route('products.index') }}">Cancel</a></p>
  </form>
</div>
@endsection
