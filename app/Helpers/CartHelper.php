<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class CartHelper
{
    /**
     * Vérifie si un élément est déjà dans le panier
     *
     * @param int $id ID de l'élément
     * @param string $type Type de l'élément (album ou song)
     * @return bool
     */
    public static function isInCart($id, $type)
    {
        $cart = Session::get('cart', []);
        $key = $id . '_' . $type;
        
        return isset($cart[$key]);
    }
}
