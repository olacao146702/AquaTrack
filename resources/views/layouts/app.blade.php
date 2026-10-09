<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'AquaTrack') - AquaTrack</title>
<script>document.documentElement.classList.add('js')</script>
@include('layouts._favicon')
@include('layouts._styles')
</head>
<body>
@php
  $role = auth()->user()?->role;
  $roleLabel = ['owner' => 'Admin', 'counter' => 'Staff', 'delivery' => 'Rider'][$role] ?? '';
  $on = fn (...$r) => request()->routeIs(...$r) ? 'aria-current=page' : '';
@endphp
<a class="skip" href="#main">Skip to content</a>
<header class="top"><div class="top-in">
  <a class="brand" href="{{ auth()->check() ? route('home') : route('login') }}">
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5c-.3 0-.6.2-.8.4C9 5.3 5 9.6 5 14a7 7 0 0 0 14 0c0-4.4-4-8.7-6.2-11.1-.2-.2-.5-.4-.8-.4z"/></svg>AquaTrack
  </a>
  @auth
  <nav class="main" id="mainnav" aria-label="Main">
    @if($role === 'owner')<a href="{{ route('dashboard') }}" {!! $on('dashboard') !!}>Dashboard</a>@endif
    @if(in_array($role, ['owner', 'counter']))
      <a href="{{ route('sales.create') }}" {!! $on('sales.create') !!}>New sale</a>
      <a href="{{ route('sales.index') }}" {!! $on('sales.index', 'sales.show') !!}>Sales</a>
    @endif
    <a href="{{ route('deliveries.index') }}" {!! $on('deliveries.*') !!}>Deliveries</a>
    @if(in_array($role, ['owner', 'counter']))
      <a href="{{ route('customers.index') }}" {!! $on('customers.*') !!}>Customers</a>
      <a href="{{ route('inventory.index') }}" {!! $on('inventory.*', 'items.*') !!}>Inventory</a>
    @endif
    @if($role === 'owner')
      <a href="{{ route('products.index') }}" {!! $on('products.*') !!}>Products</a>
      <a href="{{ route('reports.index') }}" {!! $on('reports.*') !!}>Reports</a>
      <a href="{{ route('users.index') }}" {!! $on('users.*') !!}>Users</a>
    @endif
  </nav>
  <div class="userbox">
    <span class="who"><b>{{ auth()->user()->full_name }}</b><small>{{ $roleLabel }}</small></span>
    <form method="post" action="{{ route('logout') }}">@csrf<button class="alt">Log out</button></form>
  </div>
  <button type="button" class="alt menu-btn sm" id="menuBtn" aria-expanded="false" aria-controls="mainnav">Menu</button>
  @endauth
</div></header>
<main id="main">
  @if(session('ok'))<div class="flash ok" role="status">{{ session('ok') }}</div>@endif
  @if($errors->any())<div class="flash err" role="alert">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  @yield('content')
</main>
<script>
// Phone menu: the navigation opens and closes from the Menu button.
(function () {
  const b = document.getElementById('menuBtn'), n = document.getElementById('mainnav');
  if (b && n) b.addEventListener('click', () => b.setAttribute('aria-expanded', n.classList.toggle('open')));
})();
// Loading state: after the first click the button is disabled and says "Please wait...", so a double click cannot post twice.
document.addEventListener('submit', e => {
  if (e.defaultPrevented) return;
  const b = e.target.querySelector('button:not([type=button])');
  if (b) setTimeout(() => { b.dataset.label = b.textContent; b.textContent = 'Please wait…'; b.disabled = true; }, 0);
});
// Coming back with the browser's Back button restores the buttons.
window.addEventListener('pageshow', () => document.querySelectorAll('button[data-label]').forEach(b => { b.textContent = b.dataset.label; b.disabled = false; }));
</script>
</body>
</html>
