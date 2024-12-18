<?php

namespace App\Http\Controllers;

use App\Bill;
use Illuminate\Http\Request;
use App\User;
use App\Kart;
use App\Products;
use App\sessionCart;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Session;

class kartController extends Controller
{
    public function inKart(Request $request){ //計算數量

        if(Auth::user()){
            $kart = Kart::all()->where('user_id', Auth::user()->id);
            $inKart = count($kart);
            // $inKart = 0;
            return response()->json(['msg'=>$inKart]);
            // return response()->json(['msg'=>'0']);
        }else{
            $ip_address = request()->ip();
            $sessionCart = sessionCart::where('ip_address',$ip_address)->first();
            if ($sessionCart) {
                
                $inKart=count(json_decode($sessionCart->item));

                return response()->json(['msg'=>$inKart]);

            }else{
                return response()->json(['msg'=>0]);
            }
            
        }
         
    }

    public function checkIfKart(Request $request ,$id) //判斷是否已經加入
    {
        if (Auth::user()) {
        
            $kart = Kart::where('product_id',$id)
                ->where('user_id', Auth::user()->id)
                ->first();
                //判斷是否已加入購物車
            if($kart == null)
                {
                    $isAdd = false;
                }
            else
                {
                    $isAdd = true;
                }

        }else{
            $ip_address = request()->ip();
            $sessionCart = sessionCart::where('ip_address',$ip_address)->first();
            if ($sessionCart) {
                $items=json_decode($sessionCart->item);
                if(in_array($id,$items)){
                    $isAdd =true;
                }else{
                    $isAdd =false;
                }
                
            }else{
                $isAdd=false;
            }

        }

        return response()->json(['msg'=>$isAdd]);

    }

    /** 購物車內容 */
    public function getProducts()
    {
        if ($user = Auth::user()) {
            return response()->json($user->kartProducts());
        }

        $ip = request()->ip();
        $products = sessionCart::products($ip);
        return response()->json($products);
    }

    public function ajaxShowIndex() //ajax 呼叫 modal 顯示購物車中的內容
    {
        return $this->getProducts();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!$user = Auth::user()){
            return view('auth.reg-buy');
        }
        
        $product_id_array = Kart::where('user_id', $user->id)->orderBy('product_id')->pluck('product_id')->all();
        $additionalProducts = Products::getAdditionalProducts();

        $_product_id_array = [];
        foreach ($product_id_array as $product_id) {
            if(!in_array($product_id,$additionalProducts)){
                $_product_id_array[] = $product_id;
            }
        }

        $totalPrice = Products::totalPrice($_product_id_array);
        if($totalPrice < Products::ADDITIONAL_THRESHOLD){
            Kart::where('user_id',$user->id)->whereIn('product_id',$additionalProducts)->delete();
            $product_id_array = $_product_id_array;
        }
        

        // 刪除 無主商品的綁定商品
        $violation_id_array = Products::getViolationProductIdArray($product_id_array);
        foreach ($violation_id_array as $violation_id) {
            if (($key = array_search($violation_id, $product_id_array)) !== false) {
                unset($product_id_array[$key]);
            }
        }
        if (count($violation_id_array) > 0) {
            Kart::where('user_id',$user->id)->whereIn('product_id',$violation_id_array)->delete();
        }

        // 目前購物車商品
        $products = Products::whereIn('id', $product_id_array)->get();
        // 可加購商品
        $bindedProducts = Products::getBindedProducts($product_id_array);

        return view('kart.index',[
            'products' => $products,
            'bindedProducts' => $bindedProducts,
            'relation' => Products::getAllBindedProducts('relation'),
            'addresses' => $user->addresses()->orderBy('isDefault', 'desc')->get()
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // $additionalProducts = Products::getAdditionalProducts();
        $product = Products::findOrFail($request->product_id);

        if ($user = Auth::user()) {

            // if(in_array($request->product_id,$additionalProducts)){
            //     $totalPrice =Kart::getKartTotalPrice(Auth::user()->id,$additionalProducts);
            //     if($totalPrice < 500){
            //         return response('403');
            //     }
            // }

            //親友專區 電話訂購
            if (in_array($product->productCategory->id, [25, 26])) {
                $whiteList = [
                    'may@sacred.com.tw',
                    'grace-l@sacred.com.tw',
                    'julie9066@gmail.com'
                ];
                if (!in_array($user->email, $whiteList)) {
                    return response('無法加入', 403);
                }
            }
            
            $kart = Kart::where('product_id',$request->product_id)
                ->where('user_id', $user->id)
                ->first();

            if($kart == null){
                Kart::create([
                    'user_id' => $user->id,
                    'product_id' => $request->product_id
                ]);
            }

            return response()->json(['msg'=>'成功加入購物車']);
            
        }else{

            //親友專區 電話訂購
            if (in_array($product->productCategory->id, [25, 26])) {
                return response('無法加入', 403);
            }

            $ip_address = request()->ip();
            $sessionCart = sessionCart::where('ip_address',$ip_address)->first();

            // if(in_array($request->product_id,$additionalProducts)){
            //     if($sessionCart){
            //         $productIdArray = json_decode($sessionCart->item);
            //         $totalPrice = Products::totalPrice($productIdArray,$additionalProducts);
            //         if($totalPrice < 500){
            //             return response('403');
            //         }
            //     }else{
            //         return response('403');
            //     }
            // }


            if ($sessionCart) {
                $items = json_decode($sessionCart->item);
                array_push($items,$request->product_id);
                $sessionCart->item=json_encode($items);
                $sessionCart->save();
            }else{
                $sessionCart = new sessionCart;
                $sessionCart->ip_address = $ip_address;
                $items = [];
                array_push($items,$request->product_id);

                // $sessionCart->item = implode(",",$items);
                $sessionCart->item = json_encode($items);
                $sessionCart->save();
            }
            
            return response()->json('success');
    
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return('helloworld');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        // $additionalProducts = Products::getAdditionalProducts();

        if (Auth::user()) {
            
            $kart = Kart::where('user_id',Auth::user()->id)->where('product_id',$id)->delete();

            if(!$kart){
                return response()->json(['msg'=>'錯誤','status'=>0]);
            }

            // $totalPrice = Kart::getKartTotalPrice(Auth::user()->id,$additionalProducts);

            // if($totalPrice < 500 && Kart::hasProduct(Auth::user()->id,$additionalProducts)){

            //     Kart::where('user_id',Auth::user()->id)->whereIn('product_id',$additionalProducts)->delete();
            //     return response()->json(['msg'=>'403','status'=>1]);

            // }

            return response()->json(['msg'=>'成功刪除','status'=>1]);



        }else{
            $ip_address = request()->ip();
            $sessionCart = sessionCart::where('ip_address',$ip_address)->firstOrFail();

            $items=json_decode($sessionCart->item);
            $newItems=[];
            foreach ($items as $item) {
                if ($item != $id) {
                    array_push($newItems,$item);
                }
            }
            

            $sessionCart->item = json_encode($newItems);
            $sessionCart->save();
            
            
            return response()->json(['msg'=>'success','status'=>1]); 
        }

    }
}
