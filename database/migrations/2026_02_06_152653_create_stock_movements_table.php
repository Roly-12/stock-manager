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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui a fait l'action [cite: 58]
            
            // Types: Entrée ou Sortie [cite: 50]
            $table->enum('type', ['entree', 'sortie']);
            
            $table->integer('quantity');
            $table->string('reason')->nullable(); // Motif [cite: 59]
            $table->timestamps(); // Date de création incluse [cite: 57]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
