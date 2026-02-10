<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Dashboard route
Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Product and Category management routes
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);

    // Export routes
    Route::get('/products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    Route::get('/categories/export/csv', [CategoryController::class, 'exportCsv'])->name('categories.export.csv');
    Route::get('/categories/export/pdf', [CategoryController::class, 'exportPdf'])->name('categories.export.pdf');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
