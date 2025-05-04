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
            // Vérifie si la colonne 'artist' n'existe pas avant de l'ajouter
            if (!Schema::hasColumn('songs', 'artist')) {
                $table->string('artist')->nullable()->after('album_id');
            }
            
            // La colonne 'duration' semble déjà exister, on la vérifie avant d'essayer de l'ajouter
            if (!Schema::hasColumn('songs', 'duration')) {
                $table->string('duration')->nullable()->after('artist');
            }
            
            // Vérifie si la colonne 'lyrics' n'existe pas avant de l'ajouter
            if (!Schema::hasColumn('songs', 'lyrics')) {
                $table->text('lyrics')->nullable()->after('price');
            }
            
            // Vérifie si la colonne 'bpm' n'existe pas avant de l'ajouter
            if (!Schema::hasColumn('songs', 'bpm')) {
                $table->integer('bpm')->nullable()->after('lyrics');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            // Vérifie chaque colonne avant de la supprimer
            $columnsToDelete = [];
            
            if (Schema::hasColumn('songs', 'artist')) {
                $columnsToDelete[] = 'artist';
            }
            
            if (Schema::hasColumn('songs', 'duration')) {
                $columnsToDelete[] = 'duration';
            }
            
            if (Schema::hasColumn('songs', 'lyrics')) {
                $columnsToDelete[] = 'lyrics';
            }
            
            if (Schema::hasColumn('songs', 'bpm')) {
                $columnsToDelete[] = 'bpm';
            }
            
            // Ne supprime les colonnes que si le tableau n'est pas vide
            if (!empty($columnsToDelete)) {
                $table->dropColumn($columnsToDelete);
            }
        });
    }
};
