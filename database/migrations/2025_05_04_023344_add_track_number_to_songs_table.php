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
            // Vérifie si la colonne 'track_number' n'existe pas avant de l'ajouter
            if (!Schema::hasColumn('songs', 'track_number')) {
                // Ajoute un champ pour le numéro de piste dans l'album
                // Ce champ est nullable car les singles n'ont pas de numéro de piste
                $table->integer('track_number')->nullable()->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            // Vérifie si la colonne 'track_number' existe avant de la supprimer
            if (Schema::hasColumn('songs', 'track_number')) {
                $table->dropColumn('track_number');
            }
        });
    }
};
