<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'description',
        'name_ar',
        'description_ar',
    ];

    public $timestamps = false;

    /**
     * Get the product associated with this option.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
