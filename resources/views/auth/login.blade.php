@extends('layouts.app')
@section('title', 'Sign in')
@section('content')
<div class="card" style="max-width:360px;margin:3rem auto">
  <h2 style="margin-top:0">Sign in</h2>
  <form method="post" action="{{ url('/login') }}">@csrf
    <label for="username">Username</label><input id="username" name="username" value="{{ old('username') }}" autofocus required>
    <label for="password">Password</label><input id="password" type="password" name="password" required>
    <p><button>Sign in</button></p>
  </form>
</div>
@endsection
