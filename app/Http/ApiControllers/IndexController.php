<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductCategory;
use App\Banner;
use App\sessionCart;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller {

    public function banners() {
        $banners = Banner::select(['id','image','link','alt'])->where('public',1)->orderBy('sort','desc')->get();

        foreach ($banners as $banner) {
            $host = config('app.url');
            $banner->imgUrl = "{$host}/images/banner/{$banner->image}";
        }

        return Response($banners);
    }

    public function categories()
    {
        $idArray = ProductCategory::$publicIdArray;
        $_cats = ProductCategory::select(['id','name','slug'])->whereIn('id',$idArray)->get();

        $indexDict = [];
        foreach ($_cats as $index => $cat) {
            $cat->imgUrl = "https://www.kingpork.com.tw/images/cat/landing/{$cat->id}.png";
            $cat->menuImgUrl = "https://www.kingpork.com.tw/images/cat/menu/{$cat->id}.png";
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