<?php
namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur responsable de la gestion des fonctionnalités publiques liées aux albums
 * 
 * Gère l'affichage des albums et des fonctionnalités associées pour les utilisateurs
 */
class AlbumController extends Controller
{
    /**
     * Affiche la liste publique des albums
     * Route: GET /albums
     * Route Name: albums.index
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Filtres potentiels (genre, année, etc.)
        $query = $request->get('q');
        $genre = $request->get('genre');
        $year = $request->get('year');
        
        // Construction de la requête
        $albumsQuery = Album::withCount('songs');
        
        // Appliquer les filtres
        if ($query) {
            $albumsQuery->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('artist', 'like', "%{$query}%");
            });
        }
        
        if ($genre) {
            $albumsQuery->where('genre', $genre);
        }
        
        if ($year) {
            $albumsQuery->where('year', $year);
        }
        
        // Trier et paginer les résultats
        $albums = $albumsQuery->latest()->paginate(12);
        
        // Récupérer les filtres disponibles
        $genres = Album::distinct()->pluck('genre')->filter()->sort()->values();
        $years = Album::distinct()->pluck('year')->filter()->sort()->values();
        
        return view('albums.index', compact('albums', 'genres', 'years', 'query', 'genre', 'year'));
    }

    /**
     * Affiche les détails d'un album
     * Route: GET /albums/{album}
     * Route Name: albums.show
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\View\View
     */
    public function show(Album $album)
    {
        // Charger les chansons de l'album
        $songs = $album->songs()->orderBy('track_number')->get();
        
        // Déterminer si l'utilisateur a acheté cet album
        $userOwns = false;
        
        if (Auth::check()) {
            $user = Auth::user();
            $userOwns = $user->hasPurchased($album);
        }
        
        // Albums similaires (même artiste ou genre)
        $similarAlbums = Album::where('id', '!=', $album->id)
            ->where(function ($query) use ($album) {
                if ($album->artist) {
                    $query->where('artist', $album->artist);
                }
                if ($album->genre) {
                    $query->orWhere('genre', $album->genre);
                }
            })
            ->take(4)
            ->get();
        
        // Incrémenter le compteur de vues
        $album->increment('views_count');
        
        return view('albums.show', compact('album', 'songs', 'userOwns', 'similarAlbums'));
    }
}
