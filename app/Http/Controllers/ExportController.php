<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExportController extends Controller
{
    public function index()
    {
        return Inertia::render('Reports/Index');
    }

    // Export Excel (Inventaire complet)
    public function excel()
    {
        return Excel::download(new ProductsExport, 'inventaire_'.now()->format('d_m_Y').'.xlsx');
    }

    // Export PDF (Rapport de Valorisation)
    public function pdf()
    {
        $products = Product::with('category')->get();
        $totalValue = $products->sum(fn($p) => $p->current_stock * $p->price);

        $pdf = Pdf::loadView('pdf.inventory_report', [
            'products' => $products,
            'totalValue' => $totalValue,
            'date' => now()->format('d/m/Y H:i')
        ]);

        return $pdf->download('rapport_stock.pdf');
    }
}