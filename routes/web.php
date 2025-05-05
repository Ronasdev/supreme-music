<?php

// Import des contrôleurs nécessaires
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;

// Import des contrôleurs administratifs
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AlbumController as AdminAlbumController;
use App\Http\Controllers\Admin\SongController as AdminSongController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// }); 

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes publiques accessibles sans authentification
Route::get('/', [HomeController::class, 'index'])->name('home'); // Page d'accueil
Route::get('/catalog', [HomeController::class, 'catalog'])->name('catalog'); // Catalogue complet
Route::get('/how-it-works', [HomeController::class, 'howItWorks'])->name('how-it-works'); // Fonctionnement du site
Route::get('/contact', [HomeController::class, 'contact'])->name('contact'); // Page de contact

// Routes de consultation publiques (albums et chansons)
Route::get('/albums', [AlbumController::class, 'index'])->name('albums.index'); // Liste des albums
Route::get('/albums/{album}', [AlbumController::class, 'show'])->name('albums.show'); // Détail d'un album
Route::get('/songs', [SongController::class, 'index'])->name('songs.index'); // Liste des chansons
Route::get('/songs/{song}', [SongController::class, 'show'])->name('songs.show'); // Détail d'une chanson
Route::get('/songs/{song}/preview', [SongController::class, 'preview'])->name('songs.preview'); // Prévisualisation audio

// Routes d'authentification gérées par Laravel Breeze

// Routes accessibles uniquement aux utilisateurs connectés
Route::middleware(['auth'])->group(function () {
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Playlists
    Route::resource('playlists', PlaylistController::class); // CRUD pour les playlists
    Route::post('/playlists/{playlist}/songs', [PlaylistController::class, 'addSong'])->name('playlists.songs.add'); // Ajouter une chanson
    Route::delete('/playlists/{playlist}/songs/{song}', [PlaylistController::class, 'removeSong'])->name('playlists.songs.remove'); // Retirer une chanson
    
    // Streaming
    Route::get('/stream/{song}', [StreamController::class, 'play'])->name('stream.play'); // Streaming d'une chanson
    Route::get('/player/{song}', [StreamController::class, 'player'])->name('player.show'); // Affichage du lecteur
    Route::get('/library', [StreamController::class, 'library'])->name('library'); // Bibliothèque musicale de l'utilisateur
    
    // Commandes et paiements
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'store']); // Gestion des commandes
    Route::get('/cart', [OrderController::class, 'cart'])->name('cart.show'); // Affichage du panier
    Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add'); // Ajout au panier
    Route::delete('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('cart.remove'); // Retrait du panier
    Route::delete('/cart/clear', [OrderController::class, 'clearCart'])->name('cart.clear'); // Vider le panier
    
    // Paiements
    Route::get('/payments/{order}', [PaymentController::class, 'show'])->name('payments.show'); // Page de paiement
    Route::post('/payments/{order}/initiate', [PaymentController::class, 'initiate'])->name('payments.initiate'); // Initiation du paiement
    Route::get('/payments/{order}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm'); // Confirmation du paiement
});

// Routes réservées aux administrateurs
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Tableau de bord de l'admin
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard'); // Dashboard admin
    
    // Gestion des albums
    Route::resource('albums', AdminAlbumController::class); // CRUD complet pour les albums
    
    // Gestion des chansons
    Route::resource('songs', AdminSongController::class); // CRUD complet pour les chansons
    
    // Gestion des playlists
    Route::resource('playlists', PlaylistController::class); // CRUD complet pour les playlists
    
    // Gestion des utilisateurs
    Route::resource('users', UserController::class); // CRUD pour les utilisateurs
    
    // Gestion des commandes
    Route::resource('orders', AdminOrderController::class)->except(['store', 'create', 'edit', 'update', 'destroy']); // Affichage des commandes uniquement
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status.update'); // Mise à jour du statut
    Route::get('/orders/report', [AdminOrderController::class, 'generateReport'])->name('orders.report'); // Génération de rapport de ventes
    
    // Gestion des paiements
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index'); // Liste des paiements
    Route::get('/payments/{order}', [AdminPaymentController::class, 'show'])->name('payments.show'); // Affichage d'un paiement
    Route::post('/payments/{order}/refund', [AdminPaymentController::class, 'refund'])->name('payments.refund'); // Remboursement d'un paiement
    Route::post('/payments/{order}/check', [AdminPaymentController::class, 'checkStatus'])->name('payments.check'); // Vérification du statut
    Route::get('/payments/report', [AdminPaymentController::class, 'generateReport'])->name('payments.report'); // Génération de rapport
    
    // Statistiques
    Route::get('/stats', [\App\Http\Controllers\Admin\StatController::class, 'index'])->name('stats'); // Statistiques générales
    
    // Paramètres du site
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings'); // Paramètres généraux
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update'); // Mise à jour des paramètres
    Route::post('/settings/maintenance', [\App\Http\Controllers\Admin\SettingController::class, 'maintenance'])->name('settings.maintenance'); // Actions de maintenance
});

// Webhook pour les notifications de paiement (accessible publiquement)
Route::post('/webhook/payments', [PaymentController::class, 'webhook'])->name('payments.webhook');

require __DIR__.'/auth.php';
