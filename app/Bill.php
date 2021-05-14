<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{

    public function paymentLogs(){
        return $this->hasMany('App\PaymentLog');
    }
    
    public function products(){
        $items = json_decode($this->item,true);
        $slugArray = [];
        $quantityDict = [];
        foreach ($items as $item) {
            $quantityDict[$item['slug']] = $item['quantity'];
            $slugArray[] = $item['slug'];
        }
        $products = Products::whereIn('slug',$slugArray)->get();
        foreach ($products as $product) {
            if(!isset($quantityDict[$product->slug])){ continue; }
            $product->quantity = $quantityDict[$product->slug];
        }
        return $products;
    }

    public function sendBonusToBuyer(){
        if(empty($this->user_id)){ return; }
        $user = User::find($this->user_id);
        $user->updateBonus($this->get_bonus,false);
    }
    
}
