<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Attributs assignables en masse
    // Ajout de is_admin pour permettre son assignation via create() ou update()
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', // Ajout du champ pour gérer les droits d'administration
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

   
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean', // Cast is_admin en booléen pour faciliter les comparaisons
    ];
    
    /**
     * Relation avec les commandes de l'utilisateur
     * Un utilisateur peut avoir plusieurs commandes
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Relation avec les playlists de l'utilisateur
     * Un utilisateur peut avoir plusieurs playlists
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }
}
