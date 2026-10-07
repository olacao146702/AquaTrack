<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('owner', 'counter');
    }

    public function rules(): array
    {
        return [
            'customer_name'  => ['required', 'string', 'min:2', 'max:100'],
            'contact_number' => ['nullable', 'regex:/^[0-9+\-\s()]{7,15}$/'],
            'address'        => ['nullable', 'string', 'max:255'],
            'customer_type'  => ['required', Rule::in(Customer::TYPES)],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required'  => 'Enter the customer name.',
            'customer_name.min'       => 'The customer name is too short.',
            'contact_number.regex'    => 'Enter a valid contact number (7 to 15 digits; + - ( ) and spaces allowed).',
            'customer_type.*'         => 'Choose a valid customer type.',
        ];
    }
}
