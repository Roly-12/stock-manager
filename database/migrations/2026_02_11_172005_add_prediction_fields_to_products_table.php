<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Le temps que met le fournisseur à livrer (en jours)
            $table->integer('lead_time_days')->default(7)->after('min_stock');
            // Le stock de sécurité (le "au cas où")
            $table->integer('safety_stock')->default(5)->after('lead_time_days');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['lead_time_days', 'safety_stock']);
        });
    }
};
