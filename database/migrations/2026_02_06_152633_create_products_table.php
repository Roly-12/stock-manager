<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->foreignId('category_id')->constrained();
            $table->integer('quantity')->default(0);
            $table->integer('min_stock')->default(5);
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            
            // On insère la colonne directement ici, sans IF
            $table->integer('lead_time_days')->default(7); 
            $table->integer('safety_stock')->default(5); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};