<?php

use App\Http\Controllers\{CheckoutController, ContactController, DashboardController, ProductController, ProfileController, ReviewController};
use App\Http\Middleware\{EnsureCartIsNotEmpty, EnsureUserIsArtisan, RedirectIfNoTransactionDetails};
use App\Models\Product;
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
Route::get('/', function () {
    $topRatedProducts = Product::with('reviews')
        ->withAvg('reviews', 'rating')
        ->orderByDesc('reviews_avg_rating')
        ->take(3)
        ->get();
    return view('welcome', compact("topRatedProducts"));
})->name('home');

// Static pages

Route::view('about-us', 'about-us')->name('about-us');
Route::view('jobs', 'jobs')->name('jobs');
Route::view('accessibility', 'accessibility')->name('accessibility');
Route::view('partners', 'partners')->name('partners');
Route::view("faq", "faq")->name("faq");

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
Route::prefix('products')->controller(ProductController::class)->name("products.")->group(function () {
    Route::resource('/', ProductController::class)->only(['index', 'show']);

    Route::resource('/', ProductController::class)->except(['index', 'show'])->middleware("auth");
});

// ========= Authentication Required Routes =========
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::prefix("dashboard")->controller(DashboardController::class)->middleware(EnsureUserIsArtisan::class)->name("dashboard.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('/transactions/{transaction}/mark-as-sent', 'markAsSent')
            ->name('markAsSent');
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
