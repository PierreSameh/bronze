<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInfo extends Model
{
    protected $fillable = ["product_id", "key_en", "key_ar", "value_en", "value_ar"];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
