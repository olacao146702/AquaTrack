<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function show() { return view('auth.login'); }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'max:255'],
        ], [
            'username.required' => 'Please enter your username.',
            'password.required' => 'Please enter your password.',
        ]);

        // Only reveal an inactive account when the password is right, so usernames can't be probed.
        $user = User::where('username', $cred['username'])->first();
        if ($user && $user->status !== 'Active' && Hash::check($cred['password'], $user->password)) {
            return back()->withErrors(['username' => 'This account is inactive. Please contact the owner.'])->onlyInput('username');
        }

        if (! Auth::attempt($cred + ['status' => 'Active'])) {
            return back()->withErrors(['username' => 'Invalid username or password.'])->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
