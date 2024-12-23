<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'distinguishes_en',
        'distinguishes_ar',
        'privacy_en',
        'privacy_ar',
        'terms_en',
        'terms_ar',
        'roles_en',
        'roles_ar',
        'about_en',
        'about_ar',
        'services_en',
        'services_ar',
    ];
}
