<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // On crée les catégories pour débloquer tes formulaires
        $categories = ['Électronique', 'Informatique', 'Mobilier', 'Fournitures'];

        foreach ($categories as $cat) {
            \App\Models\Category::create(['name' => $cat]);
        }

        echo "Categories créées avec succès ! \n";
    }
}
