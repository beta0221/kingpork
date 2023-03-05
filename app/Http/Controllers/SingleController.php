<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductCategory;
use App\Bill;
use App\BillItem;
use App\Products;
use App\Kart;
use App\User;
use Session;
use DB;
use Mail;

class SingleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('buynow/24');
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

    public function searchOrder(Request $request)
    {
        $billNum = $request->has('billNum') ? $request->billNum : null;
        $phone = $request->has('phone') ? $request->phone : null;
        $bills = [];
        
        if (!is_null($billNum)) {
            $bills = Bill::where('bill_id',$billNum)->where('user_id',null)->get();
        }elseif (!is_null($phone)) {
            $bills = Bill::where('ship_phone',$phone)->where('user_id',null)->get();
        }

        if (count($bills) == 0) {

            if(!is_null($billNum) || !is_null($phone)) {
                Session::flash('noResult','很抱歉，找不到這筆訂單');
            }

            return view('single.myorder');
        }

        return view('single.myorder',['bills'=>$bills]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'item.*'=>'required',
            'quantity.*'=>'required|integer|min:1',
            'ship_name'=>'required',
            'ship_phone'=>'required',
            'ship_address'=>'required',
            // 'ship_email'=>'required|E-mail',
            'ship_pay_by'=>'required',
        ]);

        $request->merge(['carrier_id' => Bill::CARRIER_ID_BLACK_CAT]);

        date_default_timezone_set('Asia/Taipei');

        $MerchantTradeNo = time() . rand(10,99);//先給訂單編號
        $total = 0;
        $products = [];

        foreach ($request->item as $index => $slug) {
            $quantity = $request->quantity[$index];
            $product = Products::where('slug', $slug)->firstOrFail();
            $product->quantity = (int)$quantity;
            $products[] = $product;

            // $getBonus += ($product->bonus * (int)$quantity);
            $total += ($product->price * (int)$quantity);
        }

        $bill = Bill::insert_row(null,$request->ship_name,$MerchantTradeNo,0,$total,0,$request);

        foreach ($products as $product) {
            BillItem::insert_row($bill->id,$product);
        }
        
        Session::flash('success','訂單已成功送出');
        
        return redirect()->route('thankYou', $MerchantTradeNo);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show() {   
        
        $id = 1;

        $productCategory = ProductCategory::find($id);

        return view('single.index',[
            'productCategory'=>$productCategory,
        ]);
    }

    /**
     * 菜單研究所
     */
    public function showMenustudy() {
        $productCategory = ProductCategory::findOrFail(17);

        return view('single.index',[
            'productCategory'=>$productCategory,
            'kol' => 'menustudy'
        ]);
    }


    public function showToBuyMenuStudy() {
        
        $productCategory = ProductCategory::findOrFail(17);

        return view('single.buy',[
            'productCategory'=>$productCategory
        ]);   

    }

    public function thankYou($id)
    {
        $bill = Bill::where('bill_id',$id)->firstOrFail();
        

        $billItems = $bill->billItems()->get();


        $finalBill = [
            'bill_id' => $bill->bill_id,
            'bonus_use'=>$bill->bonus_use,
            'price' => $bill->price,
            'billItems' => $billItems,
            'SPToken'=> $bill->SPToken,
            'pay_by'=>$bill->pay_by,
            'status'=>$bill->status,
        ];

        
        return view('single.thankYou', ['finalBill'=>$finalBill]); 
        // return view('single.thankYou');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
