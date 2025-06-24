<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [ProductController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
