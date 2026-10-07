<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'AquaTrack') - AquaTrack</title>
<style>
:root{--ink:#16212b;--mute:#5d6b78;--line:#dbe3ea;--bg:#f4f7f9;--brand:#0b6e8a;--warn:#b4531a;--ok:#1f7a4d}
*{box-sizing:border-box}body{margin:0;font:15px/1.5 system-ui,-apple-system,"Segoe UI",sans-serif;color:var(--ink);background:var(--bg)}
header{background:#fff;border-bottom:1px solid var(--line);padding:.6rem 1rem;display:flex;gap:.25rem 1rem;align-items:center;flex-wrap:wrap}
header strong{color:var(--brand);font-size:1.1rem;margin-right:.5rem}header nav{display:flex;flex-wrap:wrap;gap:.1rem}
header a{color:var(--ink);text-decoration:none;padding:.25rem .5rem;border-radius:4px}header a:hover,header a[aria-current]{background:var(--bg)}header a[aria-current]{color:var(--brand);font-weight:600}
header form{margin-left:auto}
main{max-width:980px;margin:1.25rem auto;padding:0 1rem}
.card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:1rem;margin-bottom:1rem;overflow-x:auto}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem}.stat b{display:block;font-size:1.5rem}.stat span,small{color:var(--mute)}
table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:.5rem;border-bottom:1px solid var(--line);vertical-align:middle}th{font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:var(--mute)}
label{display:block;font-weight:600;margin:.6rem 0 .2rem}input,select,textarea{width:100%;padding:.5rem;border:1px solid var(--line);border-radius:6px;font:inherit;background:#fff;color:var(--ink)}
input[type=checkbox]{width:auto}label.check{font-weight:400;display:flex;gap:.5rem;align-items:center}
:focus-visible{outline:2px solid var(--brand);outline-offset:2px}
button,.btn{background:var(--brand);color:#fff;border:0;border-radius:6px;padding:.5rem .9rem;font:inherit;cursor:pointer;text-decoration:none;display:inline-block}
button.alt,.btn.alt{background:#fff;color:var(--ink);border:1px solid var(--line)}button.danger{background:#fff;color:var(--warn);border:1px solid var(--warn)}
button:disabled{opacity:.6;cursor:wait}
.flash{padding:.6rem .8rem;border-radius:6px;margin-bottom:1rem}.flash.ok{background:#e6f4ec;color:var(--ok)}.flash.err{background:#fbeae0;color:var(--warn)}
.badge{padding:.1rem .5rem;border-radius:99px;background:var(--bg);border:1px solid var(--line);font-size:.8rem;white-space:nowrap}.badge.warn{color:var(--warn);border-color:var(--warn)}.badge.good{color:var(--ok);border-color:var(--ok)}
.low{color:var(--warn);font-weight:600}.inline{display:flex;gap:.4rem;align-items:center;flex-wrap:wrap}.inline select,.inline input{width:auto}
.toolbar{display:flex;justify-content:space-between;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.8rem}.toolbar h2,.toolbar h3{margin:0}
.actions{display:flex;gap:.4rem;flex-wrap:wrap}.empty{text-align:center;color:var(--mute);padding:1.5rem .5rem}
.cols{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0 1rem}.right{text-align:right}
.pager{display:flex;gap:1rem;align-items:center;justify-content:center;padding:.8rem 0 0}.pager a,.pager .dis{padding:.3rem .7rem;border:1px solid var(--line);border-radius:6px;text-decoration:none;color:var(--ink)}.pager .dis{opacity:.5}
a{color:var(--brand)}
@media (max-width:640px){th,td{padding:.4rem .3rem;font-size:.9rem}header form{margin-left:0}}
</style>
</head>
<body>
@php $role = auth()->user()?->role; $on = fn (...$r) => request()->routeIs(...$r) ? 'aria-current=page' : ''; @endphp
<header>
  <strong>AquaTrack</strong>
  @auth
  <nav aria-label="Main">
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
  <form method="post" action="{{ route('logout') }}" class="inline">@csrf<small>{{ auth()->user()->full_name }}</small> <button class="alt">Log out</button></form>
  @endauth
</header>
<main>
  @if(session('ok'))<div class="flash ok" role="status">{{ session('ok') }}</div>@endif
  @if($errors->any())<div class="flash err" role="alert">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  @yield('content')
</main>
<script>
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
