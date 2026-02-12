<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;

class StockPredictionService
{
    /**
     * Prédit le nombre de jours restants avant rupture
     */
    public function predictRupture(int $productId)
    {
        $product = Product::findOrFail($productId);
        
        // 1. Récupérer les sorties des 30 derniers jours
        $totalSorties = StockMovement::where('product_id', $productId)
            ->where('type', 'sortie')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('quantity');

        // 2. Calculer la Consommation Moyenne Journalière (CMJ)
        $cmj = $totalSorties / 30;

        if ($cmj <= 0) {
            return [
                'status' => 'Stable',
                'jours_restants' => '∞',
                'recommandation' => "Aucune vente enregistrée sur les 30 derniers jours."
            ];
        }

        // 3. Calculer les jours restants (Attention : quantité est le nom de ta colonne)
        $joursRestants = $product->quantity / $cmj;

        // 4. Recommandation basée sur le stock minimal (min_stock)
        if ($joursRestants <= 7) {
            // On suggère de commander pour revenir au-dessus du seuil d'alerte + une marge
            $quantiteACommander = ($product->min_stock * 2) - $product->quantity;
            
            return [
                'status' => 'Alerte Rupture',
                'jours_restants' => round($joursRestants),
                'recommandation' => "Action requise : Commander environ " . max(0, (int)$quantiteACommander) . " unités."
            ];
        }

        return [
            'status' => 'OK', 
            'jours_restants' => round($joursRestants),
            'recommandation' => "Stock suffisant pour la période actuelle."
        ];
    }
}