<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Middleware\AdminMiddleware;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'register'])->name('register');
    Route::post('register', [AuthController::class, 'registerStore'])->name('register.post');
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'loginStore'])->name('login.post');
    Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('forgot-password', [AuthController::class, 'forgotPasswordStore'])->name('forgot-password.store');
    Route::get('reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPasswordStore'])->name('password.store');
});

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/books', [BooksController::class, 'index'])->name('books.index');
Route::get('/books/{slug}', [BooksController::class, 'show'])->name('books.show');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    // User account routes
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        // Profile pages
        Route::get('my-account', [UserController::class, 'userProfile'])->name('my.account');
        Route::post('update-profile', [UserController::class, 'updateProfile'])->name('update.profile');
        Route::post('update-password', [UserController::class, 'updatePassword'])->name('update.password');
        Route::post('update-avatar', [UserController::class, 'updateAvatar'])->name('update.avatar');

        // Cart routes
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{book}', [CartController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/update/{cart}', [CartController::class, 'updateQuantity'])->name('cart.update');
        Route::delete('/cart/remove/{cart}', [CartController::class, 'removeFromCart'])->name('cart.remove');

        // Order routes
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

        // Review routes
        Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

        // Wishlist routes
        Route::get('/wishlist', [FavoriteController::class, 'index'])->name('wishlist.index');
        Route::post('/wishlist/toggle/{book}', [FavoriteController::class, 'toggle'])->name('wishlist.toggle');
        Route::delete('/wishlist/{book}', [FavoriteController::class, 'destroy'])->name('wishlist.destroy');
    });
});

// Admin routes
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('books', AdminBookController::class);
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
});
