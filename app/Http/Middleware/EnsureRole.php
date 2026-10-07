<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        // An account deactivated while its owner is logged in loses access immediately.
        if ($user && $user->status !== 'Active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['username' => 'Your account is inactive. Please contact the owner.']);
        }

        abort_unless($user?->hasRole(...$roles), 403);

        return $next($request);
    }
}
