<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Kart;
use App\Products;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Session;

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
            // return response()->json(['msg'=>'0']);

            $session = Session::get('item');
            $inKart = count($session);
            return response()->json(['msg'=>$inKart]);
        }
         
    }

    public function checkIfKart($id)
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

            if (in_array($id,Session::get('item'))) {
                $isAdd = true;
            }else{
                $isAdd = false;
            }

        }

        return response()->json($isAdd);

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

            $session = Session::get('item');

            return(Session::all());


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
            Session::push('item',$request->product_id);
            Session::save();
            $msg=json_encode(Session::get('item'));
            return response()->json(['msg'=>$msg]);
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
    public function destroy($id)
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

            $oldSession = Session::get('item');
            $key = array_Search($id,$oldSession);
            unset($oldSession[$key]);
            Session::put('item',$oldSession);
            Session::save();
            $msg =json_encode(Session::get('item'));
            return response()->json(['msg'=>$msg,'status'=>1]); 
        }

    }
}
