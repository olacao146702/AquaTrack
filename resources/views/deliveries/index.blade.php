@extends('layouts.app')
@section('title', 'Deliveries')
@section('content')
@if(auth()->user()->hasRole('owner','counter'))
<div class="card"><h3 style="margin-top:0">New delivery request</h3>
@if($customers->isEmpty() || $products->isEmpty())
  <div class="empty">@if($customers->isEmpty())Add a <a href="{{ route('customers.create') }}">customer</a> first. @endif @if($products->isEmpty())No active products are available. @endif</div>
@else
<form method="post" action="{{ route('deliveries.store') }}" class="grid">@csrf
  <div><label for="d_customer">Customer</label><select id="d_customer" name="customer_id">@foreach($customers as $c)<option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->customer_name }}</option>@endforeach</select></div>
  <div><label for="d_product">Product</label><select id="d_product" name="product_id">@foreach($products as $p)<option value="{{ $p->id }}" @selected(old('product_id') == $p->id)>{{ $p->product_name }}</option>@endforeach</select></div>
  <div><label for="d_qty">Quantity</label><input id="d_qty" type="number" min="1" max="500" name="quantity" value="{{ old('quantity', 1) }}" required></div>
  <div><label for="d_addr">Address</label><input id="d_addr" name="delivery_address" value="{{ old('delivery_address') }}" maxlength="255" required></div>
  <div><label for="d_date">Preferred date</label><input id="d_date" type="date" name="preferred_date" min="{{ today()->toDateString() }}" value="{{ old('preferred_date', today()->toDateString()) }}" required></div>
  <div><label for="d_time">Preferred time</label><input id="d_time" type="time" name="preferred_time" value="{{ old('preferred_time') }}"></div>
  <div><label for="d_rem">Remarks <small>(optional)</small></label><input id="d_rem" name="remarks" value="{{ old('remarks') }}" maxlength="255"></div>
  <div style="align-self:end"><button>Save request</button></div>
</form>
@endif
</div>
@endif
<div class="card"><h3 style="margin-top:0">Delivery requests</h3>
<form method="get" class="inline" style="margin-bottom:.6rem"><select name="status" aria-label="Status"><option value="">All statuses</option>
  @foreach(['Pending','Assigned','Out for Delivery','Delivered','Cancelled'] as $s)<option @selected(request('status') === $s)>{{ $s }}</option>@endforeach</select><button class="alt">Filter</button></form>
<table><tr><th>#</th><th>Customer</th><th>Order</th><th>When</th><th>Status</th><th>Action</th></tr>
@forelse($deliveries as $d)
<tr><td>{{ $d->id }}</td>
<td>{{ $d->customer->customer_name }}<br><small>{{ $d->delivery_address }}</small>@if($d->remarks)<br><small>Note: {{ $d->remarks }}</small>@endif</td>
<td>{{ $d->quantity }} × {{ $d->product->product_name }}</td>
<td>{{ $d->preferred_date->format('M j') }} {{ \Illuminate\Support\Str::limit((string) $d->preferred_time, 5, '') }}</td>
<td><span class="badge {{ $d->status === 'Delivered' ? 'good' : ($d->status === 'Cancelled' ? 'warn' : '') }}">{{ $d->status }}</span>@if($d->rider)<br><small>{{ $d->rider->full_name }}</small>@endif
@if($d->sale_id && auth()->user()->hasRole('owner','counter'))<br><small><a href="{{ route('sales.show', $d->sale_id) }}">Sale #{{ $d->sale_id }}</a></small>@endif</td>
<td><div class="actions">
@if(auth()->user()->role === 'owner' && $d->status === 'Pending')
  @if($riders->isEmpty())<small>No active riders. <a href="{{ route('users.create') }}">Add one</a>.</small>@else
  <form method="post" action="{{ route('deliveries.assign', $d) }}" class="inline">@csrf<select name="assigned_to" aria-label="Rider">@foreach($riders as $r)<option value="{{ $r->id }}">{{ $r->full_name }}</option>@endforeach</select><button>Assign</button></form>@endif
@endif
@if(auth()->user()->hasRole('owner','delivery') && in_array($d->status, ['Assigned','Out for Delivery']))
  <form method="post" action="{{ route('deliveries.status', $d) }}">@csrf
    <input type="hidden" name="status" value="{{ $d->status === 'Assigned' ? 'Out for Delivery' : 'Delivered' }}">
    <button>{{ $d->status === 'Assigned' ? 'Start delivery' : 'Mark delivered' }}</button></form>
@endif
@if(auth()->user()->role === 'owner' && in_array($d->status, ['Pending','Assigned']))
  <form method="post" action="{{ route('deliveries.status', $d) }}" onsubmit="return confirm('Cancel delivery #{{ $d->id }}?')">@csrf
    <input type="hidden" name="status" value="Cancelled"><button class="danger">Cancel</button></form>
@endif
</div></td></tr>
@empty<tr><td colspan="6" class="empty">{{ request('status') ? 'No deliveries with this status.' : (auth()->user()->role === 'delivery' ? 'No deliveries are assigned to you right now.' : 'No delivery requests yet.') }}</td></tr>@endforelse</table>
{{ $deliveries->links() }}</div>
@endsection
