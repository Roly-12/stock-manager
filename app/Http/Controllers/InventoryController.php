<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Inventory/Adjust', [
            'products' => Product::with('category')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'adjustments' => 'required|array',
            'adjustments.*.product_id' => 'required|exists:products,id',
            'adjustments.*.actual_stock' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->adjustments as $adj) {
                $product = Product::find($adj['product_id']);
                $diff = $adj['actual_stock'] - $product->current_stock;

                if ($diff != 0) {
                    // Création du mouvement de régularisation
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => $diff > 0 ? 'entrée' : 'sortie',
                        'quantity' => abs($diff),
                        'reason' => 'Ajustement d\'inventaire (Inventaire Physique)',
                    ]);

                    // Mise à jour du stock physique
                    $product->update(['current_stock' => $adj['actual_stock']]);
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'Inventaire validé et stock mis à jour.');
    }

    /**
     * Supprimer un produit de l'inventaire
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Optionnel : On vérifie si on veut vraiment supprimer physiquement 
        // ou juste "archiver" si des mouvements de stock existent.
        $product->delete();

        return redirect()->back()->with('success', 'Produit retiré de l\'inventaire.');
    }

    /**
     * @OA\Get(
     * path="/api/v1/inventory/scan/{barcode}",
     * summary="Scanner un produit",
     * description="Récupère les informations d'un produit via son code-barres",
     * tags={"Inventaire"},
     * @OA\Parameter(
     * name="barcode",
     * in="path",
     * required=true,
     * description="Le code scanné (QR ou barre)",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Produit trouvé avec succès"
     * ),
     * @OA\Response(
     * response=404,
     * description="Produit non trouvé"
     * )
     * )
     */
    public function scan($barcode)
    {
        // On cherche par SKU ou Barcode
        $product = Product::where('sku', $barcode)
                        ->orWhere('barcode', $barcode)
                        ->first();

        if (!$product) {
            return response()->json(['message' => 'Non trouvé'], 404);
        }

        return response()->json($product);
    }
    
}