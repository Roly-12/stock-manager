<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['inventory_id', 'product_id', 'expected_qty', 'real_qty', 'gap'];

    public function inventory() {
        return $this->belongsTo(Inventory::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}