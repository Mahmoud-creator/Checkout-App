<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [ProductController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{item}', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
Route::delete('/cart/{item}', [CartController::class, 'removeItem'])->name('cart.removeItem');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
