<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('owner', 'counter');
    }

    public function rules(): array
    {
        return [
            'customer_id'        => ['nullable', 'exists:customers,id'],
            'payment_status'     => ['required', 'in:Paid,Unpaid'],
            'lines'              => ['required', 'array', 'min:1'],
            'lines.0.product_id' => ['required', 'exists:products,id'],
            'lines.*.product_id' => ['nullable', 'exists:products,id'],
            'lines.*.quantity'   => ['required_with:lines.*.product_id', 'nullable', 'integer', 'min:1', 'max:500'],
        ];
    }
}
