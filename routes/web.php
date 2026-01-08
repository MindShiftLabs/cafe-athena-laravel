<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BaristaController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost']);
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerPost']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes (Protected)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management Routes
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Product Management Routes
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
    Route::post('/products/toggle-stock', [AdminController::class, 'toggleProductStock'])->name('products.toggle');

    // Order Management Routes
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::put('/orders/update', [AdminController::class, 'updateOrder'])->name('orders.update');

    // Settings Routes
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [AdminController::class, 'updateProfile'])->name('settings.update-profile');
    Route::put('/settings/password', [AdminController::class, 'changePassword'])->name('settings.change-password');
});

// Barista Routes (Protected)
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost']);
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerPost']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes (Protected)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management Routes
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Product Management Routes
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
    Route::post('/products/toggle-stock', [AdminController::class, 'toggleProductStock'])->name('products.toggle');

    // Order Management Routes
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::put('/orders/update', [AdminController::class, 'updateOrder'])->name('orders.update');

    // Settings Routes
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [AdminController::class, 'updateProfile'])->name('settings.update-profile');
    Route::put('/settings/password', [AdminController::class, 'changePassword'])->name('settings.change-password');
});

// Barista Routes (Protected)
Route::middleware(['auth'])->prefix('barista')->name('barista.')->group(function () {
    Route::get('/dashboard', [BaristaController::class, 'dashboard'])->name('dashboard');
    
    // Order Management Routes
    Route::get('/orders', [BaristaController::class, 'orders'])->name('orders');
    Route::put('/orders/update', [BaristaController::class, 'updateOrder'])->name('orders.update');
    Route::get('/orders/{id}/details', [BaristaController::class, 'getOrderDetails'])->name('orders.details');

    // Product Management Routes
    Route::get('/products', [BaristaController::class, 'products'])->name('products');
    Route::post('/products', [BaristaController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{id}', [BaristaController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [BaristaController::class, 'destroyProduct'])->name('products.destroy');
    Route::post('/products/toggle-stock', [BaristaController::class, 'toggleProductStock'])->name('products.toggle');

    // Settings Routes
    Route::get('/settings', [BaristaController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [BaristaController::class, 'updateProfile'])->name('settings.update-profile');
    Route::put('/settings/password', [BaristaController::class, 'changePassword'])->name('settings.change-password');
});

// Customer Routes (Protected)
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/history', [CustomerController::class, 'history'])->name('history');
    Route::get('/settings', [CustomerController::class, 'settings'])->name('settings');
    
    // Settings Actions
    Route::put('/settings/profile', [CustomerController::class, 'updateProfile'])->name('settings.update-profile');
    Route::put('/settings/password', [CustomerController::class, 'changePassword'])->name('settings.change-password');

    // API Routes for Dashboard/History
    Route::get('/api/products', [CustomerController::class, 'getProducts'])->name('products.api');
    Route::get('/api/orders', [CustomerController::class, 'getOrderHistory'])->name('orders.api');
    Route::get('/api/orders/{id}/details', [BaristaController::class, 'getOrderDetails'])->name('order.details'); // Reusing Barista logic for now
    Route::post('/api/orders/process', [CustomerController::class, 'processOrder'])->name('order.process');
    Route::post('/api/orders/update-status', [CustomerController::class, 'updateOrderStatus'])->name('order.update');
});