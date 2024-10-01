<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Represents a product category in the application.
 *
 * @property int         $id          Unique identifier for the category.
 * @property string      $name        Name of the category.
 * @property string|null $description Description of the category. Can be null.
 * @property Carbon      $created_at  Timestamp when the category was created.
 * @property Carbon      $updated_at  Timestamp when the category was last updated.
 */
class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'description' => 'string',
    ];

    /**
     * Define the relationship with products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
