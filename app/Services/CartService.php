<?php

namespace App\Services;

use App\Models\{Product, ShoppingCart};

/**
 * CartService is responsible for managing the shopping cart functionality.
 * It offers methods to add products to the shopping cart.
 */
class CartService
{
    /**
     * Adds a product to the shopping cart or updates its quantity if it already exists.
     * It checks if the product is already in the cart for the current user.
     * If it is, it updates the quantity; if not, it creates a new cart item.
     *
     * @param  int   $productId the ID of the product to add to the cart
     * @param  int   $quantity  the quantity of the product to add
     * @return array returns a success message upon successful addition
     */
    public function addToCart(int $productId, int $quantity): array
    {
        $product = Product::findOrFail($productId);

        $cartItem = auth()->user()->cart()->where('product_id', $productId)->first();

        $isNewItem = false;

        if ($cartItem) {
            // Check if adding the quantity exceeds the available quantity
            if (($cartItem->quantity + $quantity) > $product->quantity) {
                return ['error' => 'Cannot add more of this item to the cart'];
            }
            $cartItem->quantity += $quantity;
        } else {
            // Check if the requested quantity is more than available
            if ($quantity > $product->quantity) {
                return ['error' => 'Requested quantity exceeds available stock'];
            }
            $cartItem = auth()->user()->cart()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
            $isNewItem = true;
        }

        $cartItem->save();

        return ['success' => 'Product added to cart!', 'isNewItem' => $isNewItem];
    }
}
