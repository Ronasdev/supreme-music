<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Supprime la table media qui était utilisée par Spatie MediaLibrary
     */
    public function up(): void
    {
        Schema::dropIfExists('media');
    }

    /**
     * Reverse the migrations.
     * Recréation de la table n'est pas prise en charge car Spatie MediaLibrary n'est plus installé
     */
    public function down(): void
    {
        // Aucune action en down car nous avons supprimé Spatie MediaLibrary
        // et nous n'avons plus accès à la structure originale
    }
};
