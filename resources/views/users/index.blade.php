@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="card">
  <div class="toolbar"><h2>Users</h2><a class="btn" href="{{ route('users.create') }}">Add user</a></div>
  <table><tr><th>Name</th><th>Username</th><th>Role</th><th>Status</th><th></th></tr>
  @foreach($users as $u)
    <tr><td>{{ $u->full_name }}@if($u->is(auth()->user())) <small>(you)</small>@endif</td><td>{{ $u->username }}</td><td>{{ ['owner' => 'Admin / Owner', 'counter' => 'Counter staff', 'delivery' => 'Delivery rider'][$u->role] }}</td>
    <td><span class="badge {{ $u->status === 'Active' ? 'good' : 'warn' }}">{{ $u->status }}</span></td><td><a class="btn alt" href="{{ route('users.edit', $u) }}">Edit</a></td></tr>
  @endforeach</table>
  {{ $users->links() }}
  <p><small>Accounts are deactivated instead of deleted, so sales and stock history keep their author.</small></p>
</div>
@endsection
