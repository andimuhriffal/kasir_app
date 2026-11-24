<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Halaman tamu (belum login).
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Root: arahkan ke dashboard (atau login jika belum login).
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard: admin + kasir.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Polling KPI realtime (hanya JSON, dipakai dashboard).
    Route::get('/dashboard/kpis', [DashboardController::class, 'kpis'])->name('dashboard.kpis');

    // Transaksi: admin + kasir (kasir hanya melihat pesanannya sendiri).
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    // Polling status realtime — harus di atas /orders/{order} agar tidak
    // tertangkap sebagai id pesanan.
    Route::get('/orders/statuses', [OrderController::class, 'statuses'])->name('orders.statuses');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::patch('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Admin only: master data + laporan.
    Route::middleware('role:admin')->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
        Route::get('/reports/laba-rugi', [ReportController::class, 'labaRugi'])->name('reports.laba-rugi');
        Route::get('/reports/laba-rugi/export-pdf', [ReportController::class, 'labaRugiPdf'])->name('reports.laba-rugi-pdf');
        Route::get('/reports/laba-rugi/export-csv', [ReportController::class, 'labaRugiCsv'])->name('reports.laba-rugi-csv');
    });
});
