<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes de l'utilisateur connecté
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupère toutes les commandes de l'utilisateur connecté
        $orders = Auth::user()->orders()->latest()->paginate(10);
        
        return view('orders.index', compact('orders'));
    }

    /**
     * Affiche le panier d'achat de l'utilisateur
     * 
     * @return \Illuminate\View\View
     */
    public function cart()
    {
        // Récupère le panier de la session
        $cart = Session::get('cart', []);
        $items = [];
        $total = 0;
        
        // Débogage - Afficher la structure actuelle du panier
        \Illuminate\Support\Facades\Log::info('Structure du panier', [
            'cart' => $cart,
            'keys' => array_keys($cart)
        ]);
        
        // Récupère les détails pour chaque élément du panier
        foreach ($cart as $cartKey => $item) {
            // Voici le problème : le format de la clé n'est pas utilisé correctement
            // La clé du panier est au format "id_type" comme "5_album"
            // Nous devons extraire l'ID réel et le type de cette clé
            
            // Première solution : extraire les parties de la clé cartKey
            $parts = explode('_', $cartKey);
            
            // Vérifier que nous avons un format valide (doit avoir au moins deux parties)
            if (count($parts) >= 2) {
                $id = $parts[0]; // L'ID est la première partie
                $type = $parts[1]; // Le type est la seconde partie
            } else {
                // Si la clé n'est pas au bon format, utiliser les valeurs du panier
                $id = $item['id'];
                $type = $item['type'];
            }
            
            // Charger le produit en fonction du type
            if ($type === 'album') {
                $product = Album::find($id);
                if ($product) {
                    $items[] = [
                        'id' => $id,
                        'type' => 'album',
                        'cart_key' => $cartKey, // Stocke la clé exacte du panier pour la suppression
                        'product' => $product,
                        'price' => $product->price,
                    ];
                    $total += $product->price;
                }
            } elseif ($type === 'song') {
                $product = Song::find($id);
                if ($product) {
                    $items[] = [
                        'id' => $id,
                        'type' => 'song',
                        'cart_key' => $cartKey, // Stocke la clé exacte du panier pour la suppression
                        'product' => $product,
                        'price' => $product->price,
                    ];
                    $total += $product->price;
                }
            }
        }
        
        return view('cart.show', compact('items', 'total'));
    }

    /**
     * Crée une nouvelle commande à partir du panier
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Vérifie si le panier est vide
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.show')
                ->with('error', 'Votre panier est vide');
        }
        
        // Calcule le total de la commande
        $total = 0;
        $items = [];
        
        foreach ($cart as $cartKey => $item) {
            // Récupérer l'ID numérique depuis le cartKey ou depuis l'item
            $itemId = $item['id']; // Utiliser l'ID stocké dans le tableau
            $itemType = $item['type'];
            
            if ($itemType === 'album') {
                $product = Album::find($itemId);
                if ($product) {
                    $total += $product->price;
                    $items[] = [
                        'id' => $itemId, // ID numérique
                        'type' => 'album',
                        'price' => $product->price,
                    ];
                }
            } elseif ($itemType === 'song') {
                $product = Song::find($itemId);
                if ($product) {
                    $total += $product->price;
                    $items[] = [
                        'id' => $itemId, // ID numérique
                        'type' => 'song',
                        'price' => $product->price,
                    ];
                }
            }
        }
        
        // Crée la commande
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $total,
            'status' => 'pending',
        ]);
        
        // Crée les éléments de commande
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $item['id'],
                'item_type' => $item['type'],
                'price' => $item['price'],
            ]);
        }
        
        // Vide le panier
        Session::forget('cart');
        
        // Redirige vers la page de paiement
        return redirect()->route('payments.show', $order)
            ->with('success', 'Commande créée avec succès !');
    }

    /**
     * Affiche les détails d'une commande
     * 
     * @param  Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        // Vérifie que l'utilisateur est bien le propriétaire de la commande
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('orders.index')
                ->with('error', 'Commande non autorisée');
        }
        
        // Charge les items de la commande avec leurs relations
        $order->load('orderItems');
        
        return view('orders.show', compact('order'));
    }

    /**
     * Ajoute un produit au panier (album ou chanson)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToCart(Request $request)
    {
        // Validation des données
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|in:album,song',
        ]);
        
        $id = $request->input('id');
        $type = $request->input('type');
        
        // Vérifie si le produit existe
        if ($type === 'album') {
            $product = Album::find($id);
        } else {
            $product = Song::find($id);
        }
        
        if (!$product) {
            return back()->with('error', 'Produit non trouvé');
        }
        
        // Récupère le panier actuel ou crée un nouveau
        $cart = Session::get('cart', []);
        
        // Génère la clé unique pour le panier (id_type)
        $cartKey = $id . '_' . $type;
        
        // Vérifie si le produit n'est pas déjà dans le panier
        if (isset($cart[$cartKey])) {
            return back()->with('info', 'Ce produit est déjà dans votre panier');
        }
        
        // Ajoute le produit au panier
        $cart[$cartKey] = [
            'id' => $id,
            'type' => $type,
            'added_at' => now(),
        ];
        
        // Enregistre le panier dans la session
        Session::put('cart', $cart);
        
        return redirect()->back()->with('success', 'Produit ajouté au panier');
    }

    /**
     * Retire un produit du panier
     * 
     * @param  string  $id  Format: id_type (ex: 1_album, 2_song)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFromCart($id)
    {
        // Récupère le panier actuel
        $cart = Session::get('cart', []);
        
        // La clé du panier est au format "id_type" (ex: "5_album", "10_song")
        // Cette clé est utilisée à la fois dans addToCart et dans la vue
        
        // Vérifie si le produit est dans le panier avec la clé transmise
        if (isset($cart[$id])) {
            // Récupère les informations de l'article pour le message de confirmation
            $itemType = $cart[$id]['type'] === 'album' ? 'Album' : 'Chanson';
            
            // Retire le produit du panier
            unset($cart[$id]);
            
            // Enregistre le panier mis à jour dans la session
            Session::put('cart', $cart);
            
            return back()->with('success', $itemType . ' retiré(e) du panier avec succès');
        }
        
        // Si l'article n'est pas trouvé, enregistre des informations de débogage
        \Illuminate\Support\Facades\Log::warning('Tentative de suppression d\'un produit non trouvé dans le panier', [
            'cart_keys' => array_keys($cart),
            'requested_id' => $id,
        ]);
        
        return back()->with('error', 'Produit non trouvé dans le panier');
    }

    /**
     * Affiche les détails d'une commande (pour l'admin)
     * 
     * @param  Order  $order
     * @return \Illuminate\View\View
     */
    public function adminShow(Order $order)
    {
        // Charge les items de la commande avec leurs relations
        $order->load(['orderItems', 'user']);
        
        return view('admin.orders.show', compact('order'));
    }
    
    /**
     * Met à jour le statut d'une commande (pour l'admin)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Validation des données
        $request->validate([
            'status' => 'required|in:pending,paid,failed',
        ]);
        
        // Mise à jour du statut
        $order->update([
            'status' => $request->status,
        ]);
        
        return back()->with('success', 'Statut de la commande mis à jour');
    }

    /**
     * Vide complètement le panier
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCart()
    {
        // Supprime le panier de la session
        Session::forget('cart');
        
        return redirect()->route('cart.show')
            ->with('success', 'Votre panier a été vidé avec succès');
    }
}
