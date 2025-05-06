<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Gérer le téléchargement d'avatar s'il est présent dans la requête
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            try {
                // Méthode alternative de stockage d'avatar sans utiliser MediaLibrary
                // pour contourner le problème de la fonction imagecreatefromstring manquante
                
                // 1. Définir le chemin de stockage
                $avatarPath = public_path('storage/avatars');
                
                // 2. Créer le dossier s'il n'existe pas
                if (!file_exists($avatarPath)) {
                    mkdir($avatarPath, 0755, true);
                }
                
                // 3. Générer un nom de fichier unique
                $avatarName = time() . '-' . $request->file('avatar')->getClientOriginalName();
                
                // 4. Déplacer le fichier vers le dossier de stockage
                $request->file('avatar')->move($avatarPath, $avatarName);
                
                // 5. Sauvegarder le chemin dans la base de données
                // Nous devons d'abord vérifier si la table users a une colonne 'avatar_path'
                // Si ce n'est pas le cas, nous allons enregistrer le chemin dans une session pour l'utiliser dans la vue
                $avatarUrl = '/storage/avatars/' . $avatarName;
                
                // Stocker le chemin dans la session
                session(['user_avatar' => $avatarUrl]);
                
            } catch (\Exception $e) {
                return Redirect::route('profile.edit')
                    ->withErrors(['avatar' => 'Une erreur est survenue lors du téléchargement de l\'image: ' . $e->getMessage()]);
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
