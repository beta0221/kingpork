<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Kart;
use App\Products;
use App\sessionCart;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Session;

class kartController extends Controller
{
    public function inKart(Request $request){

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

    public function checkIfKart(Request $request ,$id)
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()){
            
            $kart = Kart::all()->where('user_id', Auth::user()->id);
            $shit = [];
            $i = 0;
            foreach ($kart as $k) {
                $shit[$i] = $k->product_id;
                $i++;
            }
                // $shit = DB::table('products')->whereIn('id', [7,8,9])->get();
                $products = DB::table('products')->whereIn('id', $shit)->get();
            return view('kart.index',['products'=>$products]);

        }
        else{
            // return redirect('login');

            // Session::flush();
            // return('hello');

            
            $ip = request()->ip();
            return($ip);


        }
        

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
        if (Auth::user()) {
            
        
            $kart = Kart::where('product_id',$request->product_id)
                ->where('user_id', Auth::user()->id)
                ->first();
            if($kart == null){
                $kart = new Kart;
                $kart->user_id = Auth::user()->id;
                $kart->product_id = $request->product_id;
                $kart->save();
            }

            return response()->json(['msg'=>'成功加入購物車']);
            
        }else{
            $ip_address = request()->ip();
            $sessionCart = sessionCart::where('ip_address',$ip_address)->first();
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
        if (Auth::user()) {
            
            $kart = Kart::where('user_id',Auth::user()->id)->where('product_id',$id)->delete();

            if($kart)
            {
                return response()->json(['msg'=>'成功刪除','status'=>1]);    
            }
            else
            {
                return response()->json(['msg'=>'錯誤','status'=>0]);
            }
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
