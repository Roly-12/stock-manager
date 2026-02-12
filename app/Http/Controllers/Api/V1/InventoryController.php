<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    // 1. Créer une session d'inventaire
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        return Inventory::create([
            'date' => $request->date,
            'notes' => $request->notes,
            'status' => 'en_cours'
        ]);
    }

    // 2. Ajouter les quantités réelles constatées
    public function addItem(Request $request, Inventory $inventory)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'real_qty' => 'required|integer|min:0',
        ]);

        $product = Product::find($request->product_id);
        $expected = $product->current_stock;

        return $inventory->items()->create([
            'product_id' => $product->id,
            'expected_qty' => $expected,
            'real_qty' => $request->real_qty,
            'gap' => $request->real_qty - $expected
        ]);
    }

    // 3. Valider l'inventaire et AJUSTER le stock automatiquement
    public function validateInventory(Inventory $inventory)
    {
        if ($inventory->status === 'validé') {
            return response()->json(['error' => 'Déjà validé'], 400);
        }

        return DB::transaction(function () use ($inventory) {
            foreach ($inventory->items as $item) {
                // Mise à jour du stock réel du produit (Ajustement automatique)
                $item->product->update([
                    'current_stock' => $item->real_qty
                ]);
            }

            $inventory->update(['status' => 'validé']);

            return response()->json(['message' => 'Inventaire validé et stock mis à jour']);
        });
    }
}