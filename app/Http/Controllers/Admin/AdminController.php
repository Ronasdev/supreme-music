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
     * @return \Illuminate\View\View
     */
    public function stats()
    {
        // Ventes par mois (12 derniers mois)
        $salesByMonth = Order::where('status', 'paid')
            ->select(DB::raw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_price) as total'))
            ->whereYear('created_at', '>=', now()->subYear()->year)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        // Formats des données pour les graphiques
        $monthLabels = $salesByMonth->map(function ($item) {
            return date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year));
        });
        
        $salesData = $salesByMonth->pluck('total');
        
        // Statistiques diverses
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'paid')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $conversionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0;
        
        return view('admin.stats', compact(
            'monthLabels',
            'salesData',
            'totalUsers',
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'conversionRate'
        ));
    }

    /**
     * Affiche les statistiques de vente détaillées
     * Route: GET /admin/stats/sales
     * Route Name: admin.stats.sales
     * 
     * @return \Illuminate\View\View
     */
    public function salesStats()
    {
        // Ventes par produit (top 20)
        $albumSales = DB::table('order_items')
            ->join('albums', function ($join) {
                $join->on('order_items.item_id', '=', 'albums.id')
                    ->where('order_items.item_type', '=', Album::class);
            })
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '=', 'paid')
            ->select('albums.title', DB::raw('COUNT(*) as count'), DB::raw('SUM(order_items.price) as revenue'))
            ->groupBy('albums.title')
            ->orderBy('revenue', 'desc')
            ->take(20)
            ->get();
            
        $songSales = DB::table('order_items')
            ->join('songs', function ($join) {
                $join->on('order_items.item_id', '=', 'songs.id')
                    ->where('order_items.item_type', '=', Song::class);
            })
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '=', 'paid')
            ->select('songs.title', DB::raw('COUNT(*) as count'), DB::raw('SUM(order_items.price) as revenue'))
            ->groupBy('songs.title')
            ->orderBy('revenue', 'desc')
            ->take(20)
            ->get();
            
        return view('admin.stats.sales', compact('albumSales', 'songSales'));
    }

    /**
     * Affiche les statistiques des utilisateurs
     * Route: GET /admin/stats/users
     * Route Name: admin.stats.users
     * 
     * @return \Illuminate\View\View
     */
    public function usersStats()
    {
        // Nouveaux utilisateurs par mois
        $usersByMonth = User::select(DB::raw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count'))
            ->whereYear('created_at', '>=', now()->subYear()->year)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        // Utilisateurs les plus actifs (par nombre de commandes)
        $topBuyers = User::withCount(['orders' => function ($query) {
                $query->where('status', 'paid');
            }])
            ->orderBy('orders_count', 'desc')
            ->take(10)
            ->get();
            
        // Utilisateurs par pays (si disponible)
        // Note: Cela nécessiterait d'ajouter un champ country à la table users
        
        return view('admin.stats.users', compact('usersByMonth', 'topBuyers'));
    }

    /**
     * Affiche la page des paramètres du site
     * Route: GET /admin/settings
     * Route Name: admin.settings
     * 
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        // Récupérer les paramètres actuels (pourrait être stocké dans une table settings)
        $settings = [
            'site_name' => config('app.name'),
            'admin_email' => config('mail.from.address', 'admin@example.com'),
            'currency' => '€',
            'vat_rate' => 20, // Taux de TVA en pourcentage
            'items_per_page' => 12,
            'enable_streaming' => true,
            'maintenance_mode' => false
        ];
        
        return view('admin.settings', compact('settings'));
    }

    /**
     * Met à jour les paramètres du site
     * Route: PATCH /admin/settings
     * Route Name: admin.settings.update
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'items_per_page' => 'required|integer|min:5|max:100',
            'enable_streaming' => 'boolean',
            'maintenance_mode' => 'boolean'
        ]);
        
        // Mise à jour des paramètres (dans cet exemple, nous ne les persistons pas réellement)
        // Dans une implémentation réelle, ils seraient enregistrés dans la base de données
        
        return redirect()->route('admin.settings')
            ->with('admin_success', 'Les paramètres ont été mis à jour avec succès.');
    }
}
