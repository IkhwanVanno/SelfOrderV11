<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsidebarController;
use App\Http\Controllers\AsidebarController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\AuthCheck;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Midtrans Callback (tidak perlu authentication untuk webhook)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Protected Routes
Route::middleware([AuthCheck::class])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    // User routes
    Route::get('/food', [UsidebarController::class, 'food'])->name('food');
    Route::get('/drink', [UsidebarController::class, 'drink'])->name('drink');
    Route::get('/snack', [UsidebarController::class, 'snack'])->name('snack');
    Route::get('/cart', [UsidebarController::class, 'cart'])->name('cart');
    
    // Cart API routes
    Route::post('/cart/add', [UsidebarController::class, 'addToCart'])->name('cart.add');
    Route::put('/cart/update/{item}', [UsidebarController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove/{item}', [UsidebarController::class, 'removeFromCart'])->name('cart.remove');
    Route::put('/cart/update-product/{productId}', [UsidebarController::class, 'updateCartByProduct'])->name('cart.update.product');
    Route::delete('/cart/remove-product/{productId}', [UsidebarController::class, 'removeFromCartByProduct'])->name('cart.remove.product');
    
    // Payment routes
    Route::post('/payment/create', [PaymentController::class, 'createTransaction'])->name('payment.create');
    Route::get('/payment/status/{transactionId}', [PaymentController::class, 'checkStatus'])->name('payment.status');

    // Admin only routes
    Route::middleware([AuthCheck::class . ':admin'])->group(function () {
        // Order Management
        Route::get('/order', [AsidebarController::class, 'order'])->name('order');

        // Payment Management
        Route::get('/payment', [AsidebarController::class, 'payment'])->name('payment');
        Route::get('/payment/{id}/details', [AsidebarController::class, 'paymentDetails'])->name('payment.details');
        
        // Product Management
        Route::get('/product', [AsidebarController::class, 'product'])->name('product');
        Route::post('/product', [AsidebarController::class, 'storeProduct'])->name('product.store');
        Route::put('/product/{id}', [AsidebarController::class, 'updateProduct'])->name('product.update');
        Route::delete('/product/{id}', [AsidebarController::class, 'deleteProduct'])->name('product.delete');
        
        // User Management
        Route::get('/user', [AsidebarController::class, 'user'])->name('user');
        Route::post('/user', [AsidebarController::class, 'storeUser'])->name('user.store');
        Route::put('/user/{id}', [AsidebarController::class, 'updateUser'])->name('user.update');
        Route::delete('/user/{id}', [AsidebarController::class, 'deleteUser'])->name('user.delete');
    });
});