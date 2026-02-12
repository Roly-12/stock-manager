<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['date', 'status', 'notes'];

    // Un inventaire contient plusieurs lignes de produits [cite: 65]
    public function items() {
        return $this->hasMany(InventoryItem::class);
    }
}