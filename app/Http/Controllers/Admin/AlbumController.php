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
        
        // Gestion de l'image de couverture avec Spatie MediaLibrary
        if ($request->hasFile('cover')) {
            $album->addMediaFromRequest('cover')->toMediaCollection('cover');
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

        // Gestion de l'image de couverture avec Spatie MediaLibrary
        if ($request->hasFile('cover')) {
            // Supprime l'ancienne image si elle existe
            $album->clearMediaCollection('cover');
            
            // Ajoute la nouvelle image
            $album->addMediaFromRequest('cover')->toMediaCollection('cover');
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
        try {
            // Commencer une transaction pour assurer l'intégrité des données
            \DB::beginTransaction();
            
            // Récupérer toutes les chansons associées à cet album
            $songs = $album->songs;
            
            // Supprimer chaque chanson associée à l'album
            foreach ($songs as $song) {
                // Supprimer les médias de la chanson
                $song->clearMediaCollection('audio');
                // Supprimer la chanson
                $song->delete();
            }
            
            // Supprimer les médias associés à l'album
            $album->clearMediaCollection('cover');
            
            // Supprimer l'album
            $album->delete();
            
            // Valider la transaction
            \DB::commit();
            
            return redirect()->route('admin.albums.index')
                ->with('admin_success', 'Album et ses ' . count($songs) . ' chanson(s) supprimés avec succès');                
        } catch (\Exception $e) {
            // En cas d'erreur, annuler toutes les modifications
            \DB::rollBack();
            
            // Afficher un message d'erreur plus convivial
            return redirect()->route('admin.albums.index')
                ->with('admin_error', 'Erreur lors de la suppression de l\'album : ' . $e->getMessage());
        }
    }
}
