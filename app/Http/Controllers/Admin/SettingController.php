<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingController extends Controller
{
    /**
     * Affiche la page des paramètres du site
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Vérifie si la table settings existe
        $settingsExists = Schema::hasTable('settings');
        
        // Récupérer les paramètres généraux de l'application
        if ($settingsExists) {
            $settings = DB::table('settings')->get()->keyBy('key');
        } else {
            // Si la table n'existe pas, utiliser des valeurs par défaut
            $settings = collect([
                'site_name' => (object) ['key' => 'site_name', 'value' => config('app.name', 'Supreme Musique')],
                'contact_email' => (object) ['key' => 'contact_email', 'value' => config('mail.from.address', 'contact@exemple.com')],
                'currency' => (object) ['key' => 'currency', 'value' => '€'],
                'vat_rate' => (object) ['key' => 'vat_rate', 'value' => '20'],
                'items_per_page' => (object) ['key' => 'items_per_page', 'value' => '12'],
                'enable_streaming' => (object) ['key' => 'enable_streaming', 'value' => 'true'],
                'maintenance_mode' => (object) ['key' => 'maintenance_mode', 'value' => 'false'],
            ]);
        }
        
        // Informations sur l'application
        $appInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'database' => config('database.default'),
        ];
        
        // Informations sur le stockage
        $storageInfo = [
            'public_disk_size' => $this->formatSize($this->getDiskSize('public')),
            'total_media_files' => DB::table('media')->count(),
            'total_media_size' => $this->formatSize(DB::table('media')->sum('size')),
        ];
        
        return view('admin.settings.index', compact('settings', 'appInfo', 'storageInfo'));
    }
    
    /**
     * Met à jour les paramètres du site
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Valider les paramètres
        $validated = $request->validate([
            'site_name' => 'required|string|max:100',
            'site_description' => 'nullable|string|max:255',
            'contact_email' => 'required|email|max:100',
            'maintenance_mode' => 'boolean',
            'default_currency' => 'required|string|size:3',
            'enable_user_registration' => 'boolean',
            'enable_guest_checkout' => 'boolean',
        ]);
        
        // Mettre à jour les paramètres dans la base de données
        foreach ($validated as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }
        
        // Gérer le mode maintenance
        if ($request->has('maintenance_mode')) {
            if ($request->maintenance_mode) {
                Artisan::call('down');
            } else {
                Artisan::call('up');
            }
        }
        
        // Vider le cache des paramètres
        Cache::forget('app_settings');
        
        return redirect()->route('admin.settings')
            ->with('admin_success', 'Les paramètres ont été mis à jour avec succès.');
    }
    
    /**
     * Effectue les opérations de maintenance
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function maintenance(Request $request)
    {
        try {
            $action = $request->input('action');
            $message = '';
            
            switch ($action) {
                case 'clear_cache':
                    Artisan::call('cache:clear');
                    $message = 'Le cache a été vidé avec succès.';
                    break;
                    
                case 'clear_view_cache':
                    Artisan::call('view:clear');
                    $message = 'Le cache des vues a été vidé avec succès.';
                    break;
                
                case 'optimize':
                    Artisan::call('optimize');
                    $message = 'L\'application a été optimisée avec succès.';
                    break;
                
                default:
                    return redirect()->route('admin.settings')
                        ->with('admin_error', 'Action de maintenance non reconnue.');
            }
            
            return redirect()->route('admin.settings')
                ->with('admin_success', $message);
                
        } catch (\Exception $e) {
            return redirect()->route('admin.settings')
                ->with('admin_error', 'Erreur lors de la maintenance : ' . $e->getMessage());
        }
    }
    
    /**
     * Obtient la taille d'un disque de stockage
     * 
     * @param  string  $disk
     * @return int
     */
    private function getDiskSize($disk)
    {
        $path = storage_path('app/public');
        $size = 0;
        
        foreach (File::allFiles($path) as $file) {
            $size += $file->getSize();
        }
        
        return $size;
    }
    
    /**
     * Formate une taille en octets en unité lisible (KB, MB, GB)
     * 
     * @param  int  $bytes
     * @return string
     */
    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
