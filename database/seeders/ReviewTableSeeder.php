<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewTableSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            ['product_id' => 9, 'user_id' => 12, 'rating' => 5, 'review' => 'The quality exceeded my expectations! Absolutely love it.'],
            ['product_id' => 9, 'user_id' => 13, 'rating' => 4, 'review' => 'Beautifully made, but the color was a bit different from the picture.'],
            ['product_id' => 9, 'user_id' => 14, 'rating' => 3, 'review' => 'Good craftsmanship, but the shipping was delayed.'],
            ['product_id' => 10, 'user_id' => 15, 'rating' => 5, 'review' => 'This is now a centerpiece in my home. So happy with the purchase!'],
            ['product_id' => 10, 'user_id' => 16, 'rating' => 4, 'review' => 'Lovely item, though a bit pricey.'],
            ['product_id' => 10, 'user_id' => 17, 'rating' => 2, 'review' => 'Not as sturdy as I expected, but it looks nice.'],
            ['product_id' => 11, 'user_id' => 18, 'rating' => 5, 'review' => 'Exceptional quality and detail. I’m thoroughly impressed!'],
            ['product_id' => 11, 'user_id' => 19, 'rating' => 4, 'review' => 'Beautiful work, but the delivery took longer than expected.'],
            ['product_id' => 11, 'user_id' => 20, 'rating' => 3, 'review' => 'Nice, but not exactly what I was expecting based on the photos.'],
            ['product_id' => 12, 'user_id' => 21, 'rating' => 5, 'review' => 'Absolutely perfect! I couldn’t be happier with this purchase.'],
            ['product_id' => 12, 'user_id' => 22, 'rating' => 4, 'review' => 'Great product, though the color is slightly off from the website image.'],
            ['product_id' => 12, 'user_id' => 23, 'rating' => 2, 'review' => 'Arrived damaged, but customer service was helpful in resolving the issue.'],
            ['product_id' => 13, 'user_id' => 24, 'rating' => 5, 'review' => 'Superb quality! It has become my favorite piece in the house.'],
            ['product_id' => 13, 'user_id' => 25, 'rating' => 4, 'review' => 'Looks great, but the size was a bit smaller than I expected.'],
            ['product_id' => 13, 'user_id' => 26, 'rating' => 3, 'review' => 'Decent product, but I found it a bit overpriced for the quality.'],
            ['product_id' => 14, 'user_id' => 27, 'rating' => 5, 'review' => 'I am extremely pleased with this purchase. Highly recommend!'],
            ['product_id' => 14, 'user_id' => 28, 'rating' => 4, 'review' => 'Very good craftsmanship, though the packaging was not great.'],
            ['product_id' => 14, 'user_id' => 29, 'rating' => 2, 'review' => 'The product is okay, but it did not meet my expectations.'],
            ['product_id' => 15, 'user_id' => 29, 'rating' => 5, 'review' => 'Truly a masterpiece! The craftsmanship is top-notch.'],
            ['product_id' => 15, 'user_id' => 30, 'rating' => 4, 'review' => 'Very unique and well-made, but took a while to arrive.'],
            ['product_id' => 15, 'user_id' => 31, 'rating' => 1, 'review' => 'Not what I expected, and the material feels a bit cheap.'],
            ['product_id' => 16, 'user_id' => 30, 'rating' => 5, 'review' => 'Stunning design and excellent quality. I highly recommend it.'],
            ['product_id' => 16, 'user_id' => 31, 'rating' => 4, 'review' => 'Great product, but the packaging could be improved.'],
        ];

        // Create each review in the database
        foreach ($reviews as $review) {
            Review::create([
                'product_id' => $review['product_id'],
                'user_id' => $review['user_id'],
                'rating' => $review['rating'],
                'review' => $review['review'],
            ]);
        }
    }
}
