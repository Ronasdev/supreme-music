<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Affiche la page de paiement pour une commande
     * 
     * @param Order $order - La commande à payer
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        // Vérifie que l'utilisateur est bien le propriétaire de la commande
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('orders.index')
                ->with('error', 'Commande non autorisée');
        }
        
        // Vérifie que la commande est en attente de paiement
        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Cette commande a déjà été traitée');
        }
        
        return view('payments.show', compact('order'));
    }
    
    /**
     * Initialise le paiement via Orange Money
     * 
     * @param Request $request
     * @param Order $order - La commande à payer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function initiate(Request $request, Order $order)
    {
        // Vérifie que l'utilisateur est bien le propriétaire de la commande
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('orders.index')
                ->with('error', 'Commande non autorisée');
        }
        
        // Vérifie que la commande est en attente de paiement
        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Cette commande a déjà été traitée');
        }
        
        // Validation du numéro de téléphone Orange Money
        $request->validate([
            'phone_number' => 'required|regex:/^[0-9]{8,15}$/'
        ]);
        
        try {
            // À IMPLÉMENTER : Intégration réelle avec l'API Orange Money
            // Ceci est un exemple de code à remplacer par l'intégration réelle
            
            // Simulation d'une réponse de l'API Orange Money
            $transactionId = 'OM-' . uniqid();
            
            // Mise à jour de la commande avec l'ID de transaction
            $order->update([
                'payment_method' => 'orange_money',
                'transaction_id' => $transactionId
            ]);
            
            // Redirection vers la page de confirmation
            return redirect()->route('payments.confirm', $order)
                ->with('info', 'Paiement initié, veuillez confirmer la transaction sur votre téléphone.');
                
        } catch (\Exception $e) {
            // Log l'erreur
            Log::error('Erreur d\'initiation de paiement Orange Money: ' . $e->getMessage());
            
            // Redirection avec message d'erreur
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'initiation du paiement: ' . $e->getMessage());
        }
    }
    
    /**
     * Confirmation de paiement (après redirection depuis Orange Money)
     * 
     * @param Order $order - La commande payée
     * @return \Illuminate\View\View
     */
    public function confirm(Order $order)
    {
        // Vérifie que l'utilisateur est bien le propriétaire de la commande
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('orders.index')
                ->with('error', 'Commande non autorisée');
        }
        
        return view('payments.confirm', compact('order'));
    }
    
    /**
     * Webhook pour recevoir les notifications de paiement d'Orange Money
     * Cette route devrait être accessible publiquement
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        // Validation de la requête
        // À adapter selon la documentation de l'API Orange Money
        $payload = $request->all();
        
        // Log la requête pour debugging
        Log::info('Webhook Orange Money reçu', $payload);
        
        // À IMPLÉMENTER : Vérification de la signature du webhook
        // pour s'assurer qu'il vient bien d'Orange Money
        
        try {
            // Récupération de l'ID de transaction
            $transactionId = $payload['transaction_id'] ?? null;
            $status = $payload['status'] ?? null;
            
            if (!$transactionId) {
                return response()->json(['error' => 'Transaction ID manquant'], 400);
            }
            
            // Recherche de la commande associée
            $order = Order::where('transaction_id', $transactionId)->first();
            
            if (!$order) {
                return response()->json(['error' => 'Commande non trouvée'], 404);
            }
            
            // Mise à jour du statut de la commande selon la réponse d'Orange Money
            if ($status === 'success') {
                $order->update(['status' => 'paid']);
                
                // Ajoute les chansons/albums aux playlists de l'utilisateur si nécessaire
                // Cette logique peut être déplacée dans un Job si complexe
                $this->processPurchases($order);
            } else {
                $order->update(['status' => 'failed']);
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            // Log l'erreur
            Log::error('Erreur de traitement webhook Orange Money: ' . $e->getMessage());
            
            // Retourne une erreur 500 mais confirme la réception pour éviter les retransmissions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Traite les achats d'une commande confirmée
     * 
     * @param Order $order - La commande payée
     * @return void
     */
    private function processPurchases(Order $order)
    {
        // Cette méthode pourrait être déplacée dans un Job si complexe
        
        // Traitement pour chaque élément de la commande
        foreach ($order->orderItems as $item) {
            // Logique spécifique selon le type d'item
            if ($item->item_type === 'song') {
                // Logique pour l'achat d'une chanson
                // Par exemple, ajouter à une playlist "Achats" par défaut
            } elseif ($item->item_type === 'album') {
                // Logique pour l'achat d'un album
            }
        }
    }
}
