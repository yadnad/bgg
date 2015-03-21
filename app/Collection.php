<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model {

    public function scopeOwned($query)
    {
        return $query->where('own', 1);
    }

    public function scopeToPlay($query)
    {
        return $query->where('want_to_play', 1);
    }

    public function scopeForTrade($query)
    {
        return $query->where('for_trade', 1);
    }

    public function scopeWishlist($query)
    {
        return $query->where('wishlist', 1);
    }

}
