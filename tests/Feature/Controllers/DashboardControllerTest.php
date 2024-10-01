<?php

use App\Models\{Product, Transaction, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('markAsSent successfully marks a transaction as sent by owner artisan', function () {
    $ownerArtisan = User::factory()->create(['isArtisan' => true]);
    $product = Product::factory()->create(['artisan_id' => $ownerArtisan->id]);
    $transaction = Transaction::factory()->create(['product_id' => $product->id]);

    $response = $this->actingAs($ownerArtisan)->patch(route('dashboard.markAsSent', ['transaction' => $transaction->id]));

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Product marked as sent']);
});

test('markAsSent returns unauthorized for non-owner user or artisan', function () {
    $ownerArtisan = User::factory()->create(['isArtisan' => true]);
    $otherUser = User::factory()->create(['isArtisan' => false]);
    $otherArtisan = User::factory()->create(['isArtisan' => true]);
    $product = Product::factory()->create(['artisan_id' => $ownerArtisan->id]);
    $transaction = Transaction::factory()->create(['product_id' => $product->id]);

    // Non-owner user attempt
    $responseUser = $this->actingAs($otherUser)->patch(route('dashboard.markAsSent', ['transaction' => $transaction->id]));
    $responseUser->assertStatus(403);
    $responseUser->assertJson(['message' => 'Unauthorized action']);

    // Non-owner artisan attempt
    $responseArtisan = $this->actingAs($otherArtisan)->patch(route('dashboard.markAsSent', ['transaction' => $transaction->id]));
    $responseArtisan->assertStatus(403);
    $responseArtisan->assertJson(['message' => 'Unauthorized action']);
});
