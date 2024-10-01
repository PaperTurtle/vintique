<?php

use App\Models\{Product, ShoppingCart, User};

it('belongs to a user', function () {
    $user = User::factory()->create();
    $cartItem = ShoppingCart::factory()->create(['user_id' => $user->id]);

    expect($cartItem->user)->toBeInstanceOf(User::class);
});

it('belongs to a product', function () {
    $product = Product::factory()->create();
    $cartItem = ShoppingCart::factory()->create(['product_id' => $product->id]);

    expect($cartItem->product)->toBeInstanceOf(Product::class);
});

it('has correct fillable attributes', function () {
    $cartItem = new ShoppingCart;

    expect($cartItem->getFillable())->toBe([
        'user_id',
        'product_id',
        'quantity',
    ]);
});

it('calculates total price correctly', function () {
    $product = Product::factory()->create(['price' => 100.00]);
    $cartItem = ShoppingCart::factory()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    expect($cartItem->total_price)->toBe(200.00);
});

it('can be created', function () {
    $cartItem = ShoppingCart::create([
        'user_id' => User::factory()->create()->id,
        'product_id' => Product::factory()->create()->id,
        'quantity' => 3,
    ]);

    $this->assertDatabaseHas('shopping_carts', [
        'quantity' => 3,
    ]);
});

it('can update quantity', function () {
    $cartItem = ShoppingCart::factory()->create();

    $cartItem->update(['quantity' => 5]);

    $this->assertDatabaseHas('shopping_carts', [
        'id' => $cartItem->id,
        'quantity' => 5,
    ]);
});
