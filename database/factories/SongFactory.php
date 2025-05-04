<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Durées typiques de chansons (entre 2 et 5 minutes)
        $minutes = mt_rand(2, 5);
        $seconds = mt_rand(0, 59);
        $duration = sprintf('%d:%02d', $minutes, $seconds);
        
        // Prix des chansons individuelles (entre 0.99€ et 2.99€)
        $price = round(mt_rand(99, 299) / 100, 2);
        
        // Récupération de l'ID d'un album existant ou null
        // Le champ album_id peut être null pour les singles
        $albumId = null;
        if (mt_rand(0, 10) > 3) { // 70% des chansons appartiennent à un album
            $albumIds = Album::pluck('id')->toArray();
            if (!empty($albumIds)) {
                $albumId = $this->faker->randomElement($albumIds);
            }
        }
        
        return [
            'title' => $this->faker->sentence(mt_rand(1, 6)), // Titre de chanson
            'description' => $this->faker->paragraph(mt_rand(2, 5)), // Description de la chanson
            'album_id' => $albumId, // Association à un album (ou null pour single)
            'artist' => $this->faker->name(), // Artiste peut être différent de l'album
            'duration' => $duration, // Format MM:SS
            'price' => $price, // Prix individuel
            'track_number' => $albumId ? mt_rand(1, 12) : null, // Numéro de piste dans l'album
            'lyrics' => $this->faker->paragraphs(mt_rand(3, 8), true), // Paroles
            'bpm' => mt_rand(60, 180), // Battements par minute (tempo)
            'created_at' => $this->faker->dateTimeBetween('-2 years', '-1 day'), // Toujours dans le passé
            'updated_at' => $this->faker->dateTimeBetween('-1 day', '-1 hour'), // Toujours après created_at mais dans le passé
        ];
    }
    
    /**
     * Configure la chanson comme appartenant à un album spécifique
     *
     * @param int $albumId ID de l'album auquel associer la chanson
     * @return static
     */
    public function forAlbum(int $albumId): static
    {
        return $this->state(function (array $attributes) use ($albumId) {
            // Récupération des informations de l'album
            $album = Album::find($albumId);
            
            return [
                'album_id' => $albumId,
                'artist' => $album ? $album->artist : $attributes['artist'], // Réutilisation de l'artiste de l'album
                'track_number' => $album ? (Song::where('album_id', $albumId)->count() + 1) : null,
            ];
        });
    }
}
