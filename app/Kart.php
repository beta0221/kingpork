<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kart extends Model
{
    public $table = "kart";

    public function user()
    {
    	return $this->belongsTo('App\User','user_id');
    }

    public static function getKartTotalPrice($user_id,$additionalProducts=[]){

        $result = DB::table('kart')->select('product_id')->where('user_id',$user_id)->get();
        $productArray = [];
        foreach ($result as $item) {
            if(!in_array($item->product_id,$additionalProducts)){
                $productArray[] = $item->product_id;
            }
        }

        $priceArray = DB::table('products')->select('price')->whereIn('id',$productArray)->get();
        $totalPrice = 0;
        foreach ($priceArray as $item) {
            $totalPrice += $item->price;
        }
        return $totalPrice;
    }


    public static function hasProduct($user_id,$productArray){
        $result = false;
        $productIdArray = DB::table('kart')->select('product_id')->where('user_id',$user_id)->get();
        $productInKartArray = [];
        foreach ($productIdArray as $item) {
            if(in_array($item->product_id,$productArray)){
                $result = true;
            }
        }
        return $result;
    }
}
