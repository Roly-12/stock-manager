<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    // Afficher la liste des catégories
    public function index()
    {
        return Inertia::render('Categories/Index', [
            // On ne prend que les catégories "racines" et on charge leurs enfants
            'categories' => Category::whereNull('parent_id')->with('children')->get()
        ]);
    }

    // Enregistrer une nouvelle catégorie (ou sous-catégorie)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id', // Pour les sous-catégories
        ]);

        Category::create($validated);

        return redirect()->back()->with('success', 'Catégorie créée avec succès');
    }

    // Mettre à jour
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category->update($validated);

        return redirect()->back();
    }

    // Supprimer
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->back();
    }
}