<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Products extends Model
{
    
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


    /**物流限制 */
    public function carrierRestriction(){
        $rows = DB::table('product_carrier')->where('product_id',$this->id)->get();
        $carrierRestriction = [];
        foreach ($rows as $row) {
            $carrierRestriction[] = $row->carrier_id;
        }
        return $carrierRestriction;
    }

    /**更新物流限制 */
    public function updateCarrierRestriction(array $carrier_id_array){
        DB::table('product_carrier')->where('product_id',$this->id)->delete();
        
        foreach ($carrier_id_array as $carrier_id) {
            DB::table('product_carrier')->insert(['product_id'=>$this->id,'carrier_id'=>$carrier_id]);
        }
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

    /**取得 加價購類別產品欄位(預設為Id欄位) */
    public static function getAdditionalProducts($column = null){
        return Products::where('category_id',Products::ADDITIONAL_CAT_ID)
            ->pluck((is_null($column) ? 'id' : $column))
            ->all();
    }

    /**取得 所有綁定附帶商品 列表 */
    public static function getAllBindedProducts($type = null) {
        $binds = DB::table('product_bind')->get();

        $result = [
            'relation' => [
                // '{id}' => [{id}, {id}] //綁定結構
            ],
            'reverseRelation' => [
                // '{id}' => [{id}, {id}] //反向綁定結構
            ]
        ];

        foreach ($binds as $bind) {
            $product = $bind->product_id;
            $bindProduct = $bind->bind_product_id;

            if(!isset($result['relation'][$product])) {
                $result['relation'][$product] = [];
            }

            if(!in_array($bindProduct, $result['relation'][$product])) {
                $result['relation'][$product][] = $bindProduct;
            }
            
            if(!isset($result['reverseRelation'][$bindProduct])) {
                $result['reverseRelation'][$bindProduct] = [];
            }

            if(!in_array($product, $result['reverseRelation'][$bindProduct])) {
                $result['reverseRelation'][$bindProduct][] = $product;
            }
        }

        if (!is_null($type) && isset($result[$type])) {
            return $result[$type];
        }

        return $result;
    }

    /** 取得 可加購的商品 */
    public static function getBindedProducts($product_id_array) {
        // 所有 綁定商品 關聯表
        $relation = static::getAllBindedProducts('relation');
        // 可綁定商品
        $bindedProduct_id_array = array_map(function($id) use ($relation) {
            return (isset($relation[$id])) ? $relation[$id] : null;
        }, $product_id_array);
        // 過濾 null
        $bindedProduct_id_array = array_filter($bindedProduct_id_array);
        // 扁平化多維陣列
        $bindedProduct_id_array = (count($bindedProduct_id_array) > 0) ? array_merge(...$bindedProduct_id_array) : $bindedProduct_id_array;
        // 濾掉 已經在product_id_array 的id
        $bindedProduct_id_array = array_filter($bindedProduct_id_array, function($bindedProduct_id) use ($product_id_array) {
            return !in_array($bindedProduct_id, $product_id_array);
        });
        
        return Products::whereIn('id', $bindedProduct_id_array)->get();
    }

    /**篩選 無主商品的綁定商品 */
    public static function getViolationProductIdArray($product_id_array) {
        $relation = static::getAllBindedProducts('reverseRelation');
        $violation_id_array = [];
        foreach ($product_id_array as $id) {
            if (!isset($relation[$id])) { continue; }
            $required_id_array = $relation[$id];
            $isViolation = true;
            foreach ($required_id_array as $required_id) {
                if (in_array($required_id, $product_id_array)) {
                    $isViolation = false;
                }
            }
            if ($isViolation) { $violation_id_array[] = $id; }
        }
        return $violation_id_array;
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
