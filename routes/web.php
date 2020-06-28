<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('/cart')->group(function () {
    Route::post('/', [CartController::class, 'add']);
    Route::put('/{product}', [CartController::class, 'update']);
    Route::delete('/{product}', [CartController::class, 'delete']);
});

Route::prefix('/products')->group(function () {
    Route::post('/', [ProductController::class, 'create']);
    Route::get('/{product}', [ProductController::class, 'view']);
    Route::put('/{product}', [ProductController::class, 'update']);
    Route::delete('/{product}', [ProductController::class, 'delete']);
});

Route::prefix('/')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
