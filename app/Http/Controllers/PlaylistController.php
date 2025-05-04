<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    /**
     * Affiche la liste des playlists de l'utilisateur connecté.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupère toutes les playlists de l'utilisateur connecté
        $playlists = Auth::user()->playlists()->withCount('songs')->paginate(10);
        
        return view('playlists.index', compact('playlists'));
    }

    /**
     * Affiche le formulaire de création d'une playlist.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('playlists.create');
    }

    /**
     * Enregistre une nouvelle playlist.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Création de la playlist
        Auth::user()->playlists()->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('playlists.index')
            ->with('success', 'Playlist créée avec succès !');
    }

    /**
     * Affiche une playlist spécifique avec ses chansons.
     * 
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\View\View
     */
    public function show(Playlist $playlist)
    {
        // Vérifie que l'utilisateur est propriétaire de la playlist
        if ($playlist->user_id !== Auth::id()) {
            return redirect()->route('playlists.index')
                ->with('error', 'Vous n\'avez pas accès à cette playlist.');
        }

        // Charge les chansons de la playlist
        $playlist->load('songs');
        
        return view('playlists.show', compact('playlist'));
    }

    /**
     * Affiche le formulaire d'édition d'une playlist.
     * 
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\View\View
     */
    public function edit(Playlist $playlist)
    {
        // Vérifie que l'utilisateur est propriétaire de la playlist
        if ($playlist->user_id !== Auth::id()) {
            return redirect()->route('playlists.index')
                ->with('error', 'Vous n\'avez pas accès à cette playlist.');
        }
        
        return view('playlists.edit', compact('playlist'));
    }

    /**
     * Met à jour une playlist existante.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Playlist $playlist)
    {
        // Vérifie que l'utilisateur est propriétaire de la playlist
        if ($playlist->user_id !== Auth::id()) {
            return redirect()->route('playlists.index')
                ->with('error', 'Vous n\'avez pas accès à cette playlist.');
        }

        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Mise à jour de la playlist
        $playlist->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('playlists.show', $playlist)
            ->with('success', 'Playlist mise à jour avec succès !');
    }

    /**
     * Supprime une playlist.
     * 
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Playlist $playlist)
    {
        // Vérifie que l'utilisateur est propriétaire de la playlist
        if ($playlist->user_id !== Auth::id()) {
            return redirect()->route('playlists.index')
                ->with('error', 'Vous n\'avez pas accès à cette playlist.');
        }

        // Supprime la playlist (les relations seront supprimées automatiquement grâce aux clés étrangères)
        $playlist->delete();

        return redirect()->route('playlists.index')
            ->with('success', 'Playlist supprimée avec succès !');
    }

    /**
     * Ajoute une chanson à une playlist.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addSong(Request $request, Playlist $playlist)
    {
        // Vérifie que l'utilisateur est propriétaire de la playlist
        if ($playlist->user_id !== Auth::id()) {
            return redirect()->route('playlists.index')
                ->with('error', 'Vous n\'avez pas accès à cette playlist.');
        }

        // Validation des données
        $request->validate([
            'song_id' => 'required|exists:songs,id',
        ]);

        // Vérifie si la chanson n'est pas déjà dans la playlist
        if (!$playlist->songs()->where('song_id', $request->song_id)->exists()) {
            // Ajoute la chanson à la playlist
            $playlist->songs()->attach($request->song_id);
            $message = 'Chanson ajoutée à la playlist !';
        } else {
            $message = 'Cette chanson est déjà dans votre playlist.';
        }

        return back()->with('success', $message);
    }

    /**
     * Retire une chanson d'une playlist.
     * 
     * @param  \App\Models\Playlist  $playlist
     * @param  \App\Models\Song  $song
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeSong(Playlist $playlist, Song $song)
    {
        // Vérifie que l'utilisateur est propriétaire de la playlist
        if ($playlist->user_id !== Auth::id()) {
            return redirect()->route('playlists.index')
                ->with('error', 'Vous n\'avez pas accès à cette playlist.');
        }

        // Retire la chanson de la playlist
        $playlist->songs()->detach($song->id);

        return back()->with('success', 'Chanson retirée de la playlist !');
    }
}
