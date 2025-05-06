<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Album extends Model {
    use HasFactory;
    
    /**
     * Attributs assignables en masse
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'artist', 'price', 'cover_image'];

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
     * Obtient l'URL de l'image de couverture de l'album
     * 
     * @return string
     */
    public function getCoverImageUrl()
    {
        if (empty($this->cover_image)) {
            return asset('images/default-album-cover.jpg');
        }
        
        return asset('storage/covers/' . $this->id . '/' . $this->cover_image);
    }
    
    /**
     * Vérifie si l'album a une image de couverture
     * 
     * @return bool
     */
    public function hasCoverImage()
    {
        return !empty($this->cover_image) && Storage::disk('public')->exists('covers/' . $this->id . '/' . $this->cover_image);
    }
    
    /**
     * Définit l'image de couverture de l'album
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     */
    public function setCoverImage($file)
    {
        // Supprime l'ancienne image si elle existe
        if ($this->hasCoverImage()) {
            Storage::disk('public')->delete('covers/' . $this->id . '/' . $this->cover_image);
        }
        
        // Génère un nom de fichier unique
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Stocke la nouvelle image
        $path = $file->storeAs('covers/' . $this->id, $filename, 'public');
        
        if ($path) {
            $this->cover_image = $filename;
            $this->save();
            return true;
        }
        
        return false;
    }
}
