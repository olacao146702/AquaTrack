<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('owner');
    }

    public function rules(): array
    {
        return [
            'product_name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('products', 'product_name')->ignore($this->route('product')?->id)],
            'category'     => ['required', Rule::in(Product::CATEGORIES)],
            // Everything except a refill is a physical item, so it must be tied to an inventory item.
            'item_id'      => [Rule::requiredIf(fn () => $this->input('category') !== 'Refill'), 'nullable', 'exists:inventory_items,id'],
            'unit_price'   => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'is_active'    => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.unique'    => 'A product with this name already exists.',
            'item_id.required'       => 'Link this product to an inventory item so its stock is tracked.',
            'unit_price.*'           => 'Enter a price between 0.01 and 99,999.99.',
        ];
    }
}
