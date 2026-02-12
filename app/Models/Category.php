<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Category extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id'];

    // Pour voir les sous-catégories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Pour voir la catégorie parente
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relation avec les produits
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}