<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur responsable de la gestion administrative des paiements
 * 
 * Gère le suivi, la vérification et les rapports des paiements
 * dans l'interface d'administration
 */
class PaymentController extends Controller
{
    /**
     * Affiche la liste des paiements dans l'administration
     * Route: GET /admin/payments
     * Route Name: admin.payments.index
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Filtres possibles
        $status = $request->query('status');
        $userId = $request->query('user');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        
        // Construction de la requête avec filtres
        $paymentsQuery = Order::with('user')->whereNotNull('transaction_id');
        
        if ($status) {
            $paymentsQuery->where('status', $status);
        }
        
        if ($userId) {
            $paymentsQuery->where('user_id', $userId);
        }
        
        if ($startDate) {
            $paymentsQuery->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $paymentsQuery->whereDate('created_at', '<=', $endDate);
        }
        
        // Récupération des paiements avec pagination
        $payments = $paymentsQuery->latest()->paginate(15);
        
        // Statistiques pour le tableau de bord
        $stats = [
            'total' => Order::whereNotNull('transaction_id')->count(),
            'successful' => Order::where('status', 'paid')->count(),
            'failed' => Order::where('status', 'failed')->count(),
            'total_revenue' => Order::where('status', 'paid')->sum('total_price'),
        ];
        
        // Liste des utilisateurs pour le filtre
        $users = User::has('orders')->get();
        
        return view('admin.payments.index', compact('payments', 'stats', 'users', 'status', 'userId', 'startDate', 'endDate'));
    }

    /**
     * Affiche les détails d'un paiement dans l'administration
     * Route: GET /admin/payments/{order}
     * Route Name: admin.payments.show
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        // Vérification que l'ordre a un paiement associé
        if (!$order->transaction_id) {
            return redirect()->route('admin.orders.show', $order)
                ->with('admin_error', 'Cette commande n\'a pas de transaction de paiement associée.');
        }
        
        // Charge les items de la commande et l'utilisateur
        $order->load([
            'orderItems.item', // Charge les items polymorphiques (albums ou chansons)
            'user'
        ]);
        
        // Récupérer l'historique des logs de paiement pour cette transaction
        $paymentLogs = DB::table('payment_logs')
            ->where('transaction_id', $order->transaction_id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Si la table n'existe pas ou qu'il n'y a pas de logs, on crée un tableau vide
        $paymentLogs = $paymentLogs ?? collect();
        
        return view('admin.payments.show', compact('order', 'paymentLogs'));
    }

    /**
     * Marque un paiement comme remboursé
     * Route: POST /admin/payments/{order}/refund
     * Route Name: admin.payments.refund
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refund(Request $request, Order $order)
    {
        // Validation des données
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);
        
        try {
            // Vérification que la commande est bien payée
            if ($order->status !== 'paid') {
                return redirect()->back()
                    ->with('admin_error', 'Seules les commandes payées peuvent être remboursées.');
            }
            
            // Dans un système réel, on ferait appel à l'API de paiement pour effectuer le remboursement
            // Ici nous simulons simplement un remboursement réussi
            
            // Mise à jour du statut
            $order->update([
                'status' => 'refunded',
                'refunded_at' => now(),
                'refund_reason' => $request->reason,
            ]);
            
            // Enregistrement du log de remboursement
            try {
                DB::table('payment_logs')->insert([
                    'transaction_id' => $order->transaction_id,
                    'status' => 'refunded',
                    'amount' => $order->total_price,
                    'message' => 'Remboursement manuel par administrateur: ' . $request->reason,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // La table de logs n'existe peut-être pas, on continue sans erreur
                Log::info('Impossible d\'enregistrer le log de remboursement : ' . $e->getMessage());
            }
            
            return redirect()->route('admin.payments.show', $order)
                ->with('admin_success', 'Le remboursement a été effectué avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors du remboursement : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('admin_error', 'Une erreur est survenue lors du remboursement : ' . $e->getMessage());
        }
    }

    /**
     * Génère un rapport de paiements au format CSV
     * Route: GET /admin/payments/report
     * Route Name: admin.payments.report
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function generateReport(Request $request)
    {
        // Filtres pour le rapport
        $startDate = $request->input('start_date') ? date('Y-m-d', strtotime($request->input('start_date'))) : null;
        $endDate = $request->input('end_date') ? date('Y-m-d', strtotime($request->input('end_date'))) : null;
        $status = $request->input('status');
        
        // Construction de la requête
        $paymentsQuery = Order::with(['user'])->whereNotNull('transaction_id');
        
        if ($startDate) {
            $paymentsQuery->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $paymentsQuery->whereDate('created_at', '<=', $endDate);
        }
        
        if ($status) {
            $paymentsQuery->where('status', $status);
        }
        
        // Tri par date
        $paymentsQuery->orderBy('created_at', 'desc');
        
        // Récupération des données
        $payments = $paymentsQuery->get();
        
        // Génération du CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments-report-' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // En-têtes de colonnes
            fputcsv($file, [
                'ID', 'Transaction', 'Date', 'Client', 'Email', 'Méthode', 'Montant', 'Statut'
            ]);
            
            // Données
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->transaction_id,
                    $payment->created_at->format('d/m/Y H:i'),
                    $payment->user->name,
                    $payment->user->email,
                    $payment->payment_method,
                    number_format($payment->total_price, 2, ',', ' ') . ' €',
                    $payment->status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Vérifie le statut d'un paiement auprès du fournisseur
     * Route: POST /admin/payments/{order}/check
     * Route Name: admin.payments.check
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkStatus(Order $order)
    {
        try {
            // Vérification que la commande a une transaction
            if (!$order->transaction_id) {
                return redirect()->back()
                    ->with('admin_error', 'Cette commande n\'a pas de transaction de paiement associée.');
            }
            
            // Dans un système réel, on ferait appel à l'API de paiement pour vérifier le statut
            // Ici nous simulons une vérification réussie
            
            // Mise à jour du statut (simulation)
            $realStatus = $order->status; // Dans un cas réel, ce serait la réponse de l'API
            
            // Si le statut a changé, mise à jour
            if ($realStatus !== $order->status) {
                $order->update([
                    'status' => $realStatus,
                ]);
                
                return redirect()->route('admin.payments.show', $order)
                    ->with('admin_success', 'Le statut du paiement a été mis à jour : ' . $realStatus);
            }
            
            return redirect()->route('admin.payments.show', $order)
                ->with('admin_info', 'Le statut du paiement est correct : ' . $order->status);
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du statut : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('admin_error', 'Une erreur est survenue lors de la vérification : ' . $e->getMessage());
        }
    }
}
