<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name_en',
        'name_ar',
        'info_en',
        'info_ar',
        'price',
        'wholesale_price',
        'sale_percentage',
        'rate',
        'quantity',
        'description_en',
        'description_ar',
        'other_info_en',
        'other_info_ar',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'other_info_en' => 'array',
        'other_info_ar' => 'array',
    ];

    /**
     * Get the category associated with the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function options(){
        return $this->hasMany(ProductOption::class);
    }

    public function images(){
        return $this->hasMany(ProductImage::class);
    }

    public function info(){
        return $this->hasMany(ProductInfo::class);
    }
}
