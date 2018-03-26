<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    public function productCategory()
    {
    	return $this->belongsTo('App\ProductCategory','category_id');
    }

}
