<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [HomeController::class, 'home'])->name('home');

Route::prefix('/cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'view'])->name('view');
    Route::post('/', [CartController::class, 'add'])->name('add');
    Route::put('/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('/{product}', [CartController::class, 'delete'])->name('delete');
});

Route::prefix('/products')->name('product.')->group(function () {
    Route::get('/', [ProductController::class, 'list'])->name('list');
    Route::post('/', [ProductController::class, 'create'])->name('create');
    Route::get('/{product}', [ProductController::class, 'view'])->name('view');
    Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [ProductController::class, 'delete'])->name('delete');
});

Route::prefix('/')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
