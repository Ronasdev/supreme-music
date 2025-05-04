<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Song extends Model implements HasMedia {
    use HasFactory, InteractsWithMedia; // Utilisation de InteractsWithMedia pour la gestion des fichiers audio
    
    /**
     * Attributs assignables en masse
     *
     * @var array
     */
    protected $fillable = ['title','description','price','duration','album_id'];

    /**
     * Relation polymorphique avec les éléments de commande
     * Une chanson peut faire partie de plusieurs éléments de commande
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'item');
    }
    
    /**
     * Relation many-to-many avec les playlists
     * Une chanson peut être dans plusieurs playlists
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class);
    }

    /**
     * Relation avec l'album auquel appartient la chanson
     * Une chanson appartient à un seul album
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function album()
    {
        return $this->belongsTo(Album::class);
    }
    
    /**
     * Configuration des collections de médias pour Spatie MediaLibrary
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        // Collection pour le fichier audio
        $this->addMediaCollection('audio')
            ->singleFile() // Un seul fichier audio par chanson
            ->acceptsMimeTypes(['audio/mpeg', 'audio/mp3', 'audio/wav']) // N'accepte que les formats audio
            ->useDisk('public'); // Stockage sur le disque public
    }
}
