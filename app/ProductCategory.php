<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    /**禮盒slug */
    const GIFT_SLUG = "GIFT";

    protected $table = 'productCategorys';

    /**顯示在架上的類別 */
    public static $publicIdArray = [1,3,2,9,13,14,15,16,20,30];
    /**購物趣圖片url */
    public static function getDetailImgUrl($catId) {
        return config('app.url') . "/images/cat/detail/{$catId}.png";
    }
    /**首頁圖片url */
    public static function getLandingImgUrl($catId) {
        return config('app.url') . "/images/cat/landing/{$catId}.png";
    }
    /**類別列表圖片url */
    public static function getMenuImgUrl($catId) {
        return config('app.url') . "/images/cat/menu/{$catId}.png";
    }

    public function products()
    {
    	return $this->hasMany('App\Products','category_id')->orderBy('price','asc');
    }

    public function productsById()
    {
    	return $this->hasMany('App\Products','category_id')->orderBy('id');	
    }

    public function getCatDic(){
        $cats = $this->all();
        $Dic=[];
        foreach ($cats as $cat) {
            $Dic[$cat->id] = $cat->name;
        }
        return $Dic;
    }

    public static function getGiftCategory() {
        return static::where('slug', static::GIFT_SLUG)->firstOrFail();
    }
}
