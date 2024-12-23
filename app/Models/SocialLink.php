<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = [
        'youtube',
        'facebook',
        'pinterest',
        'instagram',
        'twitter',
        'tiktok',
        'email',
    ];
}
