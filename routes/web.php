<?php

use App\Http\Controllers\Web\Admin\AdminAuthController;
use App\Http\Controllers\Web\Admin\AdminProfileController;
use App\Http\Controllers\Web\Admin\CategoryWebController;
use App\Http\Controllers\Web\Admin\ContactWebController;
use App\Http\Controllers\Web\Admin\HomeController;
use App\Http\Controllers\Web\Admin\OrderWebController;
use App\Http\Controllers\Web\Admin\ProductWebController;
use App\Http\Controllers\Web\Admin\ReviewWebController;
use App\Http\Controllers\Web\Admin\UserWebController;
use App\Http\Controllers\Web\Customer\CustomerAuthController;
use App\Http\Controllers\Web\Customer\CustomerContactController;
use App\Http\Controllers\Web\Customer\CustomerProfileController;
use App\Http\Controllers\Web\Customer\CustomerWebController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/customer');

Route::prefix('customer')->name('customer.')->group(function (): void {
    Route::get('login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [CustomerAuthController::class, 'login'])->name('login.submit');
    Route::get('register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [CustomerAuthController::class, 'register'])->name('register.submit');
    Route::get('forgot-password', [CustomerAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [CustomerAuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::get('reset-password/{token?}', [CustomerAuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('reset-password', [CustomerAuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/', [CustomerWebController::class, 'index'])->name('home');
    Route::get('about', [CustomerWebController::class, 'about'])->name('about');
    Route::get('contact', [CustomerContactController::class, 'show'])->name('contact');

    Route::get('products', [CustomerWebController::class, 'products'])->name('products.index');
    Route::get('products/{product}', [CustomerWebController::class, 'showProduct'])->name('products.show');

    Route::get('cart', [CustomerWebController::class, 'cart'])->name('cart.show');
    Route::post('cart/{product}', [CustomerWebController::class, 'addToCart'])->name('cart.add');
    Route::patch('cart/{product}', [CustomerWebController::class, 'updateCartItem'])->name('cart.update');
    Route::delete('cart/{product}', [CustomerWebController::class, 'removeCartItem'])->name('cart.remove');

    Route::middleware('customer.auth')->group(function (): void {
        Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');

        Route::post('contact', [CustomerContactController::class, 'store'])->name('contact.store');

        Route::get('checkout', [CustomerWebController::class, 'checkout'])->name('checkout.show');
        Route::post('checkout', [CustomerWebController::class, 'placeOrder'])->name('checkout.store');

        Route::post('products/{product}/reviews', [CustomerWebController::class, 'storeReview'])->name('products.reviews.store');

        Route::get('orders', [CustomerWebController::class, 'orders'])->name('orders.index');
        Route::get('orders/{order}', [CustomerWebController::class, 'showOrder'])->name('orders.show');

        Route::get('profile', [CustomerProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [CustomerProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('profile/password', [CustomerProfileController::class, 'updatePassword'])->name('profile.password.update');
    });
});

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware('admin.auth')->group(function (): void {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [AdminProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::delete('products/{product}/main-image', [ProductWebController::class, 'destroyMainImage'])->name('products.main-image.destroy');
        Route::delete('products/{product}/gallery-images', [ProductWebController::class, 'destroyGalleryImages'])->name('products.gallery-images.destroy');
        Route::resource('products', ProductWebController::class)->names('products');
        Route::resource('categories', CategoryWebController::class)->names('categories');
        Route::resource('orders', OrderWebController::class)->except(['create', 'store'])->names('orders');
        Route::patch('orders/{order}/status', [OrderWebController::class, 'updateStatus'])->name('orders.update-status');
        Route::resource('reviews', ReviewWebController::class)->only(['index', 'show'])->names('reviews');
        Route::get('contacts', [ContactWebController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [ContactWebController::class, 'show'])->name('contacts.show');
        Route::put('contacts/{contact}/reply', [ContactWebController::class, 'reply'])->name('contacts.reply');
        Route::resource('users', UserWebController::class)->names('users');
    });
});
