<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOption extends Model
{
    protected $fillable = [
        'order_item_id',
        'product_option_id',
    ];

    /**
     * Get the order item that owns the order option.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the product option associated with the order option.
     */
    public function productOption()
    {
        return $this->belongsTo(ProductOption::class);
    }
}
