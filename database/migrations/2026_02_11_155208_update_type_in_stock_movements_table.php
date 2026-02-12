<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // On passe la colonne en string simple pour être tranquille, 
            // ou on met à jour l'ENUM
            $table->string('type')->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Optionnel : revenir à l'état précédent si besoin
        });
    }
};
