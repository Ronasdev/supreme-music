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
        Schema::table('songs', function (Blueprint $table) {
           // Modifie le type de la colonne 'duration' de decimal à string
            // Cela permettra de stocker des valeurs au format MM:SS comme '3:29'
            $table->string('duration', 10)->nullable()->change();
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            // Restaure le type original (decimal) de la colonne 'duration'
            $table->decimal('duration')->nullable()->change();
        
        });
    }
};
