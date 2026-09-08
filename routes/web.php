<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\DetailDataController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RbacController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;

// Auth Guest Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Multi-role Adaptive Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Impersonation Routes
    Route::match(['get', 'post'], '/impersonate/revert', [ImpersonationController::class, 'revert'])->name('impersonate.revert');
    Route::post('/impersonate/{user}', [ImpersonationController::class, 'start'])->name('impersonate.start');

    // Master Data Management
    Route::middleware(['permission:master,read'])->group(function () {
        Route::get('/master-data', [MasterDataController::class, 'index'])->name('master.index');
        Route::post('/master-data/office', [MasterDataController::class, 'storeOffice'])->middleware('permission:master,create')->name('master.office.store');
        Route::post('/master-data/branch', [MasterDataController::class, 'storeBranch'])->middleware('permission:master,create')->name('master.branch.store');
        Route::post('/master-data/warehouse', [MasterDataController::class, 'storeWarehouse'])->middleware('permission:master,create')->name('master.warehouse.store');
        Route::post('/master-data/rack', [MasterDataController::class, 'storeRack'])->middleware('permission:master,create')->name('master.rack.store');
        Route::post('/master-data/bin', [MasterDataController::class, 'storeBin'])->middleware('permission:master,create')->name('master.bin.store');
        Route::post('/master-data/product', [MasterDataController::class, 'storeProduct'])->middleware('permission:master,create')->name('master.product.store');
        Route::put('/master-data/product/{id}', [MasterDataController::class, 'updateProduct'])->middleware('permission:master,update')->name('master.product.update');
        Route::delete('/master-data/product/{id}', [MasterDataController::class, 'destroyProduct'])->middleware('permission:master,delete')->name('master.product.destroy');
    });

    // Stock Locator & Detail Data
    Route::get('/detail-data', [DetailDataController::class, 'index'])->middleware('permission:detail,read')->name('detail.index');

    // Pallet Management System
    Route::middleware(['permission:pallet,read'])->group(function () {
        Route::get('/pallets', [PalletController::class, 'index'])->name('pallets.index');
        Route::post('/pallets', [PalletController::class, 'store'])->middleware('permission:pallet,create')->name('pallets.store');
        Route::get('/api/pallets/{id}', [PalletController::class, 'show'])->name('pallets.show');
        Route::put('/pallets/{id}', [PalletController::class, 'update'])->middleware('permission:pallet,update')->name('pallets.update');
        Route::delete('/pallets/{id}', [PalletController::class, 'destroy'])->middleware('permission:pallet,delete')->name('pallets.destroy');
    });

    // Transactions Modul (Inbound, Outbound, Transfer)
    Route::middleware(['permission:inbound,read'])->get('/transaction/inbound', [TransactionController::class, 'inboundForm'])->name('transaction.inbound.form');
    Route::middleware(['permission:inbound,create'])->post('/transaction/inbound', [TransactionController::class, 'inbound'])->name('transaction.inbound');
    Route::middleware(['permission:inbound,create'])->post('/transaction/inbound/pallet-to-bin', [TransactionController::class, 'inboundPalletToBin'])->name('transaction.inbound.pallet_to_bin');
    Route::middleware(['permission:inbound,create'])->post('/transaction/inbound/box-to-pallet', [TransactionController::class, 'inboundBoxToPallet'])->name('transaction.inbound.box_to_pallet');

    Route::middleware(['permission:outbound,read'])->get('/transaction/outbound', [TransactionController::class, 'outboundForm'])->name('transaction.outbound.form');
    Route::middleware(['permission:outbound,create'])->post('/transaction/outbound', [TransactionController::class, 'outbound'])->name('transaction.outbound');

    Route::middleware(['permission:transfer,read'])->get('/transaction/transfer', [TransactionController::class, 'transferForm'])->name('transaction.transfer.form');
    Route::middleware(['permission:transfer,create'])->post('/transaction/transfer', [TransactionController::class, 'transfer'])->name('transaction.transfer');

    // API Helper Routes
    Route::get('/api/product/{id}/conversions', [TransactionController::class, 'getProductConversions'])->name('api.product.conversions');

    // User Management
    Route::middleware(['permission:users,read'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:users,create')->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users,update')->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users,delete')->name('users.destroy');
    });

    // RBAC System Management
    Route::middleware(['permission:rbac,read'])->group(function () {
        Route::get('/rbac', [RbacController::class, 'index'])->name('rbac.index');
        Route::post('/rbac/update', [RbacController::class, 'update'])->middleware('permission:rbac,update')->name('rbac.update');
    });

    // Barcode Scanner Simulator
    Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/scan', [ScannerController::class, 'scan'])->name('scanner.scan');

    // Audit Logs
    Route::get('/logs', [LogController::class, 'index'])->middleware('permission:logs,read')->name('logs.index');

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/settings', [SettingController::class, 'index'])->middleware('permission:settings,read')->name('settings.index');
});
