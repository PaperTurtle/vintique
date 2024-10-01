<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Handmade Crafts', 'description' => 'Beautifully crafted handmade items.'],
            ['name' => 'Jewelry & Accessories', 'description' => 'Handcrafted jewelry and accessories.'],
            ['name' => 'Home & Living', 'description' => 'Handmade home decor and living products.'],
            ['name' => 'Art & Collectibles', 'description' => 'Unique art pieces and collectibles.'],
            ['name' => 'Clothing & Shoes', 'description' => 'Handmade clothing and shoes for all ages.'],
            ['name' => 'Wedding & Party', 'description' => 'Personalized items for weddings and parties.'],
            ['name' => 'Toys & Entertainment', 'description' => 'Handcrafted toys and entertainment products.'],
            ['name' => 'Vintage', 'description' => 'Vintage items and retro finds.'],
            ['name' => 'Bath & Beauty', 'description' => 'Handmade beauty and bath products.'],
        ];

        // Create each category in the database
        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
            ]);
        }
    }
}
