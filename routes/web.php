<?php

use App\Http\Controllers\{CheckoutController, ContactController, DashboardController, ProductController, ProfileController, ReviewController};
use App\Http\Middleware\{EnsureCartIsNotEmpty, EnsureUserIsArtisan, RedirectIfNoTransactionDetails};
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Registration of web routes for the application. These routes are loaded
| by the RouteServiceProvider within a group that contains the "web" middleware group.
| Create something great!
|
*/

// ========= Public Routes =========
// Home page
Route::get('/', [ProductController::class, 'topRatedProducts'])->name('home');

// Static pages

Route::view('about-us', 'about-us')->name('about-us');
Route::view('jobs', 'jobs')->name('jobs');
Route::view('accessibility', 'accessibility')->name('accessibility');
Route::view('partners', 'partners')->name('partners');

Route::prefix("contact-us")->group(function () {
    Route::get('/', function () {
        return \view('contact-us');
    })->name('contact-us');
    Route::post('/', [ContactController::class, "sendEmail"]);
})->name('contact-us');

// Profile routes (publicly accessible)
Route::get('profile/{userID}', [ProfileController::class, 'show'])->name('profile.show')
    ->where('userID', '[0-9]+');

// Product routes
Route::prefix('products')->group(function () {
    // Publicly accessible product routes
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{product}', [ProductController::class, 'show'])
        ->where('product', '[0-9]+')->name('products.show');

    // Routes requiring authentication
    Route::middleware('auth')->group(function () {
        Route::get('/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::delete('/{product}/images/{productImage}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
    });
});

Route::view("/faq", "faq.index")->name("faq");

// ========= Authentication Required Routes =========
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::prefix("dashboard")->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware(EnsureUserIsArtisan::class);
        Route::patch('/transactions/{transaction}/mark-as-sent', [DashboardController::class, 'markAsSent'])
            ->name('dashboard.markAsSent')->middleware(EnsureUserIsArtisan::class);
    });

    // Cart routes
    Route::prefix("cart")->group(function () {
        Route::post('/', [CheckoutController::class, 'addToCart'])->name('cart.add');
        Route::delete('/{cartItem}', [CheckoutController::class, 'removeFromCart'])->name('cart.remove');
        Route::patch('/update/{itemId}', [CheckoutController::class, 'updateCart'])->name('cart.update');
    });

    // Checkout process
    Route::prefix("checkout")->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::get('/process', [CheckoutController::class, 'process'])->name('checkout.process')->middleware(EnsureCartIsNotEmpty::class);
        Route::post('/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');

        Route::view('/success', 'checkout.success')->name('checkout.success')->middleware(RedirectIfNoTransactionDetails::class);
    });

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/{userID}/edit', [ProfileController::class, 'edit'])->name('profile.edit')
            ->where('userID', '[0-9]+');
        Route::patch('/{userID}', [ProfileController::class, 'update'])->name('profile.update')
            ->where('userID', '[0-9]+');
        Route::delete('/{userID}', [ProfileController::class, 'destroy'])->name('profile.destroy')
            ->where('userID', '[0-9]+');
    });

    // Review routes
    Route::prefix("reviews")->group(function () {
        Route::post('/', [ReviewController::class, 'store'])->name('reviews.store');
        Route::patch('/{review}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    Route::prefix("email")->group(function () {
        Route::get('/verify', function () {
            return \view('auth.verify-email');
        })->middleware('auth')->name('verification.notice');

        Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return \redirect('/dashboard');
        })->middleware(['auth', 'signed'])->name('verification.verify');

        Route::post('/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();

            return \back()->with('message', 'Verification link sent!');
        })->middleware(['auth', 'throttle:6,1'])->name('verification.send');
    });
});

Route::fallback(function () {
    return \redirect('/')->with('error', 'The requested page is not available.');
});

// ========= Authentication Routes (Laravel Breeze) =========
require __DIR__ . '/auth.php';
