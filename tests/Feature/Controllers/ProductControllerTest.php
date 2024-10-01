<?php

use App\Models\{Product, User};
use Illuminate\Http\UploadedFile;

it('stores a new product', function () {
    $this->actingAs(User::factory()->create(['isArtisan' => true]));
    $productData = Product::factory()->make()->toArray();
    $fakeImage = UploadedFile::fake()->image('product.jpg');
    $productData['images'] = [$fakeImage];
    $response = $this->post(route('products.store'), $productData);
    $createdProduct = Product::latest('id')->first();
    $response->assertRedirect(route('products.show', $createdProduct->id));
    unset($productData['images']);
    $this->assertDatabaseHas('products', $productData);
});

it('updates a product', function () {
    $user = User::factory()->create(['isArtisan' => true]);
    $product = Product::factory()->create(['artisan_id' => $user->id]);
    $this->actingAs($user);
    $updatedData = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
        'price' => '10.00',
        'quantity' => '10',
    ];
    $response = $this->put(route('products.update', $product), $updatedData);
    $response->assertRedirect(route('products.show', $product));
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        ...$updatedData,
    ]);
});

it('deletes a product', function () {
    $user = User::factory()->create(['isArtisan' => true]);
    $this->actingAs($user);
    $product = Product::factory()->create(['artisan_id' => $user->id]);
    $response = $this->delete(route('products.destroy', $product));
    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
