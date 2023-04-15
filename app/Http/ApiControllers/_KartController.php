<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\kartController;
use App\ProductCategory;
use App\Products;
use App\sessionCart;

class _KartController extends kartController {


    public function items() {

        $ip = request()->ip();
        $idArray = sessionCart::productsId($ip);
        
        $products = Products::select(['id','name','category_id'])->whereIn('id',$idArray)->get();

        foreach ($products as $product) {
            $product->imgUrl = ProductCategory::getDetailImgUrl($product->category_id);
        }

        return response($products);

    }

}