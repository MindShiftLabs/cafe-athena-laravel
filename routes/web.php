<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

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

// Placeholders for other roles (to prevent errors in home.blade.php)
Route::middleware(['auth'])->group(function() {
    Route::get('/barista/dashboard', function() { return 'Barista Dashboard (TODO)'; })->name('barista.dashboard');
    Route::get('/customer/dashboard', function() { return 'Customer Dashboard (TODO)'; })->name('customer.dashboard');
});