<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Contrôleur responsable de la gestion du streaming audio et de la bibliothèque musicale
 * 
 * Gère l'accès aux chansons achetées, le streaming audio, l'affichage du lecteur et
 * la bibliothèque utilisateur
 */
class StreamController extends Controller
{
    /**
     * Vérifie si l'utilisateur a accès à cette chanson
     * Un utilisateur a accès s'il a acheté la chanson ou l'album contenant la chanson
     * 
     * @param Song $song La chanson à vérifier
     * @return bool True si l'utilisateur a accès, false sinon
     */
    private function userHasAccess(Song $song)
    {
        // Vérifier qu'un utilisateur est connecté
        if (!Auth::check()) {
            return false;
        }
        
        $user = Auth::user();
        
        // Si l'utilisateur est administrateur, il a accès à tout
        if ($user->is_admin) {
            return true;
        }
        
        // Vérifie si l'utilisateur a acheté cette chanson directement
        // via la méthode polymorphique sur OrderItem
        $boughtSong = OrderItem::where('item_type', Song::class)
            ->where('item_id', $song->id)
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', 'paid');
            })
            ->exists();
            
        if ($boughtSong) {
            return true;
        }
        
        // Vérifie si l'utilisateur a acheté l'album contenant cette chanson
        if ($song->album_id) {
            $boughtAlbum = OrderItem::where('item_type', Album::class)
                ->where('item_id', $song->album_id)
                ->whereHas('order', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->where('status', 'paid');
                })
                ->exists();
                
            if ($boughtAlbum) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Joue une chanson en streaming si l'utilisateur y a accès
     * Route: GET /stream/{song}
     * Route Name: stream.play
     * 
     * @param Song $song La chanson à jouer
     * @return \Illuminate\View\View|RedirectResponse Vue du lecteur ou redirection
     */
    public function play(Song $song)
    {
        // Prépare la vue du lecteur de musique avec URL de streaming
        $audioUrl = route('stream.audio', $song);
        
        // Recommandations de chansons similaires pour enrichir l'expérience utilisateur
        $similarSongs = [];
        
        if ($song->album) {
            // Si la chanson a un album, suggérer d'autres chansons du même album
            $similarSongs = Song::where('id', '!=', $song->id)
                ->where('album_id', $song->album_id)
                ->take(5)
                ->get();
        }
        
        // Si nous n'avons pas suffisamment de recommandations, ajouter des chansons du même artiste
        if ($similarSongs->count() < 3 && $song->artist) {
            $artistSongs = Song::where('id', '!=', $song->id)
                ->where('artist', 'like', '%' . $song->artist . '%')
                ->whereNotIn('id', $similarSongs->pluck('id')->toArray())
                ->take(5 - $similarSongs->count())
                ->get();
                
            $similarSongs = $similarSongs->merge($artistSongs);
        }
        
        // Vérifier si l'utilisateur possède la chanson pour déterminer les contrôles d'accès
        $userOwns = Auth::check() ? $this->userHasAccess($song) : false;
        
        return view('stream.player', compact('song', 'audioUrl', 'similarSongs', 'userOwns'));
    }
    
    /**
     * Diffuse le fichier audio de la chanson si l'utilisateur y a accès
     * Route: GET /stream/audio/{song}
     * Route Name: stream.audio
     * 
     * @param Song $song La chanson à diffuser
     * @return StreamedResponse|RedirectResponse Flux audio ou redirection
     */
    public function audio(Song $song)
    {
        // Vérifie si l'utilisateur a accès à cette chanson
        if (!$this->userHasAccess($song)) {
            return redirect()->route('songs.show', $song)
                ->with('error', 'Vous devez acheter cette chanson pour l\'écouter.');
        }
        
        // Récupère le média audio de la chanson via Spatie Media Library
        $media = $song->getFirstMedia('audio');
        
        if (!$media) {
            return redirect()->back()
                ->with('error', 'Fichier audio non disponible.');
        }
        
        // Incrémente le compteur d'écoutes (pour les statistiques)
        $song->increment('streams_count');
        
        // Retourne le fichier en streaming
        return $media->toResponse(request());
    }
    
    /**
     * Affiche la bibliothèque musicale de l'utilisateur (chansons achetées)
     * Route: GET /library
     * Route Name: library
     * 
     * @return \Illuminate\View\View|RedirectResponse Vue ou redirection
     */
    public function library()
    {
        // L'utilisateur doit être connecté pour accéder à sa bibliothèque
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter pour accéder à votre bibliothèque musicale.');
        }
        
        $user = Auth::user();
        
        // Récupère les IDs des chansons achetées directement via la relation polymorphique
        $songIds = OrderItem::where('item_type', Song::class)
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', 'paid');
            })
            ->pluck('item_id');
            
        // Récupère les IDs des albums achetés
        $albumIds = OrderItem::where('item_type', Album::class)
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', 'paid');
            })
            ->pluck('item_id');
            
        // Récupère toutes les chansons des albums achetés
        $albumSongIds = Song::whereIn('album_id', $albumIds)->pluck('id');
        
        // Combine les deux collections d'IDs et récupère les chansons
        $allSongIds = $songIds->merge($albumSongIds)->unique();
        $songs = Song::with('album')->whereIn('id', $allSongIds)->paginate(12);
        
        // Récupère les playlists de l'utilisateur pour la navigation latérale
        $playlists = $user->playlists()->withCount('songs')->get();
        
        return view('stream.library', compact('songs', 'playlists'));
    }
    
    /**
     * Télécharge la chanson si l'utilisateur l'a achetée
     * Route: GET /stream/download/{song}
     * Route Name: stream.download
     * 
     * @param Song $song La chanson à télécharger
     * @return Response|RedirectResponse Fichier ou redirection
     */
    public function download(Song $song)
    {
        // Vérifie si l'utilisateur a accès à cette chanson
        if (!$this->userHasAccess($song)) {
            return redirect()->route('songs.show', $song)
                ->with('error', 'Vous devez acheter cette chanson pour la télécharger.');
        }
        
        // Récupère le média audio de la chanson via Spatie Media Library
        $media = $song->getFirstMedia('audio');
        
        if (!$media) {
            return redirect()->back()
                ->with('error', 'Fichier audio non disponible pour téléchargement.');
        }
        
        // Prépare le nom du fichier pour le téléchargement
        $fileName = str_replace(' ', '_', $song->title);
        if ($song->artist) {
            $fileName = str_replace(' ', '_', $song->artist) . '_-_' . $fileName;
        }
        
        // Ajoute l'extension du fichier si elle n'est pas déjà présente
        $extension = pathinfo($media->file_name, PATHINFO_EXTENSION);
        if (!str_ends_with(strtolower($fileName), '.' . strtolower($extension))) {
            $fileName .= '.' . $extension;
        }
        
        // Retourne le fichier en téléchargement
        return response()->download($media->getPath(), $fileName);
    }
}
