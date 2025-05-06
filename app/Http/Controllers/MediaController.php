<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    /**
     * Sert directement un fichier audio depuis le stockage
     * Contourne les problèmes potentiels de Spatie MediaLibrary
     *
     * @param Song $song
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     */
    public function serveAudio(Song $song)
    {
        // Récupérer le média audio
        $media = $song->getFirstMedia('audio');
        
        if (!$media) {
            return response()->json(['error' => 'Fichier audio non disponible'], 404);
        }
        
        // Obtenir le chemin du fichier
        $filePath = $media->getPath();
        
        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Fichier introuvable sur le serveur: ' . $filePath], 404);
        }
        
        // Incrémenter le compteur de previews
        $song->increment('previews_count');
        
        // Déterminer le type MIME du fichier
        $mimeType = $media->mime_type ?? File::mimeType($filePath);
        
        // Solution 1: Streaming direct du fichier sans manipulation
        // Cette approche est plus simple et plus fiable
        try {
            // Ouvrir le fichier en mode binaire
            $fileStream = fopen($filePath, 'rb');
            
            // Streaming du contenu
            return response()->stream(
                function() use ($fileStream) {
                    // Lire et envoyer le fichier par morceaux
                    while (!feof($fileStream)) {
                        echo fread($fileStream, 8192); // 8KB par morceau
                        flush();
                    }
                    fclose($fileStream);
                },
                200,
                [
                    'Content-Type' => $mimeType,
                    'Content-Length' => filesize($filePath),
                    'Accept-Ranges' => 'bytes',
                    'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
                    'Cache-Control' => 'public, max-age=3600'
                ]
            );
        } catch (\Exception $e) {
            // En cas d'erreur, essayer l'approche de secours
            return response()->json([
                'error' => 'Erreur lors du streaming: ' . $e->getMessage(),
                'file_path' => $filePath,
                'file_exists' => file_exists($filePath) ? 'oui' : 'non',
                'file_size' => file_exists($filePath) ? filesize($filePath) : 'n/a'
            ], 500);
        }
    }
}
