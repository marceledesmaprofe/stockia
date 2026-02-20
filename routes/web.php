<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Dashboard route
Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Product, Category and Customer management routes
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');

    // Sales routes
    Route::resource('sales', SaleController::class);
    Route::patch('/sales/{id}/mark-as-paid', [SaleController::class, 'markAsPaid'])->name('sales.mark-as-paid');
    Route::patch('/sales/{id}/annul', [SaleController::class, 'annul'])->name('sales.annul');

    // Purchases routes
    Route::resource('purchases', PurchaseController::class);
    Route::patch('/purchases/{id}/mark-as-paid', [PurchaseController::class, 'markAsPaid'])->name('purchases.mark-as-paid');
    Route::patch('/purchases/{id}/annul', [PurchaseController::class, 'annul'])->name('purchases.annul');

    // Stock Adjustments routes
    Route::resource('stock-adjustments', StockAdjustmentController::class);
    Route::patch('/stock-adjustments/{id}/annul', [StockAdjustmentController::class, 'annul'])->name('stock-adjustments.annul');

    // Stock Movements History (read-only for audit)
    Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('/stock-movements/{id}', [StockMovementController::class, 'show'])->name('stock-movements.show');

    // Export routes
    Route::get('/products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    Route::get('/categories/export/csv', [CategoryController::class, 'exportCsv'])->name('categories.export.csv');
    Route::get('/categories/export/pdf', [CategoryController::class, 'exportPdf'])->name('categories.export.pdf');
    Route::get('/customers/export/csv', [CustomerController::class, 'exportCsv'])->name('customers.export.csv');
    Route::get('/customers/export/pdf', [CustomerController::class, 'exportPdf'])->name('customers.export.pdf');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
