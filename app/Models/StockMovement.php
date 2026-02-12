<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// On retire la façade Notification car on va utiliser le Model directement
// use Illuminate\Support\Facades\Notification; 
use App\Models\LowStockAlert;
use App\Models\User;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reason'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::saved(function ($movement) {
            // On récupère le produit frais pour avoir la quantité à jour
            $product = $movement->product;
            
            if ($product && $product->quantity <= $product->min_stock) {
                // Création de l'alerte en base de données (Model Eloquent)
                LowStockAlert::create([
                    'product_id' => $product->id,
                    'message'    => "Alerte : Le stock de {$product->name} est de {$product->quantity}.",
                ]);
            }
        });
    }
}