<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sku', 'category_id', 'price', 'quantity', 'min_stock', 'description'];

    // Point 3 : Anticipation des ruptures
    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->min_stock;
    }

    // Point 2 : Prévision simplifiée (Demande future)
    // On calcule la moyenne des sorties sur les 30 derniers jours
    public function predictDemand()
    {
        $avgSales = $this->movements()
            ->where('type', 'sortie')
            ->where('created_at', '>=', now()->subDays(30))
            ->avg('quantity') ?? 0;
            
        return ceil($avgSales * 1.2); // Prévision avec une marge de sécurité de 20%
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
}