<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Kart;
use App\Products;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Http\Response;


class kartController extends Controller
{
    public function inKart(){

        if(Auth::user()){
            $kart = Kart::all()->where('user_id', Auth::user()->id);
            $inKart = count($kart);
            // $inKart = 0;
            return response()->json(['msg'=>$inKart]);  
            // return response()->json(['msg'=>'0']);
        }else{
            return response()->json(['msg'=>'0']);
        }
         
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
        
            // $id = Auth::user()->id;
            // $userID = User::find($id);
            // return view('kart.index',['userID'=>$userID]);







        }
        else{
            return redirect('login');
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
        // return redirect()->route('products.show',$request->product_id);
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
    public function destroy($id)
    {

        $kart = DB::table('kart')->where('user_id',Auth::user()->id)->where('product_id',$id)->delete();

        if($kart)
        {
            return response()->json(['msg'=>'成功刪除','status'=>1]);    
        }
        else
        {
            return response()->json(['msg'=>'錯誤','status'=>0]);
        }
        // return redirect()->route('kart.index');
    }
}
