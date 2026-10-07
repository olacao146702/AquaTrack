<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['customer_id', 'user_id', 'sale_type', 'total_amount', 'payment_status'];

    public function items() { return $this->hasMany(SaleItem::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function cashier() { return $this->belongsTo(User::class, 'user_id'); }
}
