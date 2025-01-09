<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'shipping_method',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'zipcode',
        'payment_method',
        'payment_status',
        'promocode_id',
        'status',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the promocode applied to the order.
     */
    public function promocode()
    {
        return $this->belongsTo(Promocode::class);
    }

    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

}
