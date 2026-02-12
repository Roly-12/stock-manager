<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductPredictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // On met à jour tous les produits existants avec des valeurs de test
        \App\Models\Product::where('id', '>', 0)->update([
            'lead_time_days' => 5, // 5 jours pour être livré
            'safety_stock' => 10   // On veut toujours avoir 10 unités d'avance
        ]);
    }
}
