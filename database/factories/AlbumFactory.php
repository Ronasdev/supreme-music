<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Album>
 */
class AlbumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Liste des genres musicaux populaires pour un contenu diversifié
        $genres = [
            'Pop', 'Rock', 'Hip-Hop', 'R&B', 'Classique', 'Jazz',
            'Électronique', 'Folk', 'Country', 'Reggae', 'Soul', 'Blues',
            'Funk', 'Métal', 'Punk', 'Alternative', 'Indie', 'Disco'
        ];
        
        // Création d'un prix aléatoire entre 4.99 et 19.99 avec deux décimales
        $price = round(mt_rand(499, 1999) / 100, 2);
        
        // Années possibles pour les albums (de 1970 à aujourd'hui)
        $year = mt_rand(1970, date('Y'));
        
        return [
            'title' => $this->faker->sentence(mt_rand(1, 4)), // Titre d'album avec 1 à 4 mots
            'description' => $this->faker->paragraph(mt_rand(3, 6)), // Description détaillée
            'artist' => $this->faker->name(), // Nom de l'artiste
            'genre' => $this->faker->randomElement($genres), // Genre musical aléatoire
            'year' => $year, // Année de sortie
            'price' => $price, // Prix aléatoire
            'views_count' => mt_rand(0, 10000), // Nombre de vues/popularité
            'created_at' => $this->faker->dateTimeBetween('-2 years', '-1 day'), // Date d'ajout à la base (toujours dans le passé)
            'updated_at' => $this->faker->dateTimeBetween('-1 day', '-1 hour'), // Dernière mise à jour (toujours après created_at mais dans le passé)
        ];
    }
}
