<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Order;
use App\Models\Song;
use App\Models\User;
use App\Models\Playlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Contrôleur responsable de la gestion des fonctionnalités d'administration
 * 
 * Ce contrôleur gère les tableaux de bord administratifs, les statistiques
 * et les paramètres généraux du site
 */
class AdminController extends Controller
{
    /**
     * Affiche le tableau de bord de l'admin avec les statistiques générales
     * Route: GET /admin/dashboard
     * Route Name: admin.dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Statistiques générales pour le tableau de bord
        $totalAlbums = Album::count(); // Nombre total d'albums
        $totalSongs = Song::count(); // Nombre total de chansons
        $totalUsers = User::count(); // Nombre total d'utilisateurs
        $totalOrders = Order::count(); // Nombre total de commandes
        
        // Chiffres d'affaires total
        $totalRevenue = Order::where('status', 'paid')->sum('total_price');
        
        // Dernières commandes pour affichage rapide
        $latestOrders = Order::with(['user'])->latest()->take(5)->get();
        
        // Derniers utilisateurs inscrits
        $latestUsers = User::latest()->take(5)->get();
        
        // Albums les plus vendus
        $topAlbums = Album::withCount(['orderItems' => function ($query) {
            $query->whereHasMorph('item', [Album::class]);
        }])->orderBy('order_items_count', 'desc')->take(5)->get();
        
        // Chansons les plus vendues
        $topSongs = Song::withCount(['orderItems' => function ($query) {
            $query->whereHasMorph('item', [Song::class]);
        }])->orderBy('order_items_count', 'desc')->take(5)->get();
        
        return view('admin.dashboard', compact(
            'totalAlbums', 
            'totalSongs', 
            'totalUsers', 
            'totalOrders', 
            'totalRevenue', 
            'latestOrders', 
            'latestUsers', 
            'topAlbums', 
            'topSongs'
        ));
    }

    /**
     * Affiche les statistiques générales du site
     * Route: GET /admin/stats
     * Route Name: admin.stats
     * 
     * @deprecated Utilisez StatController::index() à la place
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stats()
    {
        return redirect()->route('admin.stats');
    }

    /**
     * Affiche les statistiques de vente détaillées
     * Route: GET /admin/stats/sales
     * Route Name: admin.stats.sales
     * 
     * @deprecated Utilisez StatController::index() à la place
     * @return \Illuminate\Http\RedirectResponse
     */
    public function salesStats()
    {
        return redirect()->route('admin.stats');
    }

    /**
     * Affiche les statistiques des utilisateurs
     * Route: GET /admin/stats/users
     * Route Name: admin.stats.users
     * 
     * @deprecated Utilisez StatController::index() à la place
     * @return \Illuminate\Http\RedirectResponse
     */
    public function usersStats()
    {
        return redirect()->route('admin.stats');
    }

    /**
     * Affiche la page des paramètres du site
     * Route: GET /admin/settings
     * Route Name: admin.settings
     * 
     * @deprecated Utilisez SettingController::index() à la place
     * @return \Illuminate\Http\RedirectResponse
     */
    public function settings()
    {
        return redirect()->route('admin.settings');
    }

    /**
     * Met à jour les paramètres du site
     * Route: PATCH /admin/settings
     * Route Name: admin.settings.update
     * 
     * @deprecated Utilisez SettingController::update() à la place
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        return app()->make(\App\Http\Controllers\Admin\SettingController::class)->update($request);
    }
}
