<?php

use App\Models\Product;
use App\Services\StockPredictionService;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $service = new StockPredictionService();
    $products = Product::all();

    foreach ($products as $product) {
        $prediction = $service->predictRupture($product->id);
        if ($prediction['status'] === 'Alerte Rupture') {
            // Logique pour envoyer une alerte aux Admins
            // Notification::send($admins, new StockAlertNotification($product));
        }
    }
})->daily(); // Vérification automatique chaque jour à minuit