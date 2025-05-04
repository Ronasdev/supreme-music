<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Définition des paramètres généraux
        $settings = [
            // Général
            [
                'key' => 'site_name',
                'value' => 'Supreme Musique',
                'type' => 'string',
                'group' => 'general',
                'display_name' => 'Nom du site',
                'description' => 'Le nom affiché dans le titre du site et les emails',
            ],
            [
                'key' => 'site_description',
                'value' => 'Plateforme de vente et streaming de musique en ligne',
                'type' => 'string',
                'group' => 'general',
                'display_name' => 'Description du site',
                'description' => 'Courte description utilisée dans les métadonnées SEO',
            ],
            [
                'key' => 'contact_email',
                'value' => 'contact@supreme-musique.com',
                'type' => 'string',
                'group' => 'contact',
                'display_name' => 'Email de contact',
                'description' => 'Adresse email pour les formulaires de contact',
            ],
            [
                'key' => 'admin_email',
                'value' => 'admin@supreme-musique.com',
                'type' => 'string',
                'group' => 'contact',
                'display_name' => 'Email d\'administration',
                'description' => 'Adresse email pour les notifications administratives',
            ],
            
            // Paramètres de vente
            [
                'key' => 'currency',
                'value' => '€',
                'type' => 'string',
                'group' => 'payment',
                'display_name' => 'Devise',
                'description' => 'Devise utilisée pour les prix (EUR, USD, etc.)',
            ],
            [
                'key' => 'vat_rate',
                'value' => '20',
                'type' => 'integer',
                'group' => 'payment',
                'display_name' => 'Taux de TVA',
                'description' => 'Taux de TVA appliqué aux ventes (en %)',
            ],
            
            // Paramètres d'affichage
            [
                'key' => 'items_per_page',
                'value' => '12',
                'type' => 'integer',
                'group' => 'display',
                'display_name' => 'Eléments par page',
                'description' => 'Nombre d\'albums ou chansons affichés par page',
            ],
            [
                'key' => 'show_latest_albums',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'display',
                'display_name' => 'Afficher les derniers albums',
                'description' => 'Afficher les derniers albums sur la page d\'accueil',
            ],
            
            // Fonctionnalités
            [
                'key' => 'enable_streaming',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'display_name' => 'Activer le streaming',
                'description' => 'Permet aux utilisateurs d\'écouter de la musique en streaming',
            ],
            [
                'key' => 'enable_downloads',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'display_name' => 'Activer les téléchargements',
                'description' => 'Permet aux utilisateurs de télécharger la musique achetée',
            ],
            
            // Maintenance
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'system',
                'display_name' => 'Mode maintenance',
                'description' => 'Activer le mode maintenance (seuls les administrateurs peuvent accéder au site)',
            ],
        ];
        
        // Insérer les paramètres dans la base de données
        foreach ($settings as $setting) {
            \DB::table('settings')->insertOrIgnore($setting);
        }
    }
}
