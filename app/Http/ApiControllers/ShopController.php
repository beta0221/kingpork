<?php

namespace App\Http\ApiControllers;
use App\Http\Controllers\Controller;
use App\ProductCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function paths() {
        $idArray = ProductCategory::$publicIdArray;
        $_cats = ProductCategory::select(['id','slug'])->whereIn('id',$idArray)->get();
        $paths = [];
        foreach ($_cats as $_cat) {
            $paths[] = ['params' => [
                'slug' => $_cat->slug
            ]];
        }
        return Response($paths);
    }
}
