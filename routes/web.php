<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Api\V1\ApiDashboardController;
use App\Http\Controllers\Api\V1\MovementController;
use App\Http\Controllers\Api\V1\AnalyticsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// --- ACCÈS PUBLIC ---
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// --- ACCÈS CONNECTÉ (Tous rôles : admin, gestionnaire, observateur) ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [ApiDashboardController::class, 'index'])->name('dashboard');
    
    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Consultation (Lecture seule autorisée pour l'observateur)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');
    
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::resource('categories', CategoryController::class);

    // Export & Scan
    Route::get('/dashboard/export-pdf', [ApiDashboardController::class, 'exportPDF'])->name('dashboard.export-pdf');
    Route::get('/dashboard/export-excel', [ApiDashboardController::class, 'exportExcel'])->name('dashboard.export-excel');
    Route::get('/inventory/scan/{barcode}', [InventoryController::class, 'scan'])->name('inventory.scan');
    Route::get('/analytics/export/{product}', [AnalyticsController::class, 'exportOrderPdf'])->name('analytics.export-pdf');
    Route::get('/movements/export/pdf', [MovementController::class, 'exportPdf'])->name('movements.export-pdf');
    
    // --- ACCÈS GESTIONNAIRE + ADMIN (Interdit aux observateurs) ---
    Route::middleware(['role:admin,gestionnaire'])->group(function () {
        // Mouvements de stock
        Route::get('/movements/create', [MovementController::class, 'create'])->name('movements.create');
        Route::post('/movements', [MovementController::class, 'store'])->name('movements.store');
        
        // Gestion des produits/catégories (Ajout/Modif)
        Route::resource('products', ProductController::class)->except(['index']);
        Route::resource('categories', CategoryController::class)->except(['index']);
    });

    // --- ACCÈS ADMIN UNIQUEMENT ---
    Route::middleware(['role:admin'])->group(function () {
        // Gestion des utilisateurs
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        
        // Configuration Système
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        
        // Suppressions sensibles
        Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    });
});

require __DIR__.'/auth.php';