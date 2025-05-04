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
             // Modifier la contrainte de clé étrangère existante
             $table->dropForeign(['album_id']);
            
             // Rendre album_id nullable pour permettre les singles (chansons sans album)
             $table->unsignedBigInteger('album_id')->nullable()->change();
             
             // Rétablir la contrainte de clé étrangère mais avec possibilité de valeur NULL
             $table->foreign('album_id')
                   ->references('id')
                   ->on('albums')
                   ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
              // Supprimer la contrainte de clé étrangère
              $table->dropForeign(['album_id']);
            
              // Rendre album_id non nullable à nouveau
              $table->unsignedBigInteger('album_id')->nullable(false)->change();
              
              // Rétablir la contrainte de clé étrangère originale
              $table->foreign('album_id')
                    ->references('id')
                    ->on('albums')
                    ->onDelete('restrict');
        });
    }
};
