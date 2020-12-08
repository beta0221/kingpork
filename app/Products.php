<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Products extends Model
{

    protected static $additionalCatId = 12;

    public function productCategory()
    {
    	return $this->belongsTo('App\ProductCategory','category_id');
    }

    public static function getAdditionalProducts(){
        $idArray=[];
        $result = DB::table('products')->select('id')->where('category_id',Products::$additionalCatId)->get();
        foreach ($result as $id) {
            $idArray[] = $id->id;
        }
        return $idArray;
    }

    public static function getAdditionalProductSlug(){
        $slugArray=[];
        $result = DB::table('products')->select('slug')->where('category_id',Products::$additionalCatId)->get();
        foreach ($result as $item) {
            $slugArray[] = $item->slug;
        }
        return $slugArray;
    }

    public static function totalPrice($productIdArray,$additionalProducts=[]){
        if(count($additionalProducts) <= 0){
            $realProductIdArray = $productIdArray;
        }else{
            $realProductIdArray = [];
            foreach ($productIdArray as $id) {
                if(!in_array($id,$additionalProducts)){
                    $realProductIdArray[] = $id;
                }
            }
        }
        
        $priceArray = DB::table('products')->select('price')->whereIn('id',$realProductIdArray)->get();
        $totalPrice = 0;
        foreach ($priceArray as $item) {
            $totalPrice += $item->price;
        }
        return $totalPrice;
    }

    public static function totalPriceBySlug($productSlugArray,$additionalProductSlug=[]){
        if(count($additionalProductSlug) <= 0){
            $realProductSlugArray = $productSlugArray;
        }else{
            $realProductSlugArray = [];
            foreach ($productSlugArray as $slug) {
                if(!in_array($slug,$additionalProductSlug)){
                    $realProductSlugArray[] = $slug;
                }
            }
        }
        
        $priceArray = DB::table('products')->select('price')->whereIn('slug',$realProductSlugArray)->get();
        $totalPrice = 0;
        foreach ($priceArray as $item) {
            $totalPrice += $item->price;
        }
        return $totalPrice;
    }

    public static function hasCategory($productSlugArray,$cat_id){
        $productList = Products::whereIn('slug',$productSlugArray)->get();
        $result = false;
        foreach ($productList as $product) {
            if($product->category_id == $cat_id){
                $result = true;
            }
        }
        return $result;
    }


}
