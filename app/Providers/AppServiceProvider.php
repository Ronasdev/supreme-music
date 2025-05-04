<?php

namespace App\Providers;

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
    }
}
