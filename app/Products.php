<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Products extends Model
{
    /**禮盒slug */
    const GIFT_SLUG = "30007";
    /**加價購類別id */
    const ADDITIONAL_CAT_ID = 12;
    /**加價購門檻 */
    const ADDITIONAL_THRESHOLD = 500;

    public $quantity = null;

    public function productCategory()
    {
    	return $this->belongsTo('App\ProductCategory','category_id');
    }

    public function inventory(){
        return $this->belongsToMany('App\Inventory','inventory_product','product_id','inventory_id')->withPivot('quantity');
    }

    public function sumInventoryAmount(int $quantity){
        $inventories = $this->inventory()->get();
        $sum = [];
        foreach ($inventories as $inventory) {
            $key = $inventory->slug;
            $value = $inventory->pivot->quantity * $quantity;
            if(!isset($sum[$key])){
                $sum[$key] = $value;
            }else{
                $sum[$key] += $value;
            }
        }
        return $sum;
    }

    public static function getAdditionalProducts(){
        $product_id_array = Products::where('category_id',Products::ADDITIONAL_CAT_ID)->pluck('id');
        if(count($product_id_array) == 0){ return []; }
        return (array)$product_id_array;
    }

    public static function getAdditionalProductSlug(){
        $product_slug_array = Products::where('category_id',Products::ADDITIONAL_CAT_ID)->pluck('slug');
        if(count($product_slug_array) == 0){ return []; }
        return (array)$product_slug_array;
    }

    public static function totalPrice($productIdArray,$additionalProducts=[]){
        $realProductIdArray = $productIdArray;
        if(!empty($additionalProducts)){
            $realProductIdArray = [];
            foreach ($productIdArray as $id) {
                if(!in_array($id,$additionalProducts)){
                    $realProductIdArray[] = $id;
                }
            }
        }
        
        $totalPrice = Products::whereIn('id',$realProductIdArray)->sum('price');
        return $totalPrice;
    }

    public static function totalPriceBySlug($productSlugArray,$additionalProductSlug=[]){
        $realProductSlugArray = $productSlugArray;
        if(!empty($additionalProductSlug)){
            $realProductSlugArray = [];
            foreach ($productSlugArray as $slug) {
                if(!in_array($slug,$additionalProductSlug)){
                    $realProductSlugArray[] = $slug;
                }
            }
        }
        
        $totalPrice = Products::whereIn('slug',$realProductSlugArray)->sum('price');
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
