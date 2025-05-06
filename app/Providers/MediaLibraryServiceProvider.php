<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class MediaLibraryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Rien à faire ici
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Adapter la configuration pour s'assurer que les médias sont servis depuis l'URL correcte
        // Cette approche est beaucoup plus simple et directe
        $this->correctMediaUrls();
    }

    /**
     * S'assure que les URLs sont correctement formatées et utilisées
     * Cette méthode adapte dynamiquement la configuration de Spatie MediaLibrary
     * pour fonctionner avec notre setup spécifique
     */
    protected function correctMediaUrls(): void
    {
        if (app()->environment('local')) {
            // 1. Définir l'URL du disque public
            Config::set('filesystems.disks.public.url', config('app.url') . '/storage');

            // 2. Adapter l'URL du chemin vers les médias - PLUS IMPORTANT
            Config::set('media-library.path_generator', \App\Support\CustomPathGenerator::class);
            
            // 3. Définir le préfixe (vide pour éviter les doublons)
            Config::set('media-library.prefix', '');
        }
    }

    // La méthode boot est déjà définie ci-dessus
}
