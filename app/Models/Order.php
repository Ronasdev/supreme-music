<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    /**
     * Attributs assignables en masse
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'total_price',
        'payment_method',
        'user_id',
        'transaction_id', // Ajout du champ pour stocker l'ID de transaction d'Orange Money
    ];

    /**
     * Relation avec les éléments de commande (order items)
     * Une commande peut contenir plusieurs éléments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Relation avec l'utilisateur qui a passé la commande
     * Une commande appartient à un seul utilisateur
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Récupère tous les albums de cette commande
     * Cette méthode filtre les orderItems pour ne récupérer que les albums
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function albums()
    {
        // Récupère les items de type 'album'
        return $this->orderItems()->where('item_type', 'album')->get()->map(function($item) {
            return Album::find($item->item_id);
        });
    }
    
    /**
     * Récupère toutes les chansons de cette commande
     * Cette méthode filtre les orderItems pour ne récupérer que les chansons
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function songs()
    {
        // Récupère les items de type 'song'
        return $this->orderItems()->where('item_type', 'song')->get()->map(function($item) {
            return Song::find($item->item_id);
        });
    }
}
