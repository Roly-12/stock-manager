<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\MovementController;
use App\Http\Controllers\Api\V1\InventoryController; // Ajouté
use App\Http\Controllers\Api\V1\DashboardController; // Ajouté
use App\Http\Controllers\Api\V1\ExportController;    // Ajouté
use App\Http\Controllers\Auth\AuthController;       // À adapter selon ton namespace
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

/* --- Routes Publiques ou Hors-Middleware --- */
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/products/{product}/pdf', [ExportController::class, 'exportProductPdf']);
    Route::get('/export/products', function () {
        return Excel::download(new ProductsExport, 'inventaire_stock.xlsx');
    });
    Route::get('/inventory/scan/{barcode}', [InventoryController::class, 'scan']);
    Route::post('/inventory/store', [InventoryController::class, 'store']);
});

/* --- Routes Protégées (Bearer Token) --- */
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    
    // Dashboard (Data pour graphiques)
    Route::get('/dashboard', [ApiDashboardController::class, 'index']);

    // Produits
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    
    // Mouvements de stock
    Route::post('/movements', [MovementController::class, 'store']);
    
    // Inventaires (Correction du v1 doublé ici)
    Route::post('/inventories', [InventoryController::class, 'store']);
    Route::post('/inventories/{inventory}/items', [InventoryController::class, 'addItem']);
    Route::post('/inventories/{inventory}/validate', [InventoryController::class, 'validateInventory']);

    // Sécurité Admin
    Route::middleware('can:admin-only')->group(function () {
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });
});