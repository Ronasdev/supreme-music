<?php

namespace Database\Seeders;

use App\Models\Album;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AlbumSeeder extends Seeder
{
    /**
     * Seeder pour la table albums
     * Génère des albums factices pour les tests
     */
    public function run(): void
    {
        // Créer le dossier pour stocker les images de couverture si nécessaire
        Storage::makeDirectory('public/albums');
        
        // Générer 20 albums avec la factory
        // Chaque commentaire explique ce que fait la ligne
        Album::factory()
            ->count(20) // Créer 20 albums
            ->create()  // Enregistrer en base de données
            ->each(function ($album) {
                // Pour chaque album, on peut exécuter des actions supplémentaires
                // Par exemple, attacher une image de couverture (utilisant Spatie Media Library)
                try {
                    // Générer une URL d'image aléatoire pour la couverture
                    $imageUrl = 'https://picsum.photos/800/800?random=' . rand(1, 1000);
                    
                    // Attacher l'image à l'album en utilisant la collection 'cover'
                    $album->addMediaFromUrl($imageUrl)
                          ->toMediaCollection('cover');
                } catch (\Exception $e) {
                    // En cas d'erreur, on continue sans image
                    // Mais on log l'erreur pour pouvoir l'identifier
                    \Log::warning("Impossible d'ajouter une image à l'album #{$album->id}: " . $e->getMessage());
                }
            });
    }
}
