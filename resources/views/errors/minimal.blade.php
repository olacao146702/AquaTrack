<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }} - AquaTrack</title>
@include('layouts._favicon')
@include('layouts._styles')
<style>body{min-height:100vh;display:grid;place-items:center;padding:1rem}.err-box{max-width:440px;text-align:center}.err-box .mark{margin:0 auto 1rem}</style></head>
<body><div class="card err-box auth" style="margin:0">
  <div class="mark" style="margin:0 auto 1rem"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5c-.3 0-.6.2-.8.4C9 5.3 5 9.6 5 14a7 7 0 0 0 14 0c0-4.4-4-8.7-6.2-11.1-.2-.2-.5-.4-.8-.4z"/></svg></div>
  <h2>{{ $title }}</h2><p>{{ $message }}</p>
  <p class="actions" style="justify-content:center"><a class="btn alt" href="javascript:history.back()">Go back</a><a class="btn" href="{{ url('/') }}">Home</a></p>
</div></body></html>
