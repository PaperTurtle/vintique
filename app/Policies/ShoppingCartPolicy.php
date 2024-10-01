<?php

namespace App\Policies;

use App\Models\{ShoppingCart, User};
use Illuminate\Auth\Access\HandlesAuthorization;

class ShoppingCartPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the shopping cart item.
     */
    public function view(User $user, ShoppingCart $shoppingCart): bool
    {
        return $user->id === $shoppingCart->user_id;
    }

    /**
     * Determine whether the user can add an item to the shopping cart.
     */
    public function addToCart(User $user): bool
    {
        return (bool) $user;
    }

    /**
     * Determine whether the user can update an item in the shopping cart.
     */
    public function update(User $user, ShoppingCart $shoppingCart): bool
    {
        return $user->id === $shoppingCart->user_id;
    }

    /**
     * Determine whether the user can remove an item from the shopping cart.
     */
    public function removeFromCart(User $user, ShoppingCart $shoppingCart): bool
    {
        return $user->id === $shoppingCart->user_id;
    }
}
