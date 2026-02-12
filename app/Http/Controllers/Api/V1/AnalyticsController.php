<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Statistiques globales
        $totalStockValue = Product::sum(DB::raw('quantity * price'));
        $reorderCost = Product::whereColumn('quantity', '<=', 'min_stock')
            ->get()
            ->sum(function($p) { 
                return ($p->min_stock - $p->quantity + 10) * $p->price; 
            });

        // Top produit : 30 derniers jours, types 'out' ou 'sortie'
        $topMovement = StockMovement::whereIn('type', ['out', 'sortie'])
            ->where('created_at', '>=', now()->subDays(30))
            ->select('product_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->with('product')
            ->first();

        // 2. Flux d'activité (30 jours glissants)
        $movements = StockMovement::whereIn('type', ['out', 'sortie'])
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(quantity) as total'))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $dailyFlux = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyFlux[] = $movements[$date] ?? 0;
        }

        // 3. Logique de prédiction pour les listes
        $formatProduct = function($product) {
            $totalOut = $product->movements()
                ->whereIn('type', ['out', 'sortie'])
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('quantity');
            
            $dailyCons = $totalOut / 30;
            $leadTime = $product->lead_time_days ?? 3;
            $safetyStock = $product->safety_stock ?? $product->min_stock;
            $reorderPoint = ($dailyCons * $leadTime) + $safetyStock;

            $daysLeft = $dailyCons > 0 
                ? max(0, floor(($product->quantity - $safetyStock) / $dailyCons)) 
                : 99;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'current_stock' => $product->quantity,
                'reorder_point' => ceil($reorderPoint),
                'prediction' => [
                    'jours_restants' => $daysLeft,
                    'recommandation' => $product->quantity <= $reorderPoint 
                        ? "Commander immédiatement (Seuil : " . ceil($reorderPoint) . " u)"
                        : "Stock sécurisé."
                ]
            ];
        };

        return Inertia::render('Analytics/Index', [
            'stats' => [
                'total_value' => number_format($totalStockValue, 0, '.', ' '),
                'reorder_cost' => number_format($reorderCost, 0, '.', ' '),
                'top_product' => $topMovement ? $topMovement->product->name : 'Aucun mouvement',
                'daily_flux' => $dailyFlux,
            ],
            'alerts' => Product::whereColumn('quantity', '<=', 'min_stock')->get()->map($formatProduct),
            'stable' => Product::whereColumn('quantity', '>', 'min_stock')->limit(10)->get()->map($formatProduct),
        ]);
    }

    public function exportOrderPdf(Product $product) 
    {
        $totalOut = $product->movements()->whereIn('type', ['out', 'sortie'])->where('created_at', '>=', now()->subDays(30))->sum('quantity');
        $dailyCons = $totalOut / 30;
        $jours = $dailyCons > 0 ? max(0, floor(($product->quantity - $product->safety_stock) / $dailyCons)) : 99;

        $data = [
            'product' => $product,
            'date' => now()->format('d/m/Y'),
            'order_number' => 'REC-' . date('Ymd') . '-' . $product->id,
            'prediction' => [
                'jours_restants' => $jours,
                'recommandation' => "Commander " . ($product->min_stock * 2) . " unités pour sécuriser le stock."
            ]
        ];

        return Pdf::loadView('pdf.order_recommendation', $data)->download("Recommandation_{$product->sku}.pdf");
    }
}