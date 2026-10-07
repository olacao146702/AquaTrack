<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::orderBy('role')->orderBy('full_name')->paginate(20)]);
    }

    public function create()
    {
        return view('users.form', ['account' => new User(['role' => 'counter', 'status' => 'Active'])]);
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->validated());

        return redirect()->route('users.index')->with('ok', "Account {$user->username} created.");
    }

    public function edit(User $user)
    {
        return view('users.form', ['account' => $user]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $keepsAccess = $data['role'] === 'owner' && $data['status'] === 'Active';

        if (! $keepsAccess && $user->is($request->user())) {
            return back()->withInput()->withErrors(['status' => 'You cannot deactivate or demote your own account.']);
        }
        if (! $keepsAccess && $user->role === 'owner' && $user->status === 'Active'
            && User::where('role', 'owner')->where('status', 'Active')->count() <= 1) {
            return back()->withInput()->withErrors(['status' => 'At least one active owner account is required.']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('ok', "Account {$user->username} updated.");
    }
}
