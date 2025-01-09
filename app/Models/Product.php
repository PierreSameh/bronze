<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'itemNo',
        'packQty',
        'stock',
        'gmtModified',
        'rate',
        'category_id',
        'group_id',
    ];

    protected $appends = ['is_new', 'in_wishlist']; // Add this line to append the is_new attribute

    public function getInWishlistAttribute()
    {
        $request = request();
        $user = null;

        // Check if there's a bearer token in the request
        if ($request->bearerToken()) {
            // Get user from token
            $token = PersonalAccessToken::findToken($request->bearerToken());
            if ($token) {
                $user = $token->tokenable;
            }
        }

        // If no user found, return false
        if (!$user) {
            return false;
        }

        // Check if product is in user's wishlist
        return $this->wishlist()
            ->where('user_id', $user->id)
            ->exists();
    }
    /**
     * Check if the product is new (created within the last 7 days).
     *
     * @return bool
     */
    public function getIsNewAttribute()
    {
        return $this->created_at >= now()->subWeek();
    }
    /**
     * Get the category associated with the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function translations()
    {
        return $this->hasOne(ProductTranslation::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
