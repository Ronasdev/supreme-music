<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
// use Spatie\MediaLibrary\MediaCollections\Contracts\HasMedia;
use Spatie\MediaLibrary\HasMedia;

class Album extends Model implements HasMedia {
    use HasFactory, InteractsWithMedia; // Utilisation de InteractsWithMedia de Spatie pour la gestion des médias
    
    /**
     * Attributs assignables en masse
     *
     * @var array
     */
    protected $fillable = ['title','description','artist','price','cover_image'];

    /**
     * Relation polymorphique avec les éléments de commande
     * Un album peut faire partie de plusieurs éléments de commande
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'item');
    }

    /**
     * Relation avec les chansons de l'album
     * Un album contient plusieurs chansons
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function songs()
    {
        return $this->hasMany(Song::class);
    }
    
    /**
     * Configuration des collections de médias pour Spatie MediaLibrary
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        // Collection pour la pochette de l'album
        $this->addMediaCollection('cover')
            ->singleFile() // Une seule image de couverture à la fois
            ->useDisk('public'); // Stockage sur le disque public
    }

}
