<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', fn () => redirect()->route(match (Auth::user()?->role) {
        'owner' => 'dashboard',
        'counter' => 'sales.create',
        default => 'deliveries.index',
    }))->name('home');

    // Owner only: overview, reports, catalogue, stock items, accounts.
    Route::middleware('role:owner')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/reports/export', [ReportController::class, 'export'])
            ->name('reports.export');

        Route::resource('products', ProductController::class)
            ->except(['show', 'destroy']);

        Route::resource('items', InventoryItemController::class)
            ->except(['index', 'show', 'destroy']);

        Route::resource('users', UserController::class)
            ->except(['show', 'destroy']);

        Route::post('/deliveries/{delivery}/assign', [DeliveryController::class, 'assign'])
            ->name('deliveries.assign');
    });

    // Owner and counter staff: day-to-day work.
    Route::middleware('role:owner,counter')->group(function () {
        Route::get('/sales', [SaleController::class, 'index'])
            ->name('sales.index');

        Route::get('/sales/create', [SaleController::class, 'create'])
            ->name('sales.create');

        Route::post('/sales', [SaleController::class, 'store'])
            ->name('sales.store');

        Route::get('/sales/{sale}', [SaleController::class, 'show'])
            ->name('sales.show');

        Route::post('/sales/{sale}/paid', [SaleController::class, 'markPaid'])
            ->name('sales.paid');

        Route::resource('customers', CustomerController::class)
            ->except(['show', 'destroy']);

        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
            ->name('customers.destroy');

        Route::get('/inventory', [InventoryController::class, 'index'])
            ->name('inventory.index');

        Route::get('/inventory/movements', [InventoryController::class, 'movements'])
            ->name('inventory.movements');

        Route::get('/inventory/adjust', [InventoryController::class, 'adjust'])
            ->name('inventory.adjust');

        Route::post('/inventory/adjust', [InventoryController::class, 'storeAdjustment'])
            ->name('inventory.adjust.store');

        Route::post('/deliveries', [DeliveryController::class, 'store'])
            ->name('deliveries.store');
    });

    // Everyone signed in: deliveries.
    // Riders only see their own deliveries.
    Route::get('/deliveries', [DeliveryController::class, 'index'])
        ->middleware('role:owner,counter,delivery')
        ->name('deliveries.index');

    Route::post('/deliveries/{delivery}/status', [DeliveryController::class, 'status'])
        ->middleware('role:owner,delivery')
        ->name('deliveries.status');
});