@extends('layouts.app')
@section('title', 'Products')
@section('content')
<div class="card">
  <div class="toolbar"><h2>Products</h2><a class="btn" href="{{ route('products.create') }}">Add product</a></div>
  <table><tr><th>Product</th><th>Category</th><th>Stock item</th><th class="right">Price</th><th>Status</th><th></th></tr>
  @forelse($products as $p)
    <tr><td>{{ $p->product_name }}</td><td>{{ $p->category }}</td><td>{{ $p->item?->item_name ?? '-' }}</td><td class="right">₱{{ number_format($p->unit_price, 2) }}</td>
    <td><span class="badge {{ $p->is_active ? 'good' : 'warn' }}">{{ $p->is_active ? 'Active' : 'Inactive' }}</span></td>
    <td><a class="btn alt" href="{{ route('products.edit', $p) }}">Edit</a></td></tr>
  @empty<tr><td colspan="6" class="empty">No products yet. Add one so it can be sold.</td></tr>@endforelse</table>
  {{ $products->links() }}
  <p><small>Products are deactivated instead of deleted, so past sales keep their history.</small></p>
</div>
@endsection
