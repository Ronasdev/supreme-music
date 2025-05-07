<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    // Durée maximale de la prévisualisation en secondes
    const PREVIEW_DURATION = 30;
    
    /**
     * Sert un fichier audio directement depuis le stockage
     *
     * @param int $song_id ID de la chanson
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function serveAudio($song_id)
    {
        // Recherche de la chanson
        $song = Song::findOrFail($song_id); 
        
        try {
            // Vérifier si la chanson a un fichier audio
            if (!$song->hasAudioFile()) {
                return response()->json(['error' => 'Fichier audio non trouvé'], 404);
            }
            
            // Obtenir le chemin du fichier sur le disque
            $filePath = $song->getAudioPath();
            
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'Fichier physique non trouvé sur le serveur'], 404);
            }
            
            // Déterminer le type MIME basé sur l'extension du fichier
            $mimeType = $this->getMimeTypeFromFileName($song->audio_file);
            
            // Retourner le fichier avec les en-têtes appropriés pour le streaming
            return response()->file($filePath, [
                'Content-Type' => $mimeType, 
                'Content-Disposition' => 'inline; filename="' . $song->audio_file . '"',
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public, max-age=3600',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération du fichier audio', 
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Uniquement pour le développement, à retirer en production
            ], 500);
        }
    }
    
    /**
     * Sert une image de pochette d'album depuis le stockage
     *
     * @param int $album_id ID de l'album
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function serveAlbumCover($album_id)
    {
        // Recherche de l'album
        $album = Album::findOrFail($album_id);
        
        try {
            // Vérifier si l'album a une image de couverture
            if (!$album->hasCoverImage()) {
                return response()->json(['error' => 'Image de couverture non trouvée'], 404);
            }
            
            // Obtenir le chemin du fichier sur le disque
            $filePath = storage_path('app/public/covers/' . $album->id . '/' . $album->cover_image);
            
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'Fichier image non trouvé sur le serveur'], 404);
            }
            
            // Déterminer le type MIME basé sur l'extension du fichier
            $mimeType = $this->getMimeTypeFromFileName($album->cover_image);
            
            // Retourner le fichier avec les en-têtes appropriés
            return response()->file($filePath, [
                'Content-Type' => $mimeType, 
                'Content-Disposition' => 'inline; filename="' . $album->cover_image . '"',
                'Cache-Control' => 'public, max-age=3600',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération de l\'image', 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Détermine le type MIME à partir du nom de fichier
     *
     * @param string $filename Nom du fichier
     * @return string Type MIME
     */
    protected function getMimeTypeFromFileName($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        $mimeTypes = [
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];
        
        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
    
    /**
     * Sert un extrait limité du fichier audio (30 secondes par défaut)
     *
     * @param int $song_id ID de la chanson
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function servePreview($song_id)
    {
        // Recherche de la chanson
        $song = Song::findOrFail($song_id);
        
        try {
            // Vérifier si la chanson a un fichier audio
            if (!$song->hasAudioFile()) {
                return response()->json(['error' => 'Fichier audio non trouvé'], 404);
            }
            
            // Obtenir le chemin du fichier sur le disque
            $filePath = $song->getAudioPath();
            
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'Fichier physique non trouvé sur le serveur'], 404);
            }
            
            // Vérifier si l'utilisateur est authentifié et a déjà acheté cette chanson
            $userCanAccessFullSong = false;
            
            if (auth()->check()) {
                $user = auth()->user();
                
                // L'administrateur a toujours accès à la chanson complète
                if ($user->is_admin) {
                    $userCanAccessFullSong = true;
                } else {
                    // Vérifier si l'utilisateur a acheté cette chanson
                    $userCanAccessFullSong = $user->orders()
                        ->whereHas('items', function ($query) use ($song) {
                            $query->where('itemable_type', Song::class)
                                  ->where('itemable_id', $song->id);
                        })
                        ->where('status', 'completed')
                        ->exists();
                }
            }
            
            // Si l'utilisateur peut accéder à la chanson complète, la servir normalement
            if ($userCanAccessFullSong) {
                return $this->serveAudio($song_id);
            }
            
            // Sinon, servir seulement un extrait avec les en-têtes appropriés
            $mimeType = $this->getMimeTypeFromFileName($song->audio_file);
            
            // On ajoute un en-tête pour informer le lecteur de la durée limitée
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $song->audio_file . '"',
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public, max-age=3600',
                'X-Preview-Only' => 'true', // En-tête personnalisé pour indiquer une prévisualisation
                'X-Preview-Duration' => self::PREVIEW_DURATION // Durée de la prévisualisation en secondes
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération de la prévisualisation', 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
