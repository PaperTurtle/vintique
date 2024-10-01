<?php

use App\Models\{Product, Review, User};

it('stores a new review', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $reviewData = [
        'product_id' => $product->id,
        'rating' => 5,
        'review' => 'Great product!',
    ];

    $this->actingAs($user)
        ->postJson(route('reviews.store'), $reviewData)
        ->assertStatus(200)
        ->assertJson([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'review' => 'Great product!',
        ]);

    $this->assertDatabaseHas('reviews', [
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 5,
        'review' => 'Great product!',
    ]);
});

it('updates an existing review', function () {
    $user = User::factory()->create();
    $review = Review::factory()->create(['user_id' => $user->id]);
    $updatedReviewData = ['rating' => 4, 'review' => 'Updated review text.'];

    $this->actingAs($user)
        ->patchJson(route('reviews.update', $review), $updatedReviewData)
        ->assertStatus(200)
        ->assertJson([
            'rating' => 4,
            'review' => 'Updated review text.',
        ]);

    $this->assertDatabaseHas('reviews', [
        'id' => $review->id,
        'rating' => 4,
        'review' => 'Updated review text.',
    ]);
});
