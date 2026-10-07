<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('owner', 'counter');
    }

    public function rules(): array
    {
        $activeProduct = Rule::exists('products', 'id')->where('is_active', true);

        return [
            'customer_id'        => ['nullable', 'exists:customers,id'],
            'payment_status'     => ['required', 'in:Paid,Unpaid'],
            'lines'              => ['required', 'array', 'min:1', 'max:10'],
            'lines.0.product_id' => ['required', $activeProduct],
            'lines.*.product_id' => ['nullable', $activeProduct],
            'lines.*.quantity'   => ['required_with:lines.*.product_id', 'nullable', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.exists'             => 'The selected customer no longer exists.',
            'payment_status.*'               => 'Choose Paid or Unpaid.',
            'lines.required'                 => 'Add at least one product.',
            'lines.0.product_id.required'    => 'Choose at least one product.',
            'lines.*.product_id.exists'      => 'A selected product is no longer available.',
            'lines.*.quantity.required_with' => 'Enter a quantity for every product you select.',
            'lines.*.quantity.integer'       => 'Quantity must be a whole number.',
            'lines.*.quantity.min'           => 'Quantity must be at least 1.',
            'lines.*.quantity.max'           => 'Quantity cannot be more than 500 per line.',
        ];
    }
}
