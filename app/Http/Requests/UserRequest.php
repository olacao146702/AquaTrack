<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('owner');
    }

    public function rules(): array
    {
        $editing = (bool) $this->route('user');

        return [
            'full_name' => ['required', 'string', 'min:2', 'max:100'],
            'username'  => ['required', 'alpha_dash:ascii', 'min:3', 'max:50', Rule::unique('users', 'username')->ignore($this->route('user')?->id)],
            'role'      => ['required', 'in:owner,counter,delivery'],
            'status'    => ['required', 'in:Active,Inactive'],
            // When editing, leaving the password blank keeps the current one.
            'password'  => [$editing ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->numbers(), 'max:72'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.alpha_dash' => 'Use letters, numbers, dashes and underscores only (no spaces).',
            'username.unique'     => 'That username is already taken.',
            'password.required'   => 'Set a password for the new account.',
            'password.confirmed'  => 'The two passwords do not match.',
        ];
    }
}
