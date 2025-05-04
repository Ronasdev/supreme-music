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
            // Vérifie si la colonne n'existe pas déjà avant de l'ajouter
            if (!Schema::hasColumn('songs', 'views_count')) {
                // Ajoute un compteur de vues avec une valeur par défaut de 0
                $table->unsignedInteger('views_count')->default(0)->after('bpm');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            // Supprime la colonne si elle existe
            if (Schema::hasColumn('songs', 'views_count')) {
                $table->dropColumn('views_count');
            }
        });
    }
};
