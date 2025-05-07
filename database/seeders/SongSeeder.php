<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Song;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SongSeeder extends Seeder
{
    /**
     * Sample audio files to use - these files should be placed in storage/demo/audio
     */
    private array $sampleAudioFiles = [
        'audio-sample-1.mp3',
        'audio-sample-2.mp3',
        'audio-sample-3.mp3',
        'audio-sample-4.mp3',
        'audio-sample-5.mp3',
    ];
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assurez-vous que le répertoire de fichiers audio existe
        Storage::disk('public')->makeDirectory('audio');
        
        // Préparer les fichiers audio de démo
        $this->copyDemoAudioFiles();
        
        // Récupérer toutes les chansons qui ont été créées
        $songs = Song::all();
        
        // Attribuer un fichier audio à chaque chanson
        foreach ($songs as $song) {
            $this->assignAudioFileToSong($song);
        }
        
        // Créer quelques singles (chansons sans album)
        Song::factory(15)->create()->each(function ($song) {
            $this->assignAudioFileToSong($song);
        });
    }
    
    /**
     * Copie les fichiers audio de démonstration vers le dossier de stockage temporaire
     */
    private function copyDemoAudioFiles(): void
    {
        $demoAudioPath = storage_path('demo/audio');
        
        // Vérifier si le dossier de démonstration existe
        if (!File::exists($demoAudioPath)) {
            // Si non, créez-le et ajoutez un message d'avertissement
            File::makeDirectory($demoAudioPath, 0755, true, true);
            $this->command->warn("Le dossier storage/demo/audio n'existe pas. Veuillez y ajouter des fichiers audio exemple.");
            return;
        }
        
        // Vérifier si les exemples audio existent
        foreach ($this->sampleAudioFiles as $audioFile) {
            if (!File::exists("$demoAudioPath/$audioFile")) {
                $this->command->warn("Fichier audio manquant : $audioFile");
            }
        }
    }
    
    /**
     * Assigne un fichier audio aléatoire à une chanson
     */
    private function assignAudioFileToSong(Song $song): void
    {
        // Si aucun fichier audio d'exemple n'existe, on ne fait rien
        if (count($this->sampleAudioFiles) === 0) {
            return;
        }
        
        // Sélectionner un fichier audio aléatoire
        $randomAudio = $this->sampleAudioFiles[array_rand($this->sampleAudioFiles)];
        $sourcePath = storage_path("demo/audio/$randomAudio");
        
        // Vérifier si le fichier source existe
        if (!File::exists($sourcePath)) {
            return;
        }
        
        // Créer le répertoire pour cette chanson si nécessaire
        Storage::disk('public')->makeDirectory("audio/{$song->id}");
        
        // Générer un nom de fichier unique
        $extension = pathinfo($randomAudio, PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $extension;
        
        // Copier le fichier vers le répertoire de la chanson
        $targetPath = Storage::disk('public')->path("audio/{$song->id}/$filename");
        File::copy($sourcePath, $targetPath);
        
        // Mettre à jour la chanson avec le nom du fichier
        $song->audio_file = $filename;
        $song->filesize = File::size($sourcePath);
        $song->save();
    }
}
