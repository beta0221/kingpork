<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductCategory;
use App\Banner;
use App\Contact;
use Validator;


class IndexController extends Controller
{

    public function banners()
    {
        $banners = Banner::select(['id', 'image', 'link', 'alt'])->where('public', 1)->orderBy('sort', 'desc')->get();

        foreach ($banners as $banner) {
            $host = config('app.url');
            $banner->imgUrl = "{$host}/images/banner/{$banner->image}";
        }

        return Response($banners);
    }

    public function categories()
    {
        $idArray = ProductCategory::$publicIdArray;
        $_cats = ProductCategory::select(['id', 'name', 'slug'])->whereIn('id', $idArray)->get();

        $indexDict = [];
        foreach ($_cats as $index => $cat) {
            $cat->imgUrl = ProductCategory::getLandingImgUrl($cat->id);
            $cat->menuImgUrl = ProductCategory::getMenuImgUrl($cat->id);
            $indexDict[$cat->id] = $index;
        }

        $cats = [];
        foreach ($idArray as $id) {
            if (!isset($indexDict[$id])) {
                continue;
            }
            $cats[] = $_cats[$indexDict[$id]];
        }

        return Response($cats);
    }

    public function contact(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|E-mail',
            'title' => 'required',
            'text' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //Ｃaptcha Validation

        // sleep(5);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'title' => $request->title,
            'message' => $request->text
        ]);

        return response([
            'success' => true,
            'message' => '訊息已成功送出，我們將會儘速回覆您。'
        ]);
    }
}
