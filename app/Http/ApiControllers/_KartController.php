<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\kartController;
use App\Kart;
use App\ProductCategory;
use App\Products;
use App\sessionCart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class _KartController extends kartController {


    public function items() {

        $products = [];
        if($user = Auth::guard('api')->user()){
            $products = $user->kartProducts();
        } else {
            $ip = request()->ip();
            $idArray = sessionCart::productsId($ip);
            $products = Products::select(['id','name','category_id'])->whereIn('id',$idArray)->get();
        }

        foreach ($products as $product) {
            $product->imgUrl = ProductCategory::getDetailImgUrl($product->category_id);
        }

        return response($products);

    }

    public function store(Request $request) {

        if($user = Auth::guard('api')->user()){
            if(!$kart = Kart::where('product_id',$request->product_id)->where('user_id', $user->id)->first()) {
                $kart = new Kart;
                $kart->user_id = $user->id;
                $kart->product_id = $request->product_id;
                $kart->save();
            }
        } else {
            $ip = request()->ip();
            if ($sessionCart = sessionCart::where('ip_address',$ip)->first()) {
                $items = json_decode($sessionCart->item);
                $items[] = $request->product_id;
                $sessionCart->item=json_encode($items);
                $sessionCart->save();
            } else {
                $sessionCart = new sessionCart;
                $sessionCart->ip_address = $ip;
                $items = [$request->product_id];
                $sessionCart->item = json_encode($items);
                $sessionCart->save();
            }
        }
        return response(['msg' => 'success']);
    }

    public function destroy(Request $request,$id) {
        if($user = Auth::guard('api')->user()){
            if(!$kart = Kart::where('product_id',$id)->where('user_id', $user->id)->delete()) {
                return response('error',500);
            }


        } else {
            $ip = request()->ip();
            $sessionCart = sessionCart::where('ip_address',$ip)->first();
            $items = json_decode($sessionCart->item);
            $newItems=[];
            foreach ($items as $item) {
                if ($item != $id) {
                    $newItems[] = $item;
                }
            }
            $sessionCart->item = json_encode($newItems);
            $sessionCart->save();

        }
        return response(['msg' => 'success']);
    }


}