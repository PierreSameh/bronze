<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'receipent_name',
        'city_id',
        'district',
        'company',
        'id_number',
        'address_one',
        'address_two',
        'zipcode',
        'country',
        'phone',
        'email',
    ];

    /**
     * Get the user that owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
