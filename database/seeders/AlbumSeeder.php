<?php

namespace Database\Seeders;

use App\Models\Album;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AlbumSeeder extends Seeder
{
    /**
     * Sample album covers to use - these files should be placed in storage/demo/covers
     */
    private array $sampleCovers = [
        'album-cover-1.jpg',
        'album-cover-2.jpg',
        'album-cover-3.jpg',
        'album-cover-4.jpg',
        'album-cover-5.jpg',
        'album-cover-6.jpg',
        'album-cover-7.jpg',
        'album-cover-8.jpg',
    ];
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assurez-vous que les répertoires existent
        Storage::disk('public')->makeDirectory('covers');
        
        // Préparation des exemples de couvertures (à copier depuis le dossier storage/demo/covers)
        $this->copyDemoCovers();
        
        // Créer 10 albums avec des chansons
        Album::factory(10)
            ->create()
            ->each(function ($album) {
                // Attribuer une couverture aléatoire à l'album
                $this->assignCoverToAlbum($album);
                
                // Créer des chansons pour cet album (entre 6 et 12 chansons par album)
                $songCount = rand(6, 12);
                \App\Models\Song::factory($songCount)
                    ->forAlbum($album->id)
                    ->create();
            });
    }
    
    /**
     * Copie les fichiers de démonstration vers le dossier de stockage public
     */
    private function copyDemoCovers(): void
    {
        // Assurez-vous que le répertoire de destination existe
        $demoCoverPath = storage_path('demo/covers');
        
        // Vérifier si le dossier de démonstration existe
        if (!File::exists($demoCoverPath)) {
            // Si non, créez-le et ajoutez un message d'avertissement
            File::makeDirectory($demoCoverPath, 0755, true, true);
            $this->command->warn("Le dossier storage/demo/covers n'existe pas. Veuillez y ajouter des images de couverture exemple.");
            return;
        }
        
        // Vérifiez si les exemples de couverture existent
        foreach ($this->sampleCovers as $cover) {
            if (!File::exists("$demoCoverPath/$cover")) {
                $this->command->warn("Fichier de couverture manquant : $cover");
            }
        }
    }
    
    /**
     * Assigne une couverture aléatoire à un album
     */
    private function assignCoverToAlbum(Album $album): void
    {   
        // Si aucune couverture d'exemple n'existe, on ne fait rien
        if (count($this->sampleCovers) === 0) {
            return;
        }
        
        // Sélectionner une couverture aléatoire
        $randomCover = $this->sampleCovers[array_rand($this->sampleCovers)];
        $sourcePath = storage_path("demo/covers/$randomCover");
        
        // Vérifier si le fichier source existe
        if (!File::exists($sourcePath)) {
            return;
        }
        
        // Créer le répertoire pour cet album si nécessaire
        Storage::disk('public')->makeDirectory("covers/{$album->id}");
        
        // Générer un nom de fichier unique
        $filename = time() . '_' . uniqid() . '.jpg';
        
        // Copier le fichier vers le répertoire de l'album
        $targetPath = Storage::disk('public')->path("covers/{$album->id}/$filename");
        File::copy($sourcePath, $targetPath);
        
        // Mettre à jour l'album avec le nom du fichier
        $album->cover_image = $filename;
        $album->save();
    }
}
