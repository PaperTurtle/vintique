<?php

use App\Models\{Product, Transaction, User};

it('belongs to a buyer', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create(['buyer_id' => $user->id]);

    expect($transaction->buyer)->toBeInstanceOf(User::class);
});

it('belongs to a product', function () {
    $product = Product::factory()->create();
    $transaction = Transaction::factory()->create(['product_id' => $product->id]);

    expect($transaction->product)->toBeInstanceOf(Product::class);
});

it('has correct fillable attributes', function () {
    $transaction = new Transaction;

    expect($transaction->getFillable())->toBe([
        'buyer_id',
        'product_id',
        'quantity',
        'total_price',
        'status',
    ]);
});

it('has correct casts', function () {
    $transaction = new Transaction;

    expect($transaction->getCasts())->toHaveKey('total_price', 'decimal:2');
    expect($transaction->getCasts())->toHaveKey('created_at', 'datetime');
    expect($transaction->getCasts())->toHaveKey('updated_at', 'datetime');
});

it('can be created', function () {
    $transaction = Transaction::factory()->create([
        'quantity' => 2,
        'total_price' => 200.00,
        'status' => 'completed',
    ]);

    $this->assertDatabaseHas('transactions', [
        'quantity' => 2,
        'total_price' => 200.00,
        'status' => 'completed',
    ]);
});

it('can update attributes', function () {
    $transaction = Transaction::factory()->create();

    $transaction->update(['status' => 'cancelled']);

    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'status' => 'cancelled',
    ]);
});
