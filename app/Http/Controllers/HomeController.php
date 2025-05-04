<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil avec les dernières sorties musicales
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupère les 4 albums les plus récents pour l'affichage en vedette
        $featuredAlbums = Album::latest()->take(4)->get();
        
        // Récupère les 8 chansons les plus récentes
        $latestSongs = Song::with('album')->latest()->take(8)->get();
        
        // Vérifie si l'utilisateur est connecté pour afficher des recommandations personnalisées
        $userPlaylists = [];
        if (Auth::check()) {
            $userPlaylists = Auth::user()->playlists()->withCount('songs')->take(3)->get();
        }
        
        return view('home', compact('featuredAlbums', 'latestSongs', 'userPlaylists'));
    }
    
    /**
     * Affiche la page du catalogue complet
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function catalog(Request $request)
    {
        // Filtres de recherche
        $search = $request->input('search');
        $sortBy = $request->input('sort', 'latest'); // Par défaut, trié par date (plus récent)
        
        // Construction de la requête pour les albums
        $albumsQuery = Album::query();
        
        // Application des filtres de recherche si spécifiés
        if ($search) {
            $albumsQuery->where('title', 'like', "%{$search}%")
                      ->orWhere('artist', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
        }
        
        // Application du tri
        if ($sortBy === 'oldest') {
            $albumsQuery->oldest();
        } elseif ($sortBy === 'name') {
            $albumsQuery->orderBy('title');
        } else {
            $albumsQuery->latest(); // Par défaut
        }
        
        // Récupération des albums paginés
        $albums = $albumsQuery->paginate(12);
        
        return view('catalog', compact('albums', 'search', 'sortBy'));
    }
    
    /**
     * Affiche la page "Comment ça marche"
     * 
     * @return \Illuminate\View\View
     */
    public function howItWorks()
    {
        return view('how-it-works');
    }
    
    /**
     * Affiche la page de contact
     * 
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('contact');
    }
}
