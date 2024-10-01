<?php

use App\Models\{Category, Product};

it('has many products', function () {
    $category = Category::factory()->create();

    $product = Product::factory()->create(['category_id' => $category->id]);

    expect($category->products)->toHaveCount(1);
    expect($category->products->first())->toBeInstanceOf(Product::class);
});

it('has correct fillable attributes', function () {
    $category = new Category;

    expect($category->getFillable())->toBe(['name', 'description']);
});

it('has correct casts', function () {
    $category = new Category;

    expect($category->getCasts())->toHaveKey('created_at', 'datetime');
    expect($category->getCasts())->toHaveKey('updated_at', 'datetime');
});

it('can be created', function () {
    $category = Category::create([
        'name' => 'Electronics',
        'description' => 'Electronic items',
    ]);

    $this->assertDatabaseHas('categories', [
        'name' => 'Electronics',
        'description' => 'Electronic items',
    ]);
});

it('can be updated', function () {
    $category = Category::factory()->create();

    $category->update(['name' => 'Updated Name']);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Name',
    ]);
});

test('category can be created without a description', function () {
    $category = new Category([
        'name' => 'Test Category',
    ]);

    expect($category->name)->toBe('Test Category');
    expect($category->description)->toBeNull();
});
