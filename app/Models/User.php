<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'joined_with',
        'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'last_otp',
        'last_otp_expire',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function wishlist(){
        return $this->hasMany(Wishlist::class);
    }

    public function cart(){
        return $this->hasMany(Cart::class);
    }

    public function addresses(){
        return $this->hasMany(Address::class);
    }

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function interacts(){
        return $this->hasMany(ReviewInteract::class);
    }

    public function supportMessages(){
        return $this->hasMany(SupportMessage::class);
    }
}
