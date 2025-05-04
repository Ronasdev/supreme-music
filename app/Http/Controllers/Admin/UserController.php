<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs
     */
    public function index()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Affiche le formulaire de création d'un utilisateur
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin') ? 1 : 0,
        ]);

        return redirect()->route('admin.users.index')
            ->with('admin_success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche les détails d'un utilisateur
     */
    public function show(User $user)
    {
        // Récupérer les commandes et playlists de l'utilisateur
        $orders = $user->orders()->latest()->take(5)->get();
        $playlists = $user->playlists()->withCount('songs')->take(5)->get();
        
        return view('admin.users.show', compact('user', 'orders', 'playlists'));
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Met à jour les informations d'un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['boolean'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->has('is_admin') ? 1 : 0,
        ];

        // Ne mettre à jour le mot de passe que s'il est fourni
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
            ->with('admin_success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur
     */
    public function destroy(User $user)
    {
        // Vérification pour éviter de supprimer son propre compte administrateur
        if ($user->id === auth()->id() && $user->is_admin) {
            return redirect()->route('admin.users.index')
                ->with('admin_error', 'Vous ne pouvez pas supprimer votre propre compte administrateur.');
        }

        // Suppression de l'utilisateur
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('admin_success', 'Utilisateur supprimé avec succès.');
    }
}
