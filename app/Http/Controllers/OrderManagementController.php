<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\User;
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

        $page = isset($_GET['page']) ? $_GET['page'] : 1 ;
        $data_take = isset($_GET['data_take']) ? $_GET['data_take'] : 20 ;
        $data_from = $page * $data_take - $data_take;
        $rows_amount = Bill::all()->count();
        $page_amount = ceil($rows_amount / $data_take);


        $jsons = Bill::where(function($query){

            $user_name = isset($_GET['user_name']) ? $_GET['user_name'] : null;
            $ship_phone = isset($_GET['ship_phone']) ? $_GET['ship_phone'] : null;
            $shipment_0 = isset($_GET['shipment_0']) ? $_GET['shipment_0'] : null;
            $shipment_1 = isset($_GET['shipment_1']) ? $_GET['shipment_1'] : null;
            $shipment_2 = isset($_GET['shipment_2']) ? $_GET['shipment_2'] : null;
            $shipment_3 = isset($_GET['shipment_3']) ? $_GET['shipment_3'] : null;
            $pay_by_ATM = isset($_GET['pay_by_ATM']) ? $_GET['pay_by_ATM'] : null;
            $pay_by_cod = isset($_GET['pay_by_cod']) ? $_GET['pay_by_cod'] : null;
            $pay_by_credit = isset($_GET['pay_by_credit']) ? $_GET['pay_by_credit'] : null;
            $ship_county = isset($_GET['ship_county']) ? $_GET['ship_county'] : null;
            $bill_id = isset($_GET['bill_id']) ? $_GET['bill_id'] : null;
            $date1 = isset($_GET['date1']) ? $_GET['date1'] : null;
            $date2 = isset($_GET['date2']) ? $_GET['date2'] : null;

            if (isset($user_name) AND $user_name != '') {
                $query->orWhere('user_name',$user_name);
            }
            if (isset($ship_phone) AND $ship_phone != '') {
                $query->orWhere('ship_phone',$ship_phone);
            }
            if (isset($shipment_0)) {
                $query->orWhere([
                    ['shipment','=',$shipment_0],
                    ['pay_by','=','貨到付款'],
                ])->orWhere([
                    ['shipment','=',$shipment_0],
                    ['status','=','1'],
                ]);
            }
            if (isset($shipment_1)) {
                $query->orWhere('shipment',$shipment_1);
            }
            if (isset($shipment_2)) {
                $query->orWhere('shipment',$shipment_2);
            }
            if (isset($shipment_3)) {
                $query->orWhere('shipment',$shipment_3);
            }
            if (isset($pay_by_ATM)) {
                $query->orWhere('pay_by',$pay_by_ATM);
            }
            if (isset($pay_by_cod)) {
                $query->orWhere('pay_by',$pay_by_cod);
            }
            if (isset($pay_by_credit)) {
                $query->orWhere('pay_by',$pay_by_credit);
            }
            if (isset($ship_county) AND $ship_county != '') {
                $query->orWhere('ship_county',$ship_county);
            }
            if (isset($bill_id) AND $bill_id != '') {
                $query->orWhere('bill_id',$bill_id);
            }
            if (isset($date1) AND isset($date2) AND $date1 != '') {
                if ($date1 == $date2) {
                    $query->orWhere('created_at','LIKE','%'.$date1.'%');
                }else{
                    $query->orWhereBetween('created_at',[$date1,$date2]);
                }
            }

        })->orderBy('id','desc')->skip($data_from)->take($data_take)->get();

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
                'created_at' => str_replace(" ","<br>",$json->created_at),
                'bill_id' => $json->bill_id,
                'user_name' => $json->user_name,
                'item' => $itemArray,
                'price' => $json->price,
                'status' => $json->status,
                'pay_by' => $json->pay_by,
                'ship_name' =>$json->ship_name,
                'ship_gender' =>$json->ship_gender,
                'ship_memo' => $json->ship_memo,
                'ship_arrive' =>$json->ship_arrive,
                'ship_arriveDate' =>$json->ship_arriveDate,
                'ship_time' =>$json->ship_time,
                'ship_receipt' =>$json->ship_receipt,
                'ship_three_id' =>$json->ship_three_id,
                'ship_three_company' =>$json->ship_three_company,
                'shipment'=>$json->shipment,
            ];
            $j++;
        }

        return view('order.index',['orders'=>$orders,'page_amount'=>$page_amount]);
    }

   
    public function csv_download(Request $request)
    {

        // $jsons = Bill::where(function($query){
        //     $query->orWhere('pay_by','貨到付款')->orWhere([['status','=','1'],['pay_by','!=','貨到付款']]);  
        // })->whereIn('bill_id',$request->selectArray)->get();

        $jsons = Bill::whereIn('bill_id',$request->selectArray)->get();

        $j = 0;
        $orders = [];
        foreach($jsons as $json)
        {   
            $bills = json_decode($json->item,true);
            $itemArray = "";
            foreach($bills as $bill)       
            {
                $product = Products::where('slug', $bill['slug'])->firstOrFail();
                $itemArray = $itemArray.$product->short.'*'.$bill['quantity'].';';    
            }

            if ($json->ship_time == '14:00-18:00') {
                $ship_time = '2';
            }else{
                $ship_time = '1';
            }

            if ($json->ship_arriveDate == null) {
                $arrive = date('Y/m/d',strtotime('3 day'));
            }else{
                $arrive = str_replace('-', '/', $json->ship_arriveDate);
            }

            $orders[$j] = 
                $json->bill_id.",".
                $json->id.",".
                $json->ship_phone.",".
                $ship_time.",".
                $json->ship_name.",".
                $json->ship_phone.",".
                $itemArray.",".
                $json->ship_county.$json->ship_district.$json->ship_address.",".
                date('Y/m/d').",".
                $arrive;
                $j++;
        }
        $orders = json_encode($orders);
        return response()->json($orders);

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

    public function showAll($id)
    {

        $bill = Bill::where('bill_id',$id)->firstOrFail();
        $items = json_decode($bill->item,true);

        $i = 0;
        $itemArray = [];
        foreach($items as $item)
        {
            $product = Products::where('slug', $item['slug'])->firstOrFail();   
            $itemArray[$i] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $item['quantity'],
            ];
            $i++;
        }

        $user = User::find($bill->user_id);

        return view('order.showAll',['bill'=>$bill,'items'=>$itemArray,'user'=>$user]);
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
        if (isset($request->selectArray)) {

            $bills = Bill::where(function($query){
                $query->orWhere('pay_by','貨到付款')->orWhere([['status','=','1'],['pay_by','!=','貨到付款']]);  
            })->whereIn('bill_id',$request->selectArray)->get();


            foreach ($bills as $bill) {

                if ($bill->shipment == 0) {
                
                    $bill->shipment = 1;
                    $bill->save();


                }elseif ($bill->shipment == 1) {

                    $bill->shipment = 2;
                    $bill->save();


                }elseif ($bill->shipment == 2) {
                    
                    $bill->shipment = 0;
                    $bill->save();

                }

            }

            return response()->json('success');

        }else{
            $bill = Bill::where('bill_id','=',$id)->firstOrFail();
        
            if ($bill->shipment == 0) {
                
                $bill->shipment = 1;
                $bill->save();
                return response()->json(1);

            }elseif ($bill->shipment == 1) {

                $bill->shipment = 2;
                $bill->save();
                return response()->json(2);

            }elseif ($bill->shipment == 2) {
                
                $bill->shipment = 0;
                $bill->save();
                return response()->json(0);
            }    
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
