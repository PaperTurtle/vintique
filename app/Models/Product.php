<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'artisan_id',
        'category_id',
        'name',
        'description',
        'price',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** Define the relationship with the artisan who created the product. */
    public function artisan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    /** Define the relationship with the category to which the product belongs. */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Define the relationship with product images. */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /** Define the relationship with product reviews. */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** Checks if a specific user has already reviewed the product. */
    public function hasUserReviewed(int $userId): bool
    {
        return $this->reviews()->where('user_id', $userId)->exists();
    }

    /** Calculates and returns the average rating of the product based on its reviews. */
    public function averageRating(): ?float
    {
        return $this->reviews()->avg('rating');
    }

    /** Returns the total number of reviews made for the product. */
    public function totalReviews(): int
    {
        return $this->reviews()->count();
    }

    /** Checks if the product is in the current user's shopping cart. */
    public function isInUserCart(): bool
    {
        return ShoppingCart::where('user_id', auth()->id())
            ->where('product_id', $this->id)
            ->exists();
    }
}
