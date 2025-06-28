<?php

namespace App\Providers;

use App\Helpers\CartHelper;
use App\Models\Album;
use App\Models\Song;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configuration pour les URL de Media Library
        // Aucune configuration supplémentaire nécessaire ici
        // Laravel storage:link devrait suffir
        
        // Définit la map des modèles pour les relations polymorphiques
        // Résout l'erreur "Class album not found"
        Relation::morphMap([
            'album' => Album::class,
            'song' => Song::class,
        ]);
        
        // Partage la fonction isInCart avec toutes les vues
        View::composer('*', function ($view) {
            $view->with('isInCart', function ($id, $type) {
                return CartHelper::isInCart($id, $type);
            });
        });
    }
}
