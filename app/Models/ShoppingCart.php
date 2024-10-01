<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingCart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    /**
     * Define the relationship with the user who owns the cart item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship with the product in the cart.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor to calculate the total price of the cart item.
     */
    public function getTotalPriceAttribute(): float|int
    {
        return $this->quantity * $this->product->price;
    }

    /**
     * Check if the shopping cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->where('user_id', $this->user_id)->count() == 0;
    }
}
