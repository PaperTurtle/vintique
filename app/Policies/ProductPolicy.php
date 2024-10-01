<?php

namespace App\Policies;

use App\Models\{Product, ProductImage, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for product-related actions.
 *
 * This class defines the authorization rules for various actions related to products and product images.
 * It determines whether a user is allowed to perform specific actions on product records and their images.
 */
class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create product models.
     *
     * @param  User $user the authenticated user
     * @return bool true if the user can create products; otherwise, false
     */
    public function create(User $user): bool
    {
        return $user->isArtisan;
    }

    /**
     * Determine whether the user can update the specific product model.
     *
     * @param  User    $user    the authenticated user
     * @param  Product $product the product to update
     * @return bool    true if the user can update the product; otherwise, false
     */
    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->artisan_id;
    }

    /**
     * Determine whether the user can delete product models.
     *
     * @param  User $user the authenticated user
     * @return bool true if the user can delete products; otherwise, false
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->artisan_id;
    }

    /**
     * Determine whether the user can delete the specific product image.
     *
     * @param  User         $user         the authenticated user
     * @param  Product      $product      the product that owns the image
     * @param  ProductImage $productImage the product image to delete
     * @return bool         true if the user can delete the image; otherwise, false
     */
    public function deleteImage(User $user, Product $product, ProductImage $productImage): bool
    {
        return $user->id === $product->artisan_id && $productImage->product_id === $product->id;
    }

    /**
     * Determine whether the user can edit the specific product model.
     *
     * @param  User    $user    the authenticated user
     * @param  Product $product the product to edit
     * @return bool    true if the user can edit the product; otherwise, false
     */
    public function edit(User $user, Product $product): bool
    {
        return $user->id === $product->artisan_id;
    }
}
