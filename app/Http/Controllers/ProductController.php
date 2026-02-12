<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    // Fonction privée pour éviter de répéter la même requête complexe
    private function getHierarchicalCategories()
    {
        return Category::whereNull('parent_id')->with('children')->get();
    }

    public function index(Request $request)
    {
        return Inertia::render('Products/Index', [
            'products' => Product::with('category')
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%");
                })
                ->when($request->category_id, function ($query, $catId) {
                    $query->where('category_id', $catId);
                })
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            
            // CORRECTION ICI : On envoie la hiérarchie pour le filtre
            'categories' => $this->getHierarchicalCategories(),
            'filters' => $request->only(['search', 'category_id'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Products/Form', [
            'categories' => $this->getHierarchicalCategories(),
            'isEditing' => false
        ]);
    }

    public function edit(Product $product)
    {
        return Inertia::render('Products/Form', [
            'product' => $product,
            // CORRECTION ICI : On utilisait all(), maintenant on utilise la hiérarchie
            'categories' => $this->getHierarchicalCategories(),
            'isEditing' => true
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku', // AJOUTE CETTE LIGNE
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'price' => 'required|numeric',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produit créé avec succès !');
    }
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'description' => 'nullable|string'
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produit mis à jour !');
    }

    public function handleMovement(Request $request)
    {        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entrée,sortie',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Mise à jour du stock physique
        if ($request->type === 'entrée') {
            $product->increment('quantity', $request->quantity);
        } else {
            if ($product->quantity < $request->quantity) {
                return back()->withErrors(['quantity' => 'Stock insuffisant pour cette sortie !']);
            }
            $product->decrement('quantity', $request->quantity);
        }

        // ENREGISTREMENT AVEC USER_ID (Crucial pour la validation)
        $product->movements()->create([
            'product_id' => $validated['product_id'],
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
            'user_id' => auth()->id(), // On récupère l'ID de la personne connectée
        ]);

        return redirect()->route('products.index')->with('success', 'Mouvement enregistré avec succès !');
    }

    public function createMovement()
    {
        return Inertia::render('Movements/Create', [
            'products' => Product::select('id', 'name', 'sku', 'quantity as current_stock')->get()
        ]);
    }

    /**
     * Supprime le produit de la base de données.
     */
    public function destroy(Product $product)
    {
        // On supprime d'abord les mouvements associés (si nécessaire selon tes contraintes SQL)
        // ou on laisse faire la cascade si elle est configurée en base de données.
        $product->movements()->delete();
        
        // On supprime le produit
        $product->delete();

        // On redirige vers la liste avec un message de succès
        return redirect()->route('products.index')->with('success', 'Produit supprimé définitivement.');
    }

    public function show(Product $product)
    {
        $movements = $product->movements()->with('user')->latest()->take(10)->get();

        // On récupère les variations par jour
        $rawChartData = $product->movements()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw("DATE(created_at) as date") 
            ->selectRaw("SUM(CASE WHEN type = 'entrée' THEN quantity ELSE -quantity END) as change_qty")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Optionnel : Transformer en cumulé si tu veux voir la courbe de stock
        // Sinon, garde rawChartData tel quel pour voir les flux (pics d'activité)
        
        return Inertia::render('Products/Show', [
            'product' => $product->load('category'),
            'movements' => $movements,
            'chartData' => $rawChartData
        ]);
    }

}