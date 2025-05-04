<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer les utilisateurs de test
        $this->createUsers();
        
        // Exécuter les seeders dans un ordre spécifique
        // Albums en premier, puis chansons qui dépendent des albums
        $this->call([
            AlbumSeeder::class,  // Génère les albums
            SongSeeder::class,   // Génère les chansons liées aux albums et singles
        ]);
    }
    
    /**
     * Crée les utilisateurs de test, incluant un administrateur
     * et plusieurs utilisateurs standards
     */
    private function createUsers(): void
    {
        // Créer un administrateur seulement s'il n'existe pas déjà
        if (!User::where('email', 'admin@example.com')->exists()) {
            // Création et commentaires détaillés des attributs de l'administrateur
            User::create([
                'name' => 'Admin', // Nom de l'administrateur
                'email' => 'admin@example.com', // Email pour la connexion (unique)
                'password' => Hash::make('password'), // Mot de passe hashé pour sécurité
                'role' => 'admin', // Rôle d'administrateur pour accès complet
                'email_verified_at' => now(), // Email déjà vérifié pour éviter cette étape
            ]);
            
            \Log::info('Utilisateur Admin créé'); // Log pour confirmer la création
        } else {
            \Log::info('Utilisateur Admin existe déjà'); // Log si déjà existant
        }
        
        // Créer un utilisateur normal de test seulement s'il n'existe pas déjà
        if (!User::where('email', 'user@example.com')->exists()) {
            // Création et commentaires détaillés des attributs de l'utilisateur standard
            User::create([
                'name' => 'Utilisateur', // Nom d'utilisateur standard
                'email' => 'user@example.com', // Email pour la connexion (unique)
                'password' => Hash::make('password'), // Mot de passe hashé
                'role' => 'user', // Rôle utilisateur standard (accès limité)
                'email_verified_at' => now(), // Email déjà vérifié
            ]);
            
            \Log::info('Utilisateur standard créé'); // Log pour confirmer la création
        } else {
            \Log::info('Utilisateur standard existe déjà'); // Log si déjà existant
        }
        
        // Compter le nombre d'utilisateurs actuels (hors admin)
        $userCount = User::where('role', 'user')->count();
        
        // Ne créer des utilisateurs supplémentaires que si nécessaire
        if ($userCount < 10) {
            // Déterminer combien d'utilisateurs à créer
            $countToCreate = 10 - $userCount;
            
            // Créer le nombre nécessaire d'utilisateurs supplémentaires avec la factory
            User::factory($countToCreate)->create([
                'role' => 'user', // Tous auront le rôle utilisateur standard
                'email_verified_at' => now(), // Tous auront leur email vérifié
            ]);
            
            \Log::info("$countToCreate nouveaux utilisateurs créés"); // Log pour confirmer
        } else {
            \Log::info('Nombre suffisant d\'utilisateurs déjà présents'); // Log si suffisant
        }
    }
}
