<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Inertia\Inertia;

class ApiDashboardController extends Controller
{
    public function index()
    {
        // 1. Récupération des données brutes des 30 derniers jours
        $salesData = StockMovement::where('type', 'sortie')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('sum(quantity) as total'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // 2. Formatage pour Chart.js (Labels et Data)
        $chartData = [
            'labels' => $salesData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d/m')),
            'datasets' => [
                [
                    'label' => 'Quantité sortie',
                    'data' => $salesData->pluck('total'),
                    'borderColor' => '#4F46E5', // Bleu Indigo
                    'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                    'fill' => true,
                    'tension' => 0.4, // Rend la ligne courbée
                ]
            ]
        ];

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_products' => Product::count(),
                'stock_alerts' => Product::whereColumn('quantity', '<=', 'min_stock')->count(),
                'total_movements_month' => StockMovement::whereMonth('created_at', Carbon::now()->month)->count(),
                'sales_chart' => $salesData,
                'top_products' => Product::withCount(['movements' => function($query) {
                        $query->where('type', 'sortie');
                    }])
                    ->orderBy('movements_count', 'desc')
                    ->take(5)
                    ->get(),
                // ON AJOUTE CECI ICI :
                'all_products' => Product::select('id', 'name', 'sku')->get() 
            ]
        ]);
    }

    public function exportPDF()
    {
        $products = Product::all();
        
        // Calcul de la valeur totale
        $totalValue = $products->sum(function($p) {
            return $p->quantity * $p->price; 
        });

        $data = [
            'date' => now()->format('d/m/Y H:i'),
            'products' => $products,
            'totalValue' => $totalValue
        ];

        $pdf = Pdf::loadView('pdf.inventory_report', $data);
        
        return $pdf->download('rapport-stock-'.now()->format('Y-m-d').'.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new ProductsExport, 'inventaire-'.now()->format('Y-m-d').'.xlsx');
    }

    // Exemple dans ton ApiDashboardController
    public function show(Product $product, StockPredictionService $predictionService)
    {
        $prediction = $predictionService->predictRupture($product->id);
        
        return Inertia::render('Products/Show', [
            'product' => $product,
            'prediction' => $prediction 
        ]);
    }
}