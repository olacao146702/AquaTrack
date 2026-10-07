@extends('layouts.app')
@section('title', 'Deliveries')
@section('content')
@if(auth()->user()->hasRole('owner','counter'))
<div class="card"><h3 style="margin-top:0">New delivery request</h3>
<form method="post" action="{{ route('deliveries.store') }}" class="grid">@csrf
  <div><label>Customer</label><select name="customer_id">@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->customer_name }}</option>@endforeach</select></div>
  <div><label>Product</label><select name="product_id">@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->product_name }}</option>@endforeach</select></div>
  <div><label>Quantity</label><input type="number" min="1" name="quantity" value="1" required></div>
  <div><label>Address</label><input name="delivery_address" required></div>
  <div><label>Preferred date</label><input type="date" name="preferred_date" value="{{ today()->toDateString() }}" required></div>
  <div><label>Preferred time</label><input type="time" name="preferred_time"></div>
  <div style="align-self:end"><button>Save request</button></div>
</form></div>
@endif
<div class="card"><h3 style="margin-top:0">Delivery requests</h3>
<form method="get" class="inline" style="margin-bottom:.6rem"><select name="status"><option value="">All statuses</option>
  @foreach(['Pending','Assigned','Out for Delivery','Delivered','Cancelled'] as $s)<option @selected(request('status') === $s)>{{ $s }}</option>@endforeach</select><button class="alt">Filter</button></form>
<table><tr><th>#</th><th>Customer</th><th>Order</th><th>When</th><th>Status</th><th>Action</th></tr>
@forelse($deliveries as $d)
<tr><td>{{ $d->id }}</td>
<td>{{ $d->customer->customer_name }}<br><small>{{ $d->delivery_address }}</small></td>
<td>{{ $d->quantity }} × {{ $d->product->product_name }}</td>
<td>{{ $d->preferred_date->format('M j') }} {{ $d->preferred_time }}</td>
<td><span class="badge">{{ $d->status }}</span>@if($d->rider)<br><small>{{ $d->rider->full_name }}</small>@endif</td>
<td>
@if(auth()->user()->role === 'owner' && $d->status === 'Pending')
  <form method="post" action="{{ route('deliveries.assign', $d) }}" class="inline">@csrf<select name="assigned_to">@foreach($riders as $r)<option value="{{ $r->id }}">{{ $r->full_name }}</option>@endforeach</select><button>Assign</button></form>
@endif
@if(auth()->user()->hasRole('owner','delivery') && in_array($d->status, ['Assigned','Out for Delivery']))
  <form method="post" action="{{ route('deliveries.status', $d) }}">@csrf
    <input type="hidden" name="status" value="{{ $d->status === 'Assigned' ? 'Out for Delivery' : 'Delivered' }}">
    <button>{{ $d->status === 'Assigned' ? 'Start delivery' : 'Mark delivered' }}</button></form>
@endif
</td></tr>
@empty<tr><td colspan="6"><small>No delivery requests.</small></td></tr>@endforelse</table>
{{ $deliveries->links() }}</div>
@endsection
