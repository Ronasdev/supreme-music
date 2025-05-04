<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\User;
use App\Models\Song;
use App\Models\Album;
use Carbon\Carbon;

class StatController extends Controller
{
    /**
     * Affiche le tableau de bord des statistiques
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_users' => User::count(),
            'total_sales' => Order::where('status', 'completed')->sum('total_price'),
            'total_orders' => Order::count(),
            'total_songs' => Song::count(),
            'total_albums' => Album::count(),
            'new_users_today' => User::whereDate('created_at', Carbon::today())->count(),
            'orders_today' => Order::whereDate('created_at', Carbon::today())->count(),
            'sales_today' => Order::whereDate('created_at', Carbon::today())->where('status', 'completed')->sum('total_price'),
        ];
        
        // Statistiques des ventes sur les 30 derniers jours
        $salesData = $this->getSalesDataByPeriod(30);
        
        // Top 5 des chansons les plus vendues
        $topSongs = DB::table('order_items')
            ->join('songs', function($join) {
                $join->on('order_items.item_id', '=', 'songs.id')
                    ->where('order_items.item_type', '=', Song::class);
            })
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select('songs.id', 'songs.title', 'songs.artist', DB::raw('COUNT(*) as sales_count'))
            ->groupBy('songs.id', 'songs.title', 'songs.artist')
            ->orderBy('sales_count', 'desc')
            ->take(5)
            ->get();
        
        // Top 5 des albums les plus vendus
        $topAlbums = DB::table('order_items')
            ->join('albums', function($join) {
                $join->on('order_items.item_id', '=', 'albums.id')
                    ->where('order_items.item_type', '=', Album::class);
            })
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select('albums.id', 'albums.title', 'albums.artist', DB::raw('COUNT(*) as sales_count'))
            ->groupBy('albums.id', 'albums.title', 'albums.artist')
            ->orderBy('sales_count', 'desc')
            ->take(5)
            ->get();
        
        // Répartition des utilisateurs par date d'inscription (par mois)
        $usersByMonth = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as user_count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(12)
            ->get();
        
        return view('admin.stats.index', compact(
            'stats',
            'salesData',
            'topSongs',
            'topAlbums',
            'usersByMonth'
        ));
    }
    
    /**
     * Récupère les données de ventes sur une période donnée
     * 
     * @param  int  $days
     * @return \Illuminate\Support\Collection
     */
    private function getSalesDataByPeriod($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }
}
