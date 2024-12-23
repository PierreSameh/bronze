<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = ['name', 'active', 'icon'];

    public function cities()
    {
        return $this->belongsToMany(City::class, 'city_shipping_methods')
                    ->withPivot('shipping_cost', 'time_range')
                    ->withTimestamps();
    }
}
