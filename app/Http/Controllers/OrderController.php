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
        
        // Récupère les détails pour chaque élément du panier
        foreach ($cart as $id => $item) {
            if ($item['type'] === 'album') {
                $product = Album::find($id);
                if ($product) {
                    $items[] = [
                        'id' => $id,
                        'type' => 'album',
                        'product' => $product,
                        'price' => $product->price,
                    ];
                    $total += $product->price;
                }
            } elseif ($item['type'] === 'song') {
                $product = Song::find($id);
                if ($product) {
                    $items[] = [
                        'id' => $id,
                        'type' => 'song',
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
        
        foreach ($cart as $id => $item) {
            if ($item['type'] === 'album') {
                $product = Album::find($id);
                if ($product) {
                    $total += $product->price;
                    $items[] = [
                        'id' => $id,
                        'type' => 'album',
                        'price' => $product->price,
                    ];
                }
            } elseif ($item['type'] === 'song') {
                $product = Song::find($id);
                if ($product) {
                    $total += $product->price;
                    $items[] = [
                        'id' => $id,
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
        
        // Vérifie si le produit n'est pas déjà dans le panier
        if (isset($cart[$id . '_' . $type])) {
            return back()->with('info', 'Ce produit est déjà dans votre panier');
        }
        
        // Ajoute le produit au panier
        $cart[$id . '_' . $type] = [
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
        
        // Vérifie si le produit est dans le panier
        if (isset($cart[$id])) {
            // Retire le produit du panier
            unset($cart[$id]);
            
            // Enregistre le panier mis à jour dans la session
            Session::put('cart', $cart);
            
            return back()->with('success', 'Produit retiré du panier');
        }
        
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
