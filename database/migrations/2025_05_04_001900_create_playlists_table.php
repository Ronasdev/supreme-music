<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crée la table playlists pour permettre aux utilisateurs de créer
     * et gérer leurs listes de lecture
     */
    public function up(): void
    {
        Schema::create('playlists', function (Blueprint $table) {
            // Champs principaux
            $table->id();
            $table->string('name'); // Nom de la playlist
            $table->unsignedBigInteger('user_id'); // Propriétaire de la playlist
            $table->text('description')->nullable(); // Description optionnelle
            $table->timestamps();
            
            // Contrainte de clé étrangère - Suppression en cascade si l'utilisateur est supprimé
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * Supprime la table playlists
     */
    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};
