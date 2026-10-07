@extends('layouts.app')
@section('title', 'Customers')
@section('content')
<div class="card">
  <div class="toolbar"><h2>Customers</h2><a class="btn" href="{{ route('customers.create') }}">Add customer</a></div>
  <form method="get" class="inline" style="margin-bottom:.8rem">
    <input type="search" name="q" value="{{ $term }}" placeholder="Search name or number" aria-label="Search customers" maxlength="100">
    <button class="alt">Search</button>@if($term !== '')<a href="{{ route('customers.index') }}">Clear</a>@endif
  </form>
  <table><tr><th>Name</th><th>Contact</th><th>Type</th><th>Address</th><th></th></tr>
  @forelse($customers as $c)
    <tr><td>{{ $c->customer_name }}</td><td>{{ $c->contact_number ?: '-' }}</td><td>{{ $c->customer_type }}</td><td>{{ $c->address ?: '-' }}</td>
    <td><div class="actions"><a class="btn alt" href="{{ route('customers.edit', $c) }}">Edit</a>
    @if(auth()->user()->role === 'owner')
      <form method="post" action="{{ route('customers.destroy', $c) }}" onsubmit="return confirm('Delete {{ addslashes($c->customer_name) }}?')">@csrf @method('DELETE')<button class="danger">Delete</button></form>
    @endif</div></td></tr>
  @empty<tr><td colspan="5" class="empty">{{ $term !== '' ? 'No customers match your search.' : 'No customers yet. Add the first one.' }}</td></tr>@endforelse</table>
  {{ $customers->links() }}
</div>
@endsection
