<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\Products;

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
                'ship_three_name' =>$json->ship_three_name,
                'ship_three_id' =>$json->ship_three_id,
                'ship_three_company' =>$json->ship_three_company,
            ];
            $j++;
            
        }

        return view('order.index',['orders'=>$orders]);
    }

    public function search(Request $request)
    {
        $jsons = Bill::all();
        if ($request->bill_id != null) {
            $jsons = Bill::where('bill_id','=',$request->bill_id)->get();
        }
        
        if ($request->date1 != null AND $request->date1 == $request->date2) {
            $date = '%'.$request->date1.'%';
            $jsons = Bill::where('created_at','LIKE',$date)->get();
        }else if($request->date1 != null AND $request->date2 != null){
            $jsons = Bill::whereBetween('created_at',[$request->date1,$request->date2])->get();
        }

        if ($request->pay_by_ATM != null) {
            $jsons = Bill::where('pay_by','=','ATM')->get();
        }

        if ($request->pay_by_cod != null) {
            $jsons = Bill::where('pay_by','=','貨到付款')->get();
        }

        if ($request->ship_county != null) {
            $jsons = Bill::where('ship_county','=',$request->ship_county)->get();
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
                'ship_three_name' =>$json->ship_three_name,
                'ship_three_id' =>$json->ship_three_id,
                'ship_three_company' =>$json->ship_three_company,
            ];
            $j++;
            
        }

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
