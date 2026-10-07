<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRequest extends Model
{
    protected $fillable = [
        'customer_id', 'product_id', 'assigned_to', 'sale_id', 'quantity', 'delivery_address',
        'preferred_date', 'preferred_time', 'status', 'completed_at', 'remarks',
    ];
    protected $casts = ['preferred_date' => 'date', 'completed_at' => 'datetime'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function rider() { return $this->belongsTo(User::class, 'assigned_to'); }
}
