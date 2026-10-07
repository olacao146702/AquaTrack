<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['item_name', 'item_type', 'filled_qty', 'empty_qty', 'low_stock_level'];

    public function movements() { return $this->hasMany(StockMovement::class, 'item_id'); }

    public function scopeLow($q)
    {
        return $q->whereColumn('filled_qty', '<=', 'low_stock_level');
    }
}
