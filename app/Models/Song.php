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
        // Supprime l'ancien fichier s'il existe
        if ($this->hasAudioFile()) {
            // Supprimer du répertoire de stockage
            Storage::disk('public')->delete('audio/' . $this->id . '/' . $this->audio_file);
            
            // Supprimer également de public/storage si disponible
            $publicPath = public_path('storage/audio/' . $this->id . '/' . $this->audio_file);
            if (file_exists($publicPath)) {
                unlink($publicPath);
            }
        }
        
        // Génère un nom de fichier unique
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Stocke le fichier dans storage/app/public comme avant
        $path = $file->storeAs('audio/' . $this->id, $filename, 'public');
        
        // Assure-toi que le répertoire public existe
        $publicDir = public_path('storage/audio/' . $this->id);
        if (!file_exists($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        
        // Copie également le fichier dans public/storage
        $targetPath = $publicDir . '/' . $filename;
        copy(storage_path('app/public/audio/' . $this->id . '/' . $filename), $targetPath);
        
        // Mettre à jour la taille du fichier
        $this->filesize = filesize($targetPath);
        
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
