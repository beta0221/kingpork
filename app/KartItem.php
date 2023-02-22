<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KartItem extends Model
{
    protected $fillable = ['kart_id', 'item_id', 'quantity'];
    public $timestamps = false;

    public static function instance($itemId, $quantity) {
        $kartItem = new KartItem();
        $kartItem->item_id = intval($itemId);
        $kartItem->quantity = intval($quantity);
        return $kartItem;
    }
}
