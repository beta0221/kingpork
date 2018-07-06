<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'productCategorys';

    public function products()
    {
    	return $this->hasMany('App\Products','category_id')->orderBy('price','asc');
    }
}
