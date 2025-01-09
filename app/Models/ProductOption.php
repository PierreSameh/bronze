<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $fillable = [
        'product_id',
        'itemNo',
        'keywords',
    ];

    /**
     * Get the product associated with this option.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cartOptions()
    {
        return $this->hasMany(CartOption::class);
    }

    public function orderOptions()
{
    return $this->hasMany(OrderOption::class);
}

}
