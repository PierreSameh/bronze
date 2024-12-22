<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $fillable = [
        'product_id',
        'option_type',
        'option_value',
    ];

    /**
     * Get the product associated with this option.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
