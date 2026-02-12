<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Utilisateur qui a réalisé l'inventaire
            $table->date('date'); // Date de réalisation [cite: 63]
            $table->string('status')->default('en_cours'); // en_cours, validé, archivé 
            $table->text('notes')->nullable(); // Justification ou remarques 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
