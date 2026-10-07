<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', fn () => redirect()->route(match (auth()->user()->role) {
        'owner' => 'dashboard', 'counter' => 'sales.create', default => 'deliveries.index',
    }))->name('home');

    Route::get('/dashboard', DashboardController::class)->middleware('role:owner')->name('dashboard');

    Route::middleware('role:owner,counter')->group(function () {
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::post('/deliveries', [DeliveryController::class, 'store'])->name('deliveries.store');
    });

    Route::get('/deliveries', [DeliveryController::class, 'index'])->middleware('role:owner,counter,delivery')->name('deliveries.index');
    Route::post('/deliveries/{delivery}/assign', [DeliveryController::class, 'assign'])->middleware('role:owner')->name('deliveries.assign');
    Route::post('/deliveries/{delivery}/status', [DeliveryController::class, 'status'])->middleware('role:owner,delivery')->name('deliveries.status');
});
