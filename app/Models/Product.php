<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['item_id', 'product_name', 'category', 'unit_price', 'is_active'];
    protected $casts = ['unit_price' => 'decimal:2', 'is_active' => 'boolean'];

    public function item() { return $this->belongsTo(InventoryItem::class, 'item_id'); }

    /** Refills use the customer's own container, so they do not reduce station stock. */
    public function consumesStock(): bool
    {
        return $this->item_id !== null && $this->category !== 'Refill';
    }
}
