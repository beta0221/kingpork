<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductCategory;
use App\sessionCart;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller {


    public function index()
    {
        $idArray = [1,3,2,9,13,14,15,16,20,30];
        $_cats = ProductCategory::select(['id','name'])->whereIn('id',$idArray)->get();

        $indexDict = [];
        foreach ($_cats as $index => $cat) {
            $cat->imgUrl = "https://www.kingpork.com.tw/images/cat/landing/{$cat->id}.png";
            $indexDict[$cat->id] = $index;
        }

        $cats = [];
        foreach ($idArray as $id) {
            if (!isset($indexDict[$id])) { continue; }
            $cats[] = $_cats[$indexDict[$id]];
        }

        return Response($cats);
    }

}