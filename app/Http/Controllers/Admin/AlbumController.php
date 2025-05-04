<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Contrôleur responsable de la gestion administrative des albums
 * 
 * Gère la création, l'édition, la suppression et l'affichage des albums
 * dans l'interface d'administration
 */
class AlbumController extends Controller
{
    /**
     * Affiche la liste des albums dans l'interface admin
     * Route: GET /admin/albums
     * Route Name: admin.albums.index
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupère tous les albums avec le nombre de chansons pour affichage admin
        $albums = Album::withCount('songs')->latest()->paginate(10);

        // Affiche la vue d'index admin
        return view('admin.albums.index', compact('albums'));
    }

    /**
     * Affiche le formulaire de création d'un album
     * Route: GET /admin/albums/create
     * Route Name: admin.albums.create
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.albums.create');
    }

    /**
     * Enregistre un nouvel album dans la base de données
     * Route: POST /admin/albums
     * Route Name: admin.albums.store
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'cover' => 'nullable|image|max:2048',
        ]);

        // Création de l'album
        $album = new Album([
            'title' => $validated['title'],
            'artist' => $validated['artist'] ?? null,
            'year' => $validated['year'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? 0,
        ]);
        
        // Gestion de l'image de couverture
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');
            $album->cover = $coverPath;
        }
        
        $album->save();

        return redirect()->route('admin.albums.index')
            ->with('admin_success', 'Album créé avec succès');
    }

    /**
     * Affiche le détail d'un album côté administration
     * Route: GET /admin/albums/{album}
     * Route Name: admin.albums.show
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\View\View
     */
    public function show(Album $album)
    {
        // Charge les chansons de l'album
        $songs = $album->songs()->orderBy('track_number')->get();
        
        // Statistiques de ventes
        $salesCount = $album->orderItems()->count();
        
        return view('admin.albums.show', compact('album', 'songs', 'salesCount'));
    }

    /**
     * Affiche le formulaire d'édition d'un album
     * Route: GET /admin/albums/{album}/edit
     * Route Name: admin.albums.edit
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\View\View
     */
    public function edit(Album $album)
    {
        return view('admin.albums.edit', compact('album'));
    }

    /**
     * Met à jour un album dans la base de données
     * Route: PUT/PATCH /admin/albums/{album}
     * Route Name: admin.albums.update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Album $album)
    {
        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'cover' => 'nullable|image|max:2048',
        ]);

        // Préparation des données à mettre à jour
        $updateData = [
            'title' => $validated['title'],
            'artist' => $validated['artist'] ?? $album->artist,
            'year' => $validated['year'] ?? $album->year,
            'description' => $validated['description'] ?? $album->description,
            'price' => $validated['price'] ?? $album->price,
        ];

        // Gestion de l'image de couverture
        if ($request->hasFile('cover')) {
            // Supprime l'ancienne image si elle existe
            if ($album->cover) {
                Storage::disk('public')->delete($album->cover);
            }
            
            // Stocke la nouvelle image
            $coverPath = $request->file('cover')->store('covers', 'public');
            $updateData['cover'] = $coverPath;
        }

        // Mise à jour de l'album
        $album->update($updateData);

        return redirect()->route('admin.albums.index')
            ->with('admin_success', 'Album modifié avec succès');
    }

    /**
     * Supprime un album de la base de données
     * Route: DELETE /admin/albums/{album}
     * Route Name: admin.albums.destroy
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Album $album)
    {
        // Supprime l'image de couverture si elle existe
        if ($album->cover) {
            Storage::disk('public')->delete($album->cover);
        }
        
        // Supprime l'album (avec les relations selon la configuration du modèle)
        $album->delete();

        return redirect()->route('admin.albums.index')
            ->with('admin_success', 'Album supprimé avec succès');
    }
}
