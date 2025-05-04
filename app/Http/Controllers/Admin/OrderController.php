<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur responsable de la gestion administrative des commandes
 * 
 * Gère l'affichage, la modification du statut et le suivi des commandes
 * dans l'interface d'administration
 */
class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes pour l'administration
     * Route: GET /admin/orders
     * Route Name: admin.orders.index
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Filtres possibles
        $status = $request->query('status');
        $userId = $request->query('user');
        
        // Construction de la requête avec filtre
        $ordersQuery = Order::with('user');
        
        if ($status) {
            $ordersQuery->where('status', $status);
        }
        
        if ($userId) {
            $ordersQuery->where('user_id', $userId);
        }
        
        // Récupération des commandes avec pagination
        $orders = $ordersQuery->latest()->paginate(15);
        
        // Statistiques pour affichage en haut de page
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'paid' => Order::where('status', 'paid')->count(),
            'failed' => Order::where('status', 'failed')->count(),
            'total_revenue' => Order::where('status', 'paid')->sum('total_price'),
        ];
        
        // Liste des utilisateurs pour le filtre
        $users = User::has('orders')->get();
        
        return view('admin.orders.index', compact('orders', 'stats', 'users', 'status', 'userId'));
    }

    /**
     * Affiche les détails d'une commande dans l'administration
     * Route: GET /admin/orders/{order}
     * Route Name: admin.orders.show
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        // Charge les items de la commande et l'utilisateur
        $order->load([
            'orderItems.item', // Charge les items polymorphiques (albums ou chansons)
            'user'
        ]);
        
        // Historique des changements de statut (si disponible)
        $statusHistory = DB::table('order_status_history')
            ->where('order_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Commandes précédentes de l'utilisateur (pour contexte)
        $previousOrders = Order::where('user_id', $order->user_id)
            ->where('id', '!=', $order->id)
            ->latest()
            ->take(5)
            ->get();
        
        return view('admin.orders.show', compact('order', 'statusHistory', 'previousOrders'));
    }

    /**
     * Met à jour le statut d'une commande
     * Route: PATCH /admin/orders/{order}/status
     * Route Name: admin.orders.status.update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Validation des données
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,paid,failed,refunded',
            'note' => 'nullable|string|max:500',
        ]);

        // Enregistre le statut précédent pour l'historique
        $oldStatus = $order->status;
        
        // Mise à jour du statut
        $order->update([
            'status' => $validated['status'],
        ]);
        
        // Enregistre le changement de statut dans l'historique (si table disponible)
        try {
            DB::table('order_status_history')->insert([
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'changed_by' => auth()->id(),
                'note' => $validated['note'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // La table d'historique n'existe peut-être pas, on continue sans erreur
        }
        
        // Notification de l'utilisateur si le statut passe à payé (selon configuration)
        if ($validated['status'] === 'paid' && $oldStatus !== 'paid') {
            // On pourrait envoyer un e-mail à l'utilisateur ici
        }
        
        return redirect()->route('admin.orders.show', $order)
            ->with('admin_success', 'Statut de la commande mis à jour avec succès.');
    }
    
    /**
     * Génère un rapport des ventes au format CSV
     * Route: GET /admin/orders/report
     * Route Name: admin.orders.report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function generateReport(Request $request)
    {
        // Filtres pour le rapport
        $startDate = $request->input('start_date') ? date('Y-m-d', strtotime($request->input('start_date'))) : null;
        $endDate = $request->input('end_date') ? date('Y-m-d', strtotime($request->input('end_date'))) : null;
        $status = $request->input('status');
        
        // Construction de la requête
        $ordersQuery = Order::with(['user', 'orderItems.item']);
        
        if ($startDate) {
            $ordersQuery->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $ordersQuery->whereDate('created_at', '<=', $endDate);
        }
        
        if ($status) {
            $ordersQuery->where('status', $status);
        }
        
        // Tri par date
        $ordersQuery->orderBy('created_at', 'desc');
        
        // Récupération des données
        $orders = $ordersQuery->get();
        
        // Génération du CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-report-' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // En-têtes de colonnes
            fputcsv($file, [
                'ID', 'Date', 'Client', 'Email', 'Nb Articles', 'Total', 'Statut'
            ]);
            
            // Données
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->user->name,
                    $order->user->email,
                    $order->orderItems->count(),
                    number_format($order->total_price, 2, ',', ' ') . ' €',
                    $order->status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
