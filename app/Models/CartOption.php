<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartOption extends Model
{
    protected $fillable = ["cart_id", "product_option_id"];

    public function cart(){
        return $this->belongsTo(Cart::class);
    }

    public function productOption(){
        return $this->belongsTo(ProductOption::class);
    }
}
