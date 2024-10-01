<?php

use App\Models\{Product, ProductImage};

it('belongs to a product', function () {
    $product = Product::factory()->create();
    $productImage = ProductImage::factory()->create(['product_id' => $product->id]);

    expect($productImage->product)->toBeInstanceOf(Product::class);
});

it('has correct fillable attributes', function () {
    $productImage = new ProductImage;

    expect($productImage->getFillable())->toBe([
        'product_id',
        'image_path',
        'alt_text',
        "resized_image_path",
        "show_image_path",
        "thumbnail_image_path",
    ]);
});

it('has correct casts', function () {
    $productImage = new ProductImage;

    expect($productImage->getCasts())->toHaveKey('created_at', 'datetime');
    expect($productImage->getCasts())->toHaveKey('updated_at', 'datetime');
});

it('can be created', function () {
    $productImage = ProductImage::create([
        'product_id' => Product::factory()->create()->id,
        'image_path' => 'images/example.jpg',
        'alt_text' => 'Example Image',
        'resized_image_path' => 'images/resized_example.jpg',
        "show_image_path" => "images/show_example.jpg",
        "thumbnail_image_path" => "images/thumbnail_example.jpg",
    ]);

    $this->assertDatabaseHas('product_images', [
        'image_path' => 'images/example.jpg',
        'alt_text' => 'Example Image',
        'resized_image_path' => 'images/resized_example.jpg',
        "show_image_path" => "images/show_example.jpg",
        "thumbnail_image_path" => "images/thumbnail_example.jpg",
    ]);
});

it('can update attributes', function () {
    $productImage = ProductImage::factory()->create();

    $productImage->update(['alt_text' => 'Updated Alt Text']);

    $this->assertDatabaseHas('product_images', [
        'id' => $productImage->id,
        'alt_text' => 'Updated Alt Text',
    ]);
});
