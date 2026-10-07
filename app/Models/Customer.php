<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public const TYPES = ['Household', 'Sari-sari Store', 'Office', 'Other'];

    protected $fillable = ['customer_name', 'contact_number', 'address', 'customer_type'];

    public function sales() { return $this->hasMany(Sale::class); }
    public function deliveries() { return $this->hasMany(DeliveryRequest::class); }

    public function scopeSearch($q, ?string $term)
    {
        return $term === null || $term === ''
            ? $q
            : $q->where(fn ($w) => $w->where('customer_name', 'like', "%{$term}%")
                ->orWhere('contact_number', 'like', "%{$term}%"));
    }
}
