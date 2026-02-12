<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);

        if ($request->type) {
            $query->where('type', $request->type);
        }

        return Inertia::render('Movements/Index', [
            'movements' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['type'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Movements/Create', [
            'products' => Product::all(['id', 'name', 'sku', 'quantity'])
        ]);
    }

    public function store(Request $request)
    {
        // 1. Mise à jour de la validation pour inclure 'ajustement'
        // On autorise min:0 car une valeur réelle peut être 0
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entrée,sortie,ajustement', 
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()->find($validated['product_id']);
            $oldQuantity = $product->quantity;
            $newQuantity = $validated['quantity'];

            // 2. Logique spécifique pour l'ajustement
            if ($validated['type'] === 'ajustement') {
                $delta = $newQuantity - $oldQuantity;

                StockMovement::create([
                    'product_id' => $validated['product_id'],
                    'user_id' => auth()->id(),
                    'type' => 'ajustement',
                    'quantity' => $delta, // On enregistre l'écart (+ ou -)
                    'reason' => "Correction (Ancien: $oldQuantity -> Nouveau: $newQuantity). " . ($validated['reason'] ?? ''),
                ]);

                $product->quantity = $newQuantity;
                $product->save();
            } else {
                // Logique classique Entrée / Sortie
                if ($validated['type'] === 'sortie' && $product->quantity < $validated['quantity']) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'quantity' => 'Stock insuffisant pour cette sortie.'
                    ]);
                }

                StockMovement::create([
                    'product_id' => $validated['product_id'],
                    'user_id' => auth()->id(),
                    'type' => $validated['type'],
                    'quantity' => $validated['quantity'],
                    'reason' => $validated['reason'],
                ]);

                if ($validated['type'] === 'entrée') {
                    $product->increment('quantity', $validated['quantity']);
                } else {
                    $product->decrement('quantity', $validated['quantity']);
                }
            }
        });

        return redirect()->route('movements.index')->with('success', 'Mouvement enregistré');
    }
    public function exportPdf(Request $request)
    {
        // On récupère les mouvements avec les relations
        // On applique le filtre 'type' si l'utilisateur l'a sélectionné
        $movements = StockMovement::with(['product', 'user'])
            ->when($request->type, function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->latest()
            ->get();

        $data = [
            'movements' => $movements,
            'type' => $request->type ?? 'Tous les flux',
            'date' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.movements_history', $data);
        
        // Le format paysage est idéal pour un journal de bord
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("Journal_Stock_" . now()->format('d-m-Y') . ".pdf");
    }
}