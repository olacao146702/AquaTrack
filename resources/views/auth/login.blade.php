@extends('layouts.app')
@section('title', 'Sign in')
@section('content')
<div class="card auth">
  <div class="mark"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5c-.3 0-.6.2-.8.4C9 5.3 5 9.6 5 14a7 7 0 0 0 14 0c0-4.4-4-8.7-6.2-11.1-.2-.2-.5-.4-.8-.4z"/></svg></div>
  <h2>Sign in</h2>
  <p><small>Sales, inventory and delivery management for your water refilling station.</small></p>
  <form method="post" action="{{ url('/login') }}">@csrf
    <label for="username">Username</label><input id="username" name="username" value="{{ old('username') }}" autocomplete="username" autocapitalize="none" autofocus required>
    <label for="password">Password</label><input id="password" type="password" name="password" autocomplete="current-password" required>
    <button>Sign in</button>
  </form>
</div>
@endsection
