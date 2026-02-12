<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LowStockAlert extends Model
{
    // Très important pour que StockMovement puisse enregistrer l'alerte
    protected $fillable = ['product_id', 'message', 'status'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}