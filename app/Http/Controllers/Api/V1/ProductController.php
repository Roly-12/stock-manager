<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Liste des produits avec leur catégorie [cite: 12]
    public function index()
    {
        return Product::with('category')->get();
    }

    // Ajouter un produit [cite: 37]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string|unique:products',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'min_stock' => 'integer',
            'optimal_stock' => 'integer'
        ]);

        return Product::create($validated);
    }

    // Détails d'un produit spécifique [cite: 61]
    public function show(Product $product)
    {
        return $product->load(['category', 'movements.user']);
    }
}