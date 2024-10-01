<?php

use App\Models\{Category, Product, ProductImage, Review, User};

it('belongs to an artisan', function () {
    $artisan = User::factory()->create();
    $product = Product::factory()->create(['artisan_id' => $artisan->id]);

    expect($product->artisan)->toBeInstanceOf(User::class);
});

it('belongs to a category', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);

    expect($product->category)->toBeInstanceOf(Category::class);
});

it('has many images', function () {
    $product = Product::factory()->create();
    $image = ProductImage::factory()->create(['product_id' => $product->id]);

    expect($product->images)->toHaveCount(1);
    expect($product->images->first())->toBeInstanceOf(ProductImage::class);
});

it('has many reviews', function () {
    $product = Product::factory()->create();
    $review = Review::factory()->create(['product_id' => $product->id]);

    expect($product->reviews)->toHaveCount(1);
    expect($product->reviews->first())->toBeInstanceOf(Review::class);
});

it('has correct fillable attributes', function () {
    $product = new Product;

    expect($product->getFillable())->toBe([
        'artisan_id',
        'category_id',
        'name',
        'description',
        'price',
        'quantity',
    ]);
});

it('has correct casts', function () {
    $product = new Product;

    expect($product->getCasts())->toHaveKey('price', 'decimal:2');
    expect($product->getCasts())->toHaveKey('created_at', 'datetime');
    expect($product->getCasts())->toHaveKey('updated_at', 'datetime');
});

it('can check if user has reviewed', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $review = Review::factory()->create(['product_id' => $product->id, 'user_id' => $user->id]);

    expect($product->hasUserReviewed($user->id))->toBeTrue();
    expect($product->hasUserReviewed(99999))->toBeFalse();
});

it('can be created', function () {
    $product = Product::create([
        'artisan_id' => User::factory()->create()->id,
        'category_id' => Category::factory()->create()->id,
        'name' => 'Handmade Vase',
        'description' => 'A beautifully crafted vase.',
        'price' => 99.99,
        'quantity' => 10,
    ]);

    $this->assertDatabaseHas('products', [
        'name' => 'Handmade Vase',
        'description' => 'A beautifully crafted vase.',
        'price' => 99.99,
        'quantity' => 10,
    ]);
});

it('can be updated', function () {
    $product = Product::factory()->create();

    $product->update(['name' => 'Updated Name']);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Name',
    ]);
});
