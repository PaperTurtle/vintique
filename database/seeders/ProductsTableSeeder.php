<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['artisan_id' => 1, 'category_id' => 1, 'name' => 'Handmade Wooden Bowl', 'description' => 'A beautifully carved wooden bowl that serves as a centerpiece.', 'price' => 59.99, 'quantity' => 10],
            ['artisan_id' => 1, 'category_id' => 2, 'name' => 'Elegant Ceramic Vase', 'description' => 'A handcrafted ceramic vase perfect for modern homes.', 'price' => 45.50, 'quantity' => 15],
            ['artisan_id' => 16, 'category_id' => 3, 'name' => 'Vintage Leather Wallet', 'description' => 'A durable and stylish vintage leather wallet.', 'price' => 29.99, 'quantity' => 20],
            ['artisan_id' => 16, 'category_id' => 4, 'name' => 'Handwoven Scarf', 'description' => 'A warm and fashionable handwoven scarf.', 'price' => 34.99, 'quantity' => 10],
            ['artisan_id' => 21, 'category_id' => 5, 'name' => 'Artisanal Scented Candles', 'description' => 'Soothing scented candles made with natural ingredients.', 'price' => 19.99, 'quantity' => 30],
            ['artisan_id' => 21, 'category_id' => 6, 'name' => 'Hand-painted Canvas Art', 'description' => 'Abstract canvas art, perfect for decorating any room.', 'price' => 89.99, 'quantity' => 5],
            ['artisan_id' => 27, 'category_id' => 7, 'name' => 'Personalized Jewelry Box', 'description' => 'An elegant, personalized jewelry box for your treasures.', 'price' => 49.99, 'quantity' => 12],
            ['artisan_id' => 27, 'category_id' => 8, 'name' => 'Custom Engraved Pen', 'description' => 'A custom engraved pen, ideal for gifts.', 'price' => 24.99, 'quantity' => 20],
        ];

        foreach ($products as $product) {
            Product::create([
                'artisan_id' => $product['artisan_id'],
                'category_id' => $product['category_id'],
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
            ]);
        }
    }
}
