<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    
    /**
     * Attributs assignables en masse
     *
     * @var array
     */
    protected $fillable = ['order_id','item_id','item_type','price'];

    /**
     * Relation avec la commande parent
     * Un élément de commande appartient à une seule commande
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Relation polymorphique avec l'item acheté (album ou chanson)
     * Cette relation permet de relier l'élément de commande à n'importe quel type d'item
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function item() 
    {
        return $this->morphTo();
    }
    
    /**
     * Scope pour filtrer par type d'item
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type Type d'item ('album' ou 'song')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('item_type', $type);
    }
}
