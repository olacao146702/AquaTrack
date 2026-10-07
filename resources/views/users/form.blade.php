@extends('layouts.app')
@section('title', $account->exists ? 'Edit user' : 'Add user')
@section('content')
<div class="card" style="max-width:560px">
  <h2 style="margin-top:0">{{ $account->exists ? 'Edit user' : 'Add user' }}</h2>
  <form method="post" action="{{ $account->exists ? route('users.update', $account) : route('users.store') }}" autocomplete="off">@csrf @if($account->exists) @method('PUT') @endif
    <label for="full_name">Full name</label><input id="full_name" name="full_name" value="{{ old('full_name', $account->full_name) }}" maxlength="100" required>
    <label for="username">Username</label><input id="username" name="username" value="{{ old('username', $account->username) }}" maxlength="50" autocapitalize="none" required>
    <div class="cols">
      <div><label for="role">Role</label><select id="role" name="role">@foreach(['owner' => 'Admin / Owner', 'counter' => 'Counter staff', 'delivery' => 'Delivery rider'] as $k => $l)<option value="{{ $k }}" @selected(old('role', $account->role) === $k)>{{ $l }}</option>@endforeach</select></div>
      <div><label for="status">Status</label><select id="status" name="status">@foreach(['Active', 'Inactive'] as $s)<option @selected(old('status', $account->status) === $s)>{{ $s }}</option>@endforeach</select></div>
    </div>
    <label for="password">Password @if($account->exists)<small>(leave blank to keep the current one)</small>@endif</label><input id="password" type="password" name="password" autocomplete="new-password" @unless($account->exists) required @endunless>
    <label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
    <small>At least 8 characters with letters and numbers.</small>
    <p class="actions"><button>Save user</button><a class="btn alt" href="{{ route('users.index') }}">Cancel</a></p>
  </form>
</div>
@endsection
