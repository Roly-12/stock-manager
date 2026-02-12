<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MovementController extends Controller
{
    public function create()
    {
        return Inertia::render('Movements/Create', [
            'products' => Product::all(['id', 'name', 'sku', 'quantity'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|in:entrée,sortie,ajustement', // Ajout ajustement
            'quantity'   => 'required|integer|min:0', // 0 possible pour un ajustement
            'reason'     => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()->find($validated['product_id']);
            $oldQuantity = $product->quantity;
            $newQuantity = $validated['quantity'];

            if ($validated['type'] === 'ajustement') {
                // On calcule la différence pour l'historique
                // Si stock passe de 10 à 8, le mouvement enregistre -2
                $movementQuantity = $newQuantity - $oldQuantity;
                
                StockMovement::create([
                    'product_id' => $validated['product_id'],
                    'user_id'    => auth()->id(),
                    'type'       => 'ajustement',
                    'quantity'   => $movementQuantity, // On stocke l'écart
                    'reason'     => "Ajustement (Ancien: $oldQuantity -> Nouveau: $newQuantity) : " . $validated['reason'],
                ]);

                $product->quantity = $newQuantity;
            } else {
                // Logique classique pour entrée/sortie
                StockMovement::create($validated + ['user_id' => auth()->id()]);
                
                if ($validated['type'] === 'entrée') {
                    $product->increment('quantity', $validated['quantity']);
                } else {
                    if ($product->quantity < $validated['quantity']) {
                        throw new \Exception("Stock insuffisant.");
                    }
                    $product->decrement('quantity', $validated['quantity']);
                }
            }
            
            $product->save();
        });

        return redirect()->route('products.index')->with('success', 'Stock mis à jour avec succès');
    }

    public function index(Request $request)
    {
        return Inertia::render('Movements/Index', [
            'movements' => StockMovement::with(['product', 'user'])
                ->when($request->type, function ($query, $type) {
                    $query->where('type', $type);
                })
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'filters' => $request->only(['type'])
        ]);
    }
}