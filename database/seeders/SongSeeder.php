<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Song;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SongSeeder extends Seeder
{
    /**
     * Seeder pour la table songs
     * Génère des chansons associées aux albums existants ainsi que des singles
     */
    public function run(): void
    {
        // Créer le dossier pour stocker les fichiers audio si nécessaire
        Storage::makeDirectory('public/songs');
        
        // Récupérer tous les albums existants
        $albums = Album::all();
        
        // Pour chaque album, créer entre 5 et 12 chansons (tracklist complète)
        foreach ($albums as $album) {
            // Détermine le nombre de chansons dans l'album
            $songCount = rand(5, 12);
            
            // Créer les chansons en utilisant forAlbum() pour associer à l'album
            Song::factory()
                ->count($songCount)  // Nombre aléatoire de chansons dans l'album
                ->forAlbum($album->id) // Associe à l'album via la relation définie
                ->create()
                ->each(function ($song, $key) use ($album) {
                    // Définit le numéro de piste de manière séquentielle (1, 2, 3...)
                    $song->track_number = $key + 1;
                    $song->save();
                    
                    // Tente d'ajouter un fichier audio factice
                    try {
                        // Dans un contexte réel, on utiliserait un vrai fichier audio
                        // Ici, on simule avec un fichier placeholder ou une URL fictive
                        // Note: dans un environnement de prod, il faudrait de vrais fichiers MP3
                        $song->addMediaFromUrl('https://s3.amazonaws.com/freecodecamp/drums/Heater-1.mp3')
                             ->toMediaCollection('audio');
                    } catch (\Exception $e) {
                        // Log l'erreur mais continue
                        \Log::warning("Impossible d'ajouter l'audio à la chanson #{$song->id}: " . $e->getMessage());
                    }
                });
        }
        
        // Créer 30 singles (chansons sans album)
        Song::factory()
            ->count(30) // 30 singles
            ->create([  // Force album_id à null pour les singles
                'album_id' => null,
                'track_number' => null, // Les singles n'ont pas de numéro de piste
            ])
            ->each(function ($song) {
                // Même tentative d'ajout d'audio que précédemment
                try {
                    $song->addMediaFromUrl('https://s3.amazonaws.com/freecodecamp/drums/Heater-2.mp3')
                         ->toMediaCollection('audio');
                } catch (\Exception $e) {
                    \Log::warning("Impossible d'ajouter l'audio au single #{$song->id}: " . $e->getMessage());
                }
            });
    }
}
