<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crée la table pivot playlist_song pour la relation many-to-many
     * entre les playlists et les chansons
     */
    public function up(): void
    {
        Schema::create('playlist_song', function (Blueprint $table) {
            // Champs principaux
            $table->id();
            $table->unsignedBigInteger('playlist_id'); // ID de la playlist
            $table->unsignedBigInteger('song_id'); // ID de la chanson
            $table->timestamps();
            
            // Contraintes de clés étrangères avec suppression en cascade
            $table->foreign('playlist_id')->references('id')->on('playlists')->onDelete('cascade');
            $table->foreign('song_id')->references('id')->on('songs')->onDelete('cascade');
            
            // Index unique pour éviter les doublons
            $table->unique(['playlist_id', 'song_id']);
        });
    }

    /**
     * Reverse the migrations.
     * Supprime la table pivot playlist_song
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_song');
    }
};
