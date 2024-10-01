<?php

namespace App\Providers;

use App\Models\ShoppingCart;
use Illuminate\Support\Facades\{Auth, View};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Register any application services. */
    public function register(): void {}

    /** Bootstrap any application services. */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $cartItemCount = ShoppingCart::where('user_id', Auth::id())
                    ->distinct('product_id')
                    ->count('product_id');
            } else {
                $cartItemCount = 0;
            }
            $view->with('cartItemCount', $cartItemCount);
        });
    }
}
