<?php

use App\Models\{Product, Review, User};

it('belongs to a product', function () {
    $product = Product::factory()->create();
    $review = Review::factory()->create(['product_id' => $product->id]);

    expect($review->product)->toBeInstanceOf(Product::class);
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $review = Review::factory()->create(['user_id' => $user->id]);

    expect($review->user)->toBeInstanceOf(User::class);
});

it('has correct fillable attributes', function () {
    $review = new Review;

    expect($review->getFillable())->toBe([
        'product_id',
        'user_id',
        'rating',
        'review',
    ]);
});

it('has correct casts', function () {
    $review = new Review;

    expect($review->getCasts())->toHaveKey('created_at', 'datetime');
    expect($review->getCasts())->toHaveKey('updated_at', 'datetime');
    expect($review->getCasts())->toHaveKey('rating', 'integer');
});

it('can be created', function () {
    $review = Review::create([
        'product_id' => Product::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'rating' => 5,
        'review' => 'Great product!',
    ]);

    $this->assertDatabaseHas('reviews', [
        'rating' => 5,
        'review' => 'Great product!',
    ]);
});

it('can update attributes', function () {
    $review = Review::factory()->create();

    $review->update(['rating' => 4, 'review' => 'Good, but could be better']);

    $this->assertDatabaseHas('reviews', [
        'id' => $review->id,
        'rating' => 4,
        'review' => 'Good, but could be better',
    ]);
});
