<?php

namespace Database\Factories;

use App\Models\{Product, ProductImage};
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'image_path' => $this->faker->imageUrl(),
            'resized_image_path' => $this->faker->imageUrl(),
            'show_image_path' => $this->faker->imageUrl(),
            'thumbnail_image_path' => $this->faker->imageUrl(),
            'alt_text' => $this->faker->words(3, true),
        ];
    }
}
