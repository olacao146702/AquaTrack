<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'AquaTrack')</title>
<style>
:root{--ink:#16212b;--mute:#5d6b78;--line:#dbe3ea;--bg:#f4f7f9;--brand:#0b6e8a;--warn:#b4531a;--ok:#1f7a4d}
*{box-sizing:border-box}body{margin:0;font:15px/1.5 system-ui,-apple-system,"Segoe UI",sans-serif;color:var(--ink);background:var(--bg)}
header{background:#fff;border-bottom:1px solid var(--line);padding:.6rem 1rem;display:flex;gap:1rem;align-items:center;flex-wrap:wrap}
header strong{color:var(--brand);font-size:1.1rem;margin-right:.5rem}header a{color:var(--ink);text-decoration:none;padding:.25rem .5rem;border-radius:4px}
header a:hover{background:var(--bg)}header form{margin-left:auto}
main{max-width:980px;margin:1.25rem auto;padding:0 1rem}
.card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:1rem;margin-bottom:1rem;overflow-x:auto}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem}.stat b{display:block;font-size:1.5rem}.stat span,small{color:var(--mute)}
table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:.5rem;border-bottom:1px solid var(--line);vertical-align:middle}th{font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:var(--mute)}
label{display:block;font-weight:600;margin:.6rem 0 .2rem}input,select{width:100%;padding:.5rem;border:1px solid var(--line);border-radius:6px;font:inherit}
button{background:var(--brand);color:#fff;border:0;border-radius:6px;padding:.5rem .9rem;font:inherit;cursor:pointer}button.alt{background:#fff;color:var(--ink);border:1px solid var(--line)}
.flash{padding:.6rem .8rem;border-radius:6px;margin-bottom:1rem}.flash.ok{background:#e6f4ec;color:var(--ok)}.flash.err{background:#fbeae0;color:var(--warn)}
.badge{padding:.1rem .5rem;border-radius:99px;background:var(--bg);border:1px solid var(--line);font-size:.8rem}.low{color:var(--warn);font-weight:600}
.inline{display:flex;gap:.4rem;align-items:center}.inline select{width:auto}
</style>
</head>
<body>
<header>
  <strong>AquaTrack</strong>
  @auth
    @if(auth()->user()->hasRole('owner'))<a href="{{ route('dashboard') }}">Dashboard</a>@endif
    @if(auth()->user()->hasRole('owner','counter'))<a href="{{ route('sales.create') }}">New sale</a><a href="{{ route('sales.index') }}">Sales</a>@endif
    <a href="{{ route('deliveries.index') }}">Deliveries</a>
    <form method="post" action="{{ route('logout') }}" class="inline">@csrf<small>{{ auth()->user()->full_name }}</small> <button class="alt">Log out</button></form>
  @endauth
</header>
<main>
  @if(session('ok'))<div class="flash ok">{{ session('ok') }}</div>@endif
  @if($errors->any())<div class="flash err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  @yield('content')
</main>
</body>
</html>
