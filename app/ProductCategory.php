<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    /**禮盒slug */
    const GIFT_SLUG = "GIFT";

    protected $table = 'productCategorys';

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
