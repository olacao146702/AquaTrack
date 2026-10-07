<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['full_name', 'username', 'password', 'role', 'status'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed'];

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
