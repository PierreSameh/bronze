<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['city'];

    public function shippingMethods()
    {
        return $this->belongsToMany(ShippingMethod::class, 'city_shipping_methods')
                    ->withPivot('shipping_cost', 'time_range')
                    ->withTimestamps();
    }

    public function addresses(){
        return $this->hasMany(Address::class);
    }
}
