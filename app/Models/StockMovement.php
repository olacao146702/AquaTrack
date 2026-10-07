<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['item_id', 'user_id', 'movement_type', 'filled_change', 'empty_change', 'remarks'];
}
