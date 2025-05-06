<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
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
    
    /**
     * Attributs à caster
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean', // Cast is_admin en booléen pour faciliter les comparaisons
    ];
    
    /**
     * Vérifie si l'utilisateur a un avatar
     *
     * @return bool
     */
    public function hasAvatar()
    {
        return !empty($this->avatar_path) && Storage::disk('public')->exists(str_replace('/storage/', '', $this->avatar_path));
    }
    
    /**
     * Obtient l'URL de l'avatar de l'utilisateur
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        if ($this->hasAvatar()) {
            return asset($this->avatar_path);
        }
        
        // Avatar par défaut
        return asset('images/default-avatar.png');
    }
    
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
    
    /**
     * Vérifie si l'utilisateur a acheté un item spécifique (chanson ou album)
     * 
     * @param mixed $item Instance de Song ou Album
     * @return bool
     */
    public function hasPurchased($item)
    {
        // Récupérer le type de modèle (Song ou Album)
        $itemType = get_class($item);
        
        // Vérifier les commandes de l'utilisateur pour voir si l'item a été acheté
        return $this->orders()
            ->whereHas('orderItems', function($query) use ($item, $itemType) {
                $query->where([
                    'item_id' => $item->id,
                    'item_type' => $itemType
                ]);
            })
            ->where('status', 'completed') // Uniquement les commandes completées
            ->exists();
    }
    
    /**
     * Configure les collections de médias pour l'utilisateur
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile() // Un seul avatar à la fois
            ->useDisk('public') // Stockage dans le disque public pour accès facile
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->width(150)
                    ->height(150)
                    ->sharpen(10);
                
                $this->addMediaConversion('profile')
                    ->width(300)
                    ->height(300);
            });
    }
}
