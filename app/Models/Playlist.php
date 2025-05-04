<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;
    
    /**
     * Attributs assignables en masse
     *
     * @var array
     */
    protected $fillable = ['name', 'user_id'];
    
    /**
     * Relation avec l'utilisateur propriétaire de la playlist
     * Une playlist appartient à un seul utilisateur
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relation many-to-many avec les chansons
     * Une playlist peut contenir plusieurs chansons
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function songs()
    {
        return $this->belongsToMany(Song::class);
    }
    
    /**
     * Ajoute une chanson à la playlist
     *
     * @param Song|int $song - Instance de Song ou ID de la chanson
     * @return void
     */
    public function addSong($song)
    {
        $songId = is_object($song) ? $song->id : $song;
        // Vérifie si la chanson n'est pas déjà dans la playlist
        if (!$this->songs()->where('song_id', $songId)->exists()) {
            $this->songs()->attach($songId);
        }
    }
    
    /**
     * Retire une chanson de la playlist
     *
     * @param Song|int $song - Instance de Song ou ID de la chanson
     * @return void
     */
    public function removeSong($song)
    {
        $songId = is_object($song) ? $song->id : $song;
        $this->songs()->detach($songId);
    }
}
