<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Album;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Contrôleur responsable de la gestion administrative des chansons
 * 
 * Gère la création, l'édition, la suppression et l'affichage des chansons
 * dans l'interface d'administration
 */
class SongController extends Controller
{
    /**
     * Affiche la liste des chansons dans l'interface admin
     * Route: GET /admin/songs
     * Route Name: admin.songs.index
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupère toutes les chansons avec leur album pour affichage admin
        $songs = Song::with('album')->latest()->paginate(15);

        // Affiche la vue d'index admin
        return view('admin.songs.index', compact('songs'));
    }

    /**
     * Affiche le formulaire de création d'une chanson (admin)
     * Route: GET /admin/songs/create
     * Route Name: admin.songs.create
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Récupère les albums pour permettre l'association
        $albums = Album::orderBy('title')->get();

        return view('admin.songs.create', compact('albums'));
    }

    /**
     * Enregistre une nouvelle chanson dans la base de données (admin)
     * Route: POST /admin/songs
     * Route Name: admin.songs.store
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation des données du formulaire
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'album_id' => 'nullable|exists:albums,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string|max:10',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string|max:1000',
            'audio_file' => 'required|file|mimes:mp3,wav,ogg|max:15360',
        ]);

        // Création de la chanson
        $song = new Song([
            'title' => $validated['title'],
            'artist' => $validated['artist'],
            'album_id' => $validated['album_id'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'year' => $validated['year'],
            'description' => $validated['description'],
        ]);
        
        $song->save();
        
        // Gestion du fichier audio avec Spatie Media Library
        if ($request->hasFile('audio_file')) {
            $song->addMediaFromRequest('audio_file')
                ->usingFileName(Str::slug($song->title) . '-' . time() . '.' . $request->file('audio_file')->getClientOriginalExtension())
                ->toMediaCollection('audio');
        }

        return redirect()->route('admin.songs.index')
            ->with('admin_success', 'La chanson a été créée avec succès.');
    }

    /**
     * Affiche le détail d'une chanson côté administration
     * Route: GET /admin/songs/{song}
     * Route Name: admin.songs.show
     *
     * @param  \App\Models\Song  $song
     * @return \Illuminate\View\View
     */
    public function show(Song $song)
    {
        return view('admin.songs.show', compact('song'));
    }

    /**
     * Affiche le formulaire d'édition d'une chanson (admin)
     * Route: GET /admin/songs/{song}/edit
     * Route Name: admin.songs.edit
     *
     * @param  \App\Models\Song  $song
     * @return \Illuminate\View\View
     */
    public function edit(Song $song)
    {
        $albums = Album::orderBy('title')->get();
        return view('admin.songs.edit', compact('song', 'albums'));
    }

    /**
     * Met à jour une chanson dans la base de données (admin)
     * Route: PUT/PATCH /admin/songs/{song}
     * Route Name: admin.songs.update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Song  $song
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Song $song)
    {
        // Validation des données du formulaire
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'album_id' => 'nullable|exists:albums,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string|max:10',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string|max:1000',
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg|max:15360',
        ]);

        // Mise à jour des données de la chanson
        $song->update([
            'title' => $validated['title'],
            'artist' => $validated['artist'],
            'album_id' => $validated['album_id'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'year' => $validated['year'],
            'description' => $validated['description'],
        ]);

        // Gestion du fichier audio avec Spatie Media Library (si nouveau fichier fourni)
        if ($request->hasFile('audio_file')) {
            // Supprime l'ancien fichier audio s'il existe
            $song->clearMediaCollection('audio');
            
            // Ajoute le nouveau fichier audio
            $song->addMediaFromRequest('audio_file')
                ->usingFileName(Str::slug($song->title) . '-' . time() . '.' . $request->file('audio_file')->getClientOriginalExtension())
                ->toMediaCollection('audio');
        }

        return redirect()->route('admin.songs.index')
            ->with('admin_success', 'La chanson a été mise à jour avec succès.');
    }

    /**
     * Supprime une chanson de la base de données (admin)
     * Route: DELETE /admin/songs/{song}
     * Route Name: admin.songs.destroy
     *
     * @param  \App\Models\Song  $song
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Song $song)
    {
        // Supprime les fichiers multimédia associés
        $song->clearMediaCollection('audio');
        
        // Supprime la chanson
        $song->delete();
        
        return redirect()->route('admin.songs.index')
            ->with('admin_success', 'La chanson a été supprimée avec succès.');
    }
}
