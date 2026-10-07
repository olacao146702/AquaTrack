<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }} - AquaTrack</title>
<style>body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f4f7f9;color:#16212b;font:16px/1.5 system-ui,sans-serif}
.box{background:#fff;border:1px solid #dbe3ea;border-radius:8px;padding:2rem;max-width:420px;margin:1rem}h1{margin-top:0;font-size:1.3rem;color:#0b6e8a}a{color:#0b6e8a}small{color:#5d6b78}</style></head>
<body><div class="box"><h1>{{ $title }}</h1><p>{{ $message }}</p>
<p><a href="javascript:history.back()">Go back</a> &middot; <a href="{{ url('/') }}">Home</a></p></div></body></html>
