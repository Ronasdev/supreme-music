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
           // On vérifie d'abord si la colonne existe
        if (Schema::hasColumn('songs', 'duration')) {
            // Étape 1: Supprimer la colonne duration existante
            Schema::table('songs', function (Blueprint $table) {
                $table->dropColumn('duration');
            });
        }
        
        // Étape 2: Recréer la colonne avec le bon type (string)
        Schema::table('songs', function (Blueprint $table) {
            $table->string('duration', 10)->nullable()->after('price');
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Si la colonne a été recréée en string, la recréer en decimal comme elle était initialement
        if (Schema::hasColumn('songs', 'duration')) {
            Schema::table('songs', function (Blueprint $table) {
                $table->dropColumn('duration');
            });
            
            Schema::table('songs', function (Blueprint $table) {
                $table->decimal('duration')->nullable()->after('price');
            });
        }
    }
};
