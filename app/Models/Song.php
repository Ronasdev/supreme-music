<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Song extends Model {
    use HasFactory;
    
    /**
     * Attributs assignables en masse
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'price', 'duration', 'album_id', 'audio_file', 'artist', 'genre', 'year'];

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
     * Vérifie si la chanson a un fichier audio associé
     *
     * @return bool
     */
    public function hasAudioFile()
    {
        return !empty($this->audio_file) && Storage::disk('public')->exists('audio/' . $this->id . '/' . $this->audio_file);
    }
    
    /**
     * Obtient l'URL du fichier audio
     *
     * @return string|null
     */
    public function getAudioUrl()
    {
        if ($this->hasAudioFile()) {
            return asset('storage/audio/' . $this->id . '/' . $this->audio_file);
        }
        
        return null;
    }
    
    /**
     * Enregistre un fichier audio pour la chanson
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     */
    public function setAudioFile($file)
    {
        // Supprime l'ancien fichier audio s'il existe
        if ($this->hasAudioFile()) {
            Storage::disk('public')->delete('audio/' . $this->id . '/' . $this->audio_file);
        }
        
        // Génère un nom de fichier unique
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Stocke le nouveau fichier audio
        $path = $file->storeAs('audio/' . $this->id, $filename, 'public');
        
        if ($path) {
            $this->audio_file = $filename;
            $this->save();
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtient le chemin physique du fichier audio
     *
     * @return string|null
     */
    public function getAudioPath()
    {
        if ($this->hasAudioFile()) {
            return storage_path('app/public/audio/' . $this->id . '/' . $this->audio_file);
        }
        
        return null;
    }
}
