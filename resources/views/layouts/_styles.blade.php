<style>
/* ---------- Design tokens ---------- */
:root{
  color-scheme:light dark;
  --bg:#f3f6f9;--surface:#fff;--surface-2:#f6f9fb;
  --ink:#0f1c27;--ink-2:#46576a;--line:#e0e7ee;--line-strong:#c5d1dc;
  --brand:#0b6e8a;--brand-hover:#095a73;--brand-soft:#e4f2f6;--on-brand:#fff;
  --ok:#17734a;--ok-bg:#e3f4eb;--warn:#9a4a0e;--warn-bg:#fdeee0;--danger:#b3261e;--danger-bg:#fdeceb;
  --r:12px;--r-sm:8px;--shadow:0 1px 2px rgba(15,28,39,.05),0 2px 6px rgba(15,28,39,.04);
  --font:system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
}
@media (prefers-color-scheme:dark){:root{
  --bg:#0d141a;--surface:#151f28;--surface-2:#1a2631;
  --ink:#e9eff4;--ink-2:#a5b4c2;--line:#25333f;--line-strong:#36485a;
  --brand:#4db6d3;--brand-hover:#7ccbe2;--brand-soft:#12303b;--on-brand:#06222b;
  --ok:#52c98f;--ok-bg:#12301f;--warn:#f2a569;--warn-bg:#33210f;--danger:#ff8f86;--danger-bg:#3a1614;
  --shadow:none;}}

/* ---------- Base ---------- */
*,*::before,*::after{box-sizing:border-box}
html{-webkit-text-size-adjust:100%}
body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.55 var(--font);-webkit-font-smoothing:antialiased}
h1,h2,h3{line-height:1.25;letter-spacing:-.01em;font-weight:650;margin:0 0 .75rem}
h2{font-size:1.3rem}h3{font-size:1.05rem}
p{margin:.5rem 0}a{color:var(--brand)}a:hover{color:var(--brand-hover)}
small{color:var(--ink-2);font-size:.8125rem}
:focus-visible{outline:2px solid var(--brand);outline-offset:2px}
.skip{position:absolute;left:-999px;top:.5rem;background:var(--brand);color:var(--on-brand);padding:.5rem 1rem;border-radius:var(--r-sm);z-index:50}
.skip:focus{left:.75rem}

/* ---------- Top bar & navigation ---------- */
header.top{position:sticky;top:0;z-index:20;background:var(--surface);border-bottom:1px solid var(--line)}
.top-in{max-width:1240px;margin:0 auto;padding:.55rem 1rem;display:flex;align-items:center;gap:.75rem}
.brand{display:flex;align-items:center;gap:.5rem;font-weight:700;font-size:1.15rem;color:var(--brand);text-decoration:none;letter-spacing:-.01em}
.brand svg{width:26px;height:26px;flex:none}
nav.main{display:flex;flex-wrap:wrap;gap:.15rem;flex:1}
nav.main a{white-space:nowrap;padding:.45rem .6rem;border-radius:var(--r-sm);color:var(--ink-2);text-decoration:none;font-weight:500}
nav.main a:hover{background:var(--surface-2);color:var(--ink)}
nav.main a[aria-current]{background:var(--brand-soft);color:var(--brand);font-weight:600}
.userbox{display:flex;align-items:center;gap:.75rem}.userbox form{margin:0}
.who{display:flex;flex-direction:column;line-height:1.2;text-align:right}.who b{font-size:.875rem;font-weight:600}
.menu-btn{display:none}

/* ---------- Page ---------- */
main{max-width:1080px;margin:1.5rem auto 3rem;padding:0 1rem}
.card{background:var(--surface);border:1px solid var(--line);border-radius:var(--r);padding:1.25rem;margin-bottom:1rem;box-shadow:var(--shadow);overflow-x:auto}
.card>h2:first-child,.card>h3:first-child{margin-top:0}
.toolbar{display:flex;justify-content:space-between;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:1rem}
.toolbar h2,.toolbar h3{margin:0}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1rem}.grid>.card{margin-bottom:0}
.cols{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:0 1rem}
.actions{display:flex;gap:.5rem;flex-wrap:wrap;align-items:center}
.inline{display:flex;gap:.5rem;align-items:center;flex-wrap:wrap}.inline select,.inline input{width:auto}
.right{text-align:right}
.stat span{display:block;font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-2)}
.stat b{display:block;font-size:1.75rem;line-height:1.3;letter-spacing:-.02em;margin-top:.15rem;font-variant-numeric:tabular-nums}
.empty{text-align:center;color:var(--ink-2);padding:2rem 1rem}
.low{color:var(--warn);font-weight:600}

/* ---------- Tables ---------- */
table{width:100%;border-collapse:collapse}
th,td{text-align:left;padding:.7rem .6rem;border-bottom:1px solid var(--line);vertical-align:middle}
th{font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-2);white-space:nowrap;border-bottom-color:var(--line-strong)}
td{font-variant-numeric:tabular-nums}
tr:last-child td{border-bottom:0}
tr:hover td{background:var(--surface-2)}
td.right,th.right{text-align:right}

/* ---------- Forms ---------- */
label{display:block;font-weight:600;font-size:.875rem;margin:.9rem 0 .3rem}
label.check{font-weight:400;font-size:.9375rem;display:flex;gap:.5rem;align-items:center}
input,select,textarea{width:100%;min-height:42px;padding:.55rem .75rem;border:1px solid var(--line-strong);border-radius:var(--r-sm);font:inherit;background:var(--surface);color:var(--ink);transition:border-color .15s,box-shadow .15s}
input::placeholder{color:var(--ink-2);opacity:.8}
input:hover,select:hover{border-color:var(--ink-2)}
input:focus,select:focus,textarea:focus{outline:none;border-color:var(--brand);box-shadow:0 0 0 3px var(--brand-soft)}
input:user-invalid,select:user-invalid{border-color:var(--danger)}
input[type=checkbox]{width:18px;height:18px;min-height:0;accent-color:var(--brand)}
input:disabled,select:disabled{opacity:.6;cursor:not-allowed}
label small{font-weight:400}

/* ---------- Buttons ---------- */
button,.btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;min-height:42px;padding:.5rem 1.1rem;border:1px solid transparent;border-radius:var(--r-sm);background:var(--brand);color:var(--on-brand);font:inherit;font-weight:600;line-height:1.2;text-decoration:none;cursor:pointer;transition:background .15s,border-color .15s}
button:hover,.btn:hover{background:var(--brand-hover);color:var(--on-brand)}
button.alt,.btn.alt{background:var(--surface);color:var(--ink);border-color:var(--line-strong)}
button.alt:hover,.btn.alt:hover{background:var(--surface-2);color:var(--ink);border-color:var(--ink-2)}
button.danger{background:var(--surface);color:var(--danger);border-color:var(--danger)}
button.danger:hover{background:var(--danger-bg);color:var(--danger)}
button:disabled{opacity:.6;cursor:wait}
td button,td .btn,.userbox button,.sm{min-height:34px;padding:.3rem .75rem;font-size:.875rem}
form>p,form>p.actions{margin:1.25rem 0 0}

/* ---------- Feedback ---------- */
.flash{padding:.75rem 1rem;border-radius:var(--r-sm);margin-bottom:1rem;border:1px solid;border-left-width:4px}
.flash.ok{background:var(--ok-bg);color:var(--ok);border-color:var(--ok)}
.flash.warn{background:var(--warn-bg);color:var(--warn);border-color:var(--warn)}
.flash.err{background:var(--danger-bg);color:var(--danger);border-color:var(--danger)}
.flash a{color:inherit;font-weight:600}
.badge{display:inline-flex;align-items:center;padding:.15rem .65rem;border-radius:999px;font-size:.75rem;font-weight:600;white-space:nowrap;background:var(--surface-2);color:var(--ink-2);border:1px solid var(--line-strong);text-decoration:none}
.badge.good{background:var(--ok-bg);color:var(--ok);border-color:transparent}
.badge.warn{background:var(--warn-bg);color:var(--warn);border-color:transparent}
a.badge:hover{border-color:var(--ink-2)}
.pager{display:flex;gap:1rem;align-items:center;justify-content:center;padding:1rem 0 0}
.pager a,.pager .dis{padding:.4rem .9rem;border:1px solid var(--line-strong);border-radius:var(--r-sm);text-decoration:none;color:var(--ink);font-weight:500}
.pager a:hover{background:var(--surface-2)}.pager .dis{opacity:.45}

/* ---------- Sign-in ---------- */
.auth{max-width:400px;margin:3rem auto}.auth .mark{display:grid;place-items:center;width:48px;height:48px;border-radius:12px;background:var(--brand-soft);color:var(--brand);margin-bottom:1rem}
.auth .mark svg{width:28px;height:28px}.auth h2{margin-bottom:.25rem}.auth button{width:100%;margin-top:.5rem}

/* ---------- Responsive ---------- */
@media (max-width:1180px){
  .js nav.main{display:none;order:4;flex-basis:100%;flex-direction:column;padding-top:.5rem}
  .js nav.main.open{display:flex}.js nav.main a{padding:.7rem .8rem}
  .top-in{flex-wrap:wrap;gap:.5rem 1rem}.brand{order:1}.userbox{order:2;margin-left:auto}.who{display:none}
  .js .menu-btn{display:inline-flex;order:3}
}
@media (max-width:640px){
  main{margin-top:1rem;padding:0 .75rem}.card{padding:1rem}
  input,select,textarea{font-size:1rem}.stat b{font-size:1.5rem}
  .toolbar{align-items:flex-start}.toolbar>.actions{width:100%}.toolbar>.actions .btn{flex:1 1 auto}
  th,td{padding:.55rem .45rem;font-size:.875rem}.auth{margin:1.5rem auto}
}
@media (prefers-reduced-motion:reduce){*{transition:none!important}}

/* ---------- Print (receipts and reports) ---------- */
@media print{
  header.top,.skip,.flash,.pager,.toolbar .btn,.toolbar button,form,.actions{display:none!important}
  body{background:#fff;color:#000}.card{border:0;box-shadow:none;padding:0}main{max-width:none;margin:0}
}
</style>
