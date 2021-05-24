<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sessionCart extends Model
{
    public $table = "session_carts";


    /** ip 的購物車商品id */
    public static function productsId($ip){
        if(!$sessionCart = sessionCart::where('ip_address',$ip)->first()){
            return [];
        }
        return json_decode($sessionCart->item);
    }

    /** ip 的購物車商品 */
    public static function products($ip){
        $productsId = sessionCart::productsId($ip);
        $products = Products::whereIn('id', $productsId)->get();
        return $products;
    }



}
