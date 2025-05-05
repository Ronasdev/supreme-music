<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

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
                // Supprimer l'ancienne image d'avatar s'il y en a une
                $user->clearMediaCollection('avatar');
                
                // Ajouter la nouvelle image d'avatar dans la collection 'avatar'
                $user->addMediaFromRequest('avatar')
                    ->usingFileName(time() . '-' . $request->file('avatar')->getClientOriginalName())
                    ->toMediaCollection('avatar');
            } catch (FileDoesNotExist $e) {
                return Redirect::route('profile.edit')
                    ->withErrors(['avatar' => 'Le fichier n\'existe pas.']);
            } catch (FileIsTooBig $e) {
                return Redirect::route('profile.edit')
                    ->withErrors(['avatar' => 'Le fichier est trop volumineux. Taille maximale: 2Mo.']);
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
