<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    public const TYPES = ['Container', 'Accessory', 'Raw Material'];

    protected $fillable = ['item_name', 'item_type', 'filled_qty', 'empty_qty', 'low_stock_level'];

    public function movements() { return $this->hasMany(StockMovement::class, 'item_id'); }
    public function products() { return $this->hasMany(Product::class, 'item_id'); }

    public function isLow(): bool
    {
        return $this->filled_qty <= $this->low_stock_level;
    }

    public function scopeLow($q)
    {
        return $q->whereColumn('filled_qty', '<=', 'low_stock_level');
    }
}
