<?php

namespace App\Http\Requests;

use App\Models\InventoryItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('owner');
    }

    public function rules(): array
    {
        $rules = [
            'item_name'       => ['required', 'string', 'min:2', 'max:100', Rule::unique('inventory_items', 'item_name')->ignore($this->route('item')?->id)],
            'item_type'       => ['required', Rule::in(InventoryItem::TYPES)],
            'low_stock_level' => ['required', 'integer', 'min:0', 'max:100000'],
        ];

        // Opening stock is only entered when the item is created; later changes go through stock movements.
        if (! $this->route('item')) {
            $rules['filled_qty'] = ['required', 'integer', 'min:0', 'max:100000'];
            $rules['empty_qty']  = ['required', 'integer', 'min:0', 'max:100000'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'item_name.unique' => 'An inventory item with this name already exists.',
            '*.integer'        => 'Enter a whole number.',
            '*.min'            => 'The number cannot be negative.',
        ];
    }
}
