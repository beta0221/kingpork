<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\Products;
use Session;

class OrderManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jsons = Bill::all();
        $j = 0;
        $orders = [];
        foreach($jsons as $json)
        {   
            $bills = json_decode($json->item,true);
            $i = 0;
            $itemArray = [];
            foreach($bills as $bill)       
            {
                $product = Products::where('slug', $bill['slug'])->firstOrFail();
                $itemArray[$i] = [
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $bill['quantity'],
                ];
                $i++;
            }
            $orders[$j] = [
                'created_at' => $json->created_at,
                'bill_id' => $json->bill_id,
                'user_name' => $json->user_name,
                'item' => $itemArray,
                'price' => $json->price,
                'status' => $json->status,
                'pay_by' => $json->pay_by,
                'SPToken' => $json->SPToken,
                'ship_name' =>$json->ship_name,
                'ship_gender' =>$json->ship_gender,
                'ship_phone' =>$json->ship_phone,
                'ship_county' =>$json->ship_county,
                'ship_district' =>$json->ship_district,
                'ship_address' =>$json->ship_address,
                'ship_memo' => $json->ship_memo,
                'ship_arrive' =>$json->ship_arrive,
                'ship_arriveDate' =>$json->ship_arriveDate,
                'ship_time' =>$json->ship_time,
                'ship_receipt' =>$json->ship_receipt,
                // 'ship_three_name' =>$json->ship_three_name,
                'ship_three_id' =>$json->ship_three_id,
                'ship_three_company' =>$json->ship_three_company,
                'shipment'=>$json->shipment,
            ];
            $j++;
        }
        return view('order.index',['orders'=>$orders]);
    }

    public function search(Request $request)
    {
        
        if ($request->date1 == $request->date2) {
            $jsons = Bill::where('bill_id','LIKE','%'.$request->bill_id.'%')//如果只搜尋一個日期
                ->where('created_at','LIKE','%'.$request->date1.'%')
                ->where('pay_by','LIKE','%'.$request->pay_by_ATM.'%')
                ->where('pay_by','LIKE','%'.$request->pay_by_cod.'%')
                ->where('pay_by','LIKE','%'.$request->pay_by_credit.'%')
                ->where('ship_county','LIKE','%'.$request->ship_county.'%')
                ->where('shipment','LIKE','%'.$request->shipment_1.'%')
                ->where('shipment','LIKE','%'.$request->shipment_0.'%')
                ->where('status','LIKE','%'.$request->pay_1.'%')
                ->where('status','NOT LIKE',$request->pay_0)
                ->get();
        }elseif ($request->date1 == null OR $request->date2 == null) {      //如果不搜尋日期
            $jsons = Bill::where('bill_id','LIKE','%'.$request->bill_id.'%')
                ->where('pay_by','LIKE','%'.$request->pay_by_ATM.'%')
                ->where('pay_by','LIKE','%'.$request->pay_by_cod.'%')
                ->where('pay_by','LIKE','%'.$request->pay_by_credit.'%')
                ->where('ship_county','LIKE','%'.$request->ship_county.'%')
                ->where('shipment','LIKE','%'.$request->shipment_1.'%')
                ->where('shipment','LIKE','%'.$request->shipment_0.'%')
                ->where('status','LIKE','%'.$request->pay_1.'%')
                ->where('status','NOT LIKE',$request->pay_0)
                ->get();
        }else{
            $jsons = Bill::where('bill_id','LIKE','%'.$request->bill_id.'%')    //如果搜尋日期區間
                ->whereBetween('created_at',[$request->date1,$request->date2])
                ->where('pay_by','LIKE','%'.$request->pay_by_ATM.'%')
                ->where('pay_by','LIKE','%'.$request->pay_by_cod.'%')
                ->where('pay_by','LIKE','%'.$request->pay_by_credit.'%')
                ->where('ship_county','LIKE','%'.$request->ship_county.'%')
                ->where('shipment','LIKE','%'.$request->shipment_1.'%')
                ->where('shipment','LIKE','%'.$request->shipment_0.'%')
                ->where('status','LIKE','%'.$request->pay_1.'%')
                ->where('status','NOT LIKE',$request->pay_0)
                ->get();
        }

        $j = 0;
        $orders = [];
        foreach($jsons as $json)
        {   
            $bills = json_decode($json->item,true);
            $i = 0;
            $itemArray = [];
            foreach($bills as $bill)       
            {
                $product = Products::where('slug', $bill['slug'])->firstOrFail();
                $itemArray[$i] = [
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $bill['quantity'],
                ];
                $i++;
            }

            $orders[$j] = [
                'created_at' => $json->created_at,
                'bill_id' => $json->bill_id,
                'user_name' => $json->user_name,
                'item' => $itemArray,
                'price' => $json->price,
                'status' => $json->status,
                'pay_by' => $json->pay_by,
                'SPToken' => $json->SPToken,
                'ship_name' =>$json->ship_name,
                'ship_gender' =>$json->ship_gender,
                'ship_phone' =>$json->ship_phone,
                'ship_county' =>$json->ship_county,
                'ship_district' =>$json->ship_district,
                'ship_address' =>$json->ship_address,
                'ship_memo' => $json->ship_memo,
                'ship_arrive' =>$json->ship_arrive,
                'ship_arriveDate' =>$json->ship_arriveDate,
                'ship_time' =>$json->ship_time,
                'ship_receipt' =>$json->ship_receipt,
                // 'ship_three_name' =>$json->ship_three_name,
                'ship_three_id' =>$json->ship_three_id,
                'ship_three_company' =>$json->ship_three_company,
                'shipment'=>$json->shipment,
            ];
            $j++;
            
        }

        Session::flash('bill_id',$request->bill_id);
        Session::flash('pay_by_ATM',$request->pay_by_ATM);
        Session::flash('pay_by_cod',$request->pay_by_cod);
        Session::flash('pay_by_credit',$request->pay_by_credit);
        Session::flash('ship_county',$request->ship_county);
        Session::flash('date1',$request->date1);
        Session::flash('date2',$request->date2);
        Session::flash('shipment_1',$request->shipment_1);
        Session::flash('shipment_0',$request->shipment_0);
        Session::flash('pay_1',$request->pay_1);
        Session::flash('pay_0',$request->pay_0);
        return view('order.index',['orders'=>$orders]);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bill = Bill::where('bill_id','=',$id)->firstOrFail();
        return response()->json($bill->ship_memo);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
        $bill = Bill::where('bill_id','=',$id)->firstOrFail();
        if ($bill->shipment == '未出貨') {
            $bill->shipment = '已出貨';
            $bill->save();
            return response()->json('1');
        }elseif ($bill->shipment == '已出貨') {
            $bill->shipment = '未出貨';
            $bill->save();
            return response()->json('0');
        }
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
