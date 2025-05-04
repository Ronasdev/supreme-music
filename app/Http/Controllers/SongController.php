<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Album;
use App\Models\Playlist;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur responsable de la gestion des fonctionnalités publiques liées aux chansons
 * 
 * Gère l'affichage des détails de chansons et les fonctionnalités publiques associées
 */
class SongController extends Controller
{
    /**
     * Affiche la liste des chansons pour le public
     * Route: GET /songs
     * Route Name: songs.index
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Recherche et filtres potentiels
        $query = $request->get('q');
        $genre = $request->get('genre');
        $artist = $request->get('artist'); // Réactivé car la colonne artist existe
        
        // Construction de la requête
        $songsQuery = Song::with('album')->latest();
        
        // Appliquer les filtres de recherche
        if ($query) {
            $songsQuery->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('artist', 'like', "%{$query}%");
            });
        }
        
        // Filtre par genre si sélectionné
        if ($genre) {
            $songsQuery->where('genre', $genre);
        }
        
        // Filtre par artiste si sélectionné
        if ($artist) {
            $songsQuery->where('artist', 'like', "%{$artist}%");
        }
        
        // Paginer les résultats
        $songs = $songsQuery->paginate(12);
        
        // Récupérer les filtres disponibles pour le formulaire de recherche
        $genres = Song::distinct()->pluck('genre')->filter()->sort()->values();
        $artists = Song::distinct()->pluck('artist')->filter()->sort()->values();
        
        return view('songs.index', compact('songs', 'genres', 'artists', 'query', 'genre', 'artist'));
    }

    /**
     * Affiche la page détaillée d'une chanson pour les visiteurs
     * Route: GET /songs/{song}
     * Route Name: songs.show
     *
     * @param  \App\Models\Song  $song
     * @return \Illuminate\View\View
     */
    public function show(Song $song)
    {
        // Détermine si l'utilisateur a acheté cette chanson
        $userOwns = false;
        $inUserPlaylists = [];
        
        if (Auth::check()) {
            $user = Auth::user();
            $userOwns = $user->hasPurchased($song);
            
            // Vérifie les playlists de l'utilisateur contenant cette chanson
            $inUserPlaylists = $user->playlists()
                ->whereHas('songs', function ($query) use ($song) {
                    $query->where('songs.id', $song->id);
                })
                ->get();
        }
        
        // Chansons similaires (même album ou même artiste)
        $similarSongs = Song::where('id', '!=', $song->id)
            ->where(function ($query) use ($song) {
                if ($song->album_id) {
                    $query->where('album_id', $song->album_id);
                }
                if ($song->artist) {
                    $query->orWhere('artist', $song->artist);
                }
            })
            ->take(4)
            ->get();
        
        // Incrémenter le compteur de vues
        $song->increment('views_count');
            
        return view('songs.show', compact('song', 'userOwns', 'inUserPlaylists', 'similarSongs'));
    }

    /**
     * Prévisualise un extrait de la chanson
     * Route: GET /songs/{song}/preview
     * Route Name: songs.preview
     * 
     * @param \App\Models\Song $song
     * @return \Illuminate\Http\Response
     */
    public function preview(Song $song)
    {
        // Récupère le média audio de la chanson
        $media = $song->getFirstMedia('audio');
        
        if (!$media) {
            return response()->json(['error' => 'Fichier audio non disponible'], 404);
        }
        
        // Incrémenter le compteur de previews
        $song->increment('previews_count');
        
        // Retourne l'URL de l'extrait (peut être une version tronquée ou de moindre qualité)
        return response()->json([
            'preview_url' => $media->getTemporaryUrl(now()->addMinutes(5), 'preview'),
            'duration' => $song->duration
        ]);
    }
}
