<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'path',
    ];

    /**
     * Get the product associated with this image.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
