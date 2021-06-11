<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\BillItem;
use App\Helpers\ECPay;
use App\User;
use App\Products;
use Excel;
use Illuminate\Support\Facades\DB;
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
    public function index(Request $request)
    {

        $page = ($request->has('page')) ? $request->page : 1 ;
        $data_take = ($request->has('data_take')) ? $request->data_take : 20 ;
        $data_from = $page * $data_take - $data_take;

        $query = Bill::orderBy('id','desc');
        
        if($request->has('user_name')){
            $query->where('user_name',$request->user_name);
        }
        if($request->has('ship_phone')){
            $query->where('ship_phone',$request->ship_phone);
        }
        if($request->has('shipment_0')){
            if ($request->has('pay_by_ATM') OR $request->has('pay_by_credit')) {
                $query->where('status','1')->where('shipment',$request->shipment_0);
            }else{
                $query->where(function($query) use($request){
                    $query->where([['pay_by','貨到付款'],['shipment',$request->shipment_0]])->orWhere([['status','1'],['shipment',$request->shipment_0]]);
                });
            }
        }
        if($request->has('shipment_1')){
            $query->where('shipment',$request->shipment_1);
        }
        if($request->has('shipment_2')){
            $query->where('shipment',$request->shipment_2);
        }
        if($request->has('shipment_3')){
            $query->where('shipment',$request->shipment_3);
        }
        
        $pay_by = [];
        if($request->has('pay_by_ATM')){
            $pay_by[] = $request->pay_by_ATM;
        }
        if($request->has('pay_by_cod')){
            $pay_by[] = $request->pay_by_cod;
        }
        if($request->has('pay_by_credit')){
            $pay_by[] = $request->pay_by_credit;
        }
        if(!empty($pay_by)){
            $query->whereIn('pay_by',$pay_by);
        }

        if($request->has('ship_county')){
            $query->where('ship_county',$request->ship_county);
        }
        if($request->has('bill_id')){
            $query->where('bill_id',$request->bill_id);
        }
        if($request->has('date1') && $request->has('date2')){
            if($request->date1 == $request->date2){
                $query->whereDate('created_at',date('Y-m-d',strtotime($request->date1)));
            }else{
                $date2 = date('Y-m-d',strtotime($request->date2."+1 day"));
                $query->whereBetween('created_at',[$request->date1,$date2]);
            }
        }
        $total = $query->count();
        $bills = $query->skip($data_from)->take($data_take)->get();
        $page_amount = ceil($total / $data_take);
        
        return view('order.index',['orders'=>$bills,'page_amount'=>$page_amount]);

    }

    private function getRow(Bill $bill,$now,$quantity = null){

        $row = "";
        $items = $bill->products();
        $itemsInShort = "";
        foreach($items as $item)       
        {
            $_quantity = $item->quantity;
            if(!is_null($quantity)){
                $_quantity = $quantity;
            }
            $itemsInShort .= ($item->short . '*' . $_quantity . ';');
        }

        $ship_time = '1';
        if ($bill->ship_time == '14:00-18:00') {
            $ship_time = '2';
        }

        $arrive = str_replace('-', '/', $bill->ship_arriveDate);
        if ($bill->ship_arriveDate == null) {
            $arrive = date('Y/m/d',strtotime('3 day'));
        }

        $cash = $bill->price;
        if ($bill->pay_by != '貨到付款') {
            $cash = null;
        }

        $bill->ship_county = str_replace(',','',$bill->ship_county);
        $bill->ship_district = str_replace(',','',$bill->ship_district);
        $bill->ship_address = str_replace(',','',$bill->ship_address);

        $row = 
            $bill->created_at.",".
            $bill->bill_id.",".
            '官網'.$bill->pay_by.",".
            $cash.",".
            $ship_time.",".
            $bill->ship_name.",".
            $bill->ship_phone.",".
            $itemsInShort.",".
            $bill->ship_county.$bill->ship_district.$bill->ship_address.",".
            $now.",".
            $arrive.",".
            $bill->price.",".
            $bill->ship_memo;

        return $row;

    }

    public function csv_download(Request $request)
    {

        $bills = Bill::whereIn('bill_id',$request->selectArray)->orderBy('id','asc')->get();
        
        $now = date("Y-m-d");
        $orders = [];
        foreach($bills as $bill)
        {   
            //代客送禮
            if ($bill->ship_name == '*' && $bill->ship_phone == '*') {
                $gifts = json_decode($bill->ship_address,true);
                foreach ($gifts as $gift) {
                    $_bill = $bill;
                    $_bill->ship_time = $gift['time'];
                    $_bill->ship_name = $gift['name'];
                    $_bill->ship_phone = $gift['phone'];
                    $_bill->ship_address = $gift['address'];
                    $orders[] = $this->getRow($_bill,$now,(int)$gift['quantity']);
                }
                continue;
            }

            //一般訂單
            $orders[] = $this->getRow($bill,$now);

        }
        $orders = json_encode($orders);
        return response()->json($orders);

    }


    public function ExportExcelForAccountant(Request $request){

        date_default_timezone_set('Asia/Taipei');
        $cellData = [
            ['訂單編號','訂單日期','交易日期','客戶','購買人','商品貨號','','商品名稱','數量','單位','單價','抵扣紅利','含稅金額','','含稅金額','收件人','郵遞區號','送貨地址','聯絡電話','行動電話','代收宅配單號','代收貨款','付款方式','','','','發票號碼','發票收件人','發票種類','發票統編','買受人名稱','','','','','','信用卡後4碼','','部門','備註'],
        ];
        $bill_id_array = json_decode($request->bill_id);
        $now = date("Y-m-d");
        if($bills = Bill::whereIn('bill_id',$bill_id_array)->orderBy('id','asc')->get()){

            foreach ($bills as  $bill) {
                //代客送禮
                if($bill->ship_name == '*' && $bill->ship_phone == '*'){
                    if(!$product = Products::where('slug',Products::GIFT_SLUG)->first()){ continue; }

                    $gifts = json_decode($bill->ship_address,true);
                    foreach ($gifts as $index => $gift) {
                        $receiver = $gift['name'];
                        $phone = $gift['phone'];
                        $address = $gift['address'];
                        $quantity = (int)$gift['quantity'];
                        
                        $cellData[] = $this->getAccountantRow($bill,$product,$index,$quantity,$now,$receiver,$address,$phone);
                    }
                    continue;
                }

                //一般訂單
                $items = $bill->products();
                foreach ($items as $index => $item) {
                    $receiver = $bill->ship_name;
                    $address = $bill->ship_county . $bill->ship_district . $bill->ship_address;
                    $phone = $bill->ship_phone;

                    if($item instanceof BillItem){
                        if(!$product = Products::find($item->product_id)){ continue; }
                        $item->erp_id = $product->erp_id;
                    }

                    $cellData[] = $this->getAccountantRow($bill,$item,$index,$item->quantity,$now,$receiver,$address,$phone);
                }

            }

        }

        Excel::create('會計訂單輸出-' . $now, function($excel)use($cellData) {
            $excel->sheet('Sheet1', function($sheet)use($cellData) {
                $sheet->rows($cellData);
            });
        })->download('xls');

    }

    private function getAccountantRow($bill,$product,$index,$quantity,$now,$receiver,$address,$phone){
        $bill_id = $bill->bill_id;
        $billDate = $bill->created_at;
        $buyer = $bill->user_name;
        $erp_id = $product->erp_id;
        $productName = $product->name;
        $price = $product->price;
        if($index == 0){
            $bonus = $bill->bonus_use;
            $totalPrice = $bill->price;
        }else{
            $bonus = null;
            $totalPrice = null;
        }
        $payType = '官網' . $bill->pay_by;
        if($bill->pay_by == '貨到付款' && $index == 0){
            $onDeliveryPrice = $bill->price;
        }else{
            $onDeliveryPrice = 0;
        }
        $invoiceType = $bill->ship_receipt;
        $invoice_id = $bill->ship_three_id;
        $invoice_company = $bill->ship_three_company;

        $newRow = [$bill_id,$billDate,$now,null,$buyer,$erp_id,null,$productName,$quantity,'組',$price,$bonus,$totalPrice,null,$totalPrice,$receiver,null,$address,$phone,$phone,null,$onDeliveryPrice,$payType,null,null,null,null,$receiver,$invoiceType,$invoice_id,$invoice_company,null,null,null,null,null,null,null,'官網',$bill->ship_memo];
        return $newRow;
    }


    public function MonthlyReport($date){
        
        $cellData = [
            ['訂單日期','訂單筆數','單日業績','平均客單價'],
        ];

        $bills = DB::select("SELECT * FROM bills WHERE MONTH(created_at) = MONTH('".$date."') AND YEAR(created_at) = YEAR('".$date."') AND shipment = 2");

        $billsDic = [];

        foreach ($bills as $bill) {
            $date = substr($bill->created_at,0,10);

            if(isset($billsDic[$date]['amount'])){
                $billsDic[$date]['amount'] += 1; 
            }else{
                $billsDic[$date]['amount'] = 1;
            }

            if(isset($billsDic[$date]['total'])){
                $billsDic[$date]['total'] += (int)$bill->price;
            }else{
                $billsDic[$date]['total'] = (int)$bill->price;
            }
        }

        foreach ($billsDic as $date => $bill) {
            $avg = intval((int)$bill['total'] / (int)$bill['amount']);
            $newRow = [$date,$bill['amount'],$bill['total'],$avg];
            array_push($cellData,$newRow);
        }

        $time = strtotime($date);
        $year_month = date("Y-m",$time);
        Excel::create('月報表-' . $year_month, function($excel)use($cellData) {
            $excel->sheet('Sheet1', function($sheet)use($cellData) {
                $sheet->rows($cellData);
            });
        })->download('xls');
    }









    public function DailyReport($date){

        $cellData = [
            ['訂單日期','訂購人','訂購品項','數量','金額','前次訂購日期','入會日期'],
        ];

        $bills = DB::select("SELECT * FROM bills WHERE MONTH(created_at) = MONTH('".$date."') AND YEAR(created_at) = YEAR('".$date."') AND DAY(created_at) = DAY('".$date."') AND shipment = 2");
        
        foreach ($bills as $bill) {
            if($user = User::find($bill->user_id)){
                $prevOrderDate = null;
                if($result = Bill::where('user_id',$bill->user_id)->orderBy('id','desc')->where('created_at','<',$bill->created_at)->skip(1)->first()){
                    $prevOrderDate = $result->created_at;
                }
                $items = json_decode($bill->item,true);
                foreach ($items as $index => $item) {
                    if($product = Products::where('slug',$item['slug'])->first()){
                        $newRow = [$bill->created_at,$user->name,$product->name,$item['quantity'],$product->price,$prevOrderDate,$user->created_at];
                        array_push($cellData,$newRow);
                    }
                }
            }
        }

        Excel::create('日報表-' . $date, function($excel)use($cellData) {
            $excel->sheet('Sheet1', function($sheet)use($cellData) {
                $sheet->rows($cellData);
            });
        })->download('xls');
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
        return response()->json(['memo'=>$bill->ship_memo,'mark'=>$bill->allReturn]);
    }

    public function showAll($id)
    {

        $bill = Bill::where('bill_id',$id)->firstOrFail();
        $items = $bill->products();
        $cardInfo = $bill->getPaymentInfo(ECPay::PAYMENT_INFO_CARD);

        $user = null;
        if ($bill->user_id != null) {
            $user = User::find($bill->user_id);
        }

        $storeInfo = null;
        if($bill->carrier_id == Bill::CARRIER_ID_FAMILY_MART){
            $storeInfo = $bill->familyStore;
        }
        
        return view('order.showAll',[
            'carrierDict' => Bill::getAllCarriers(),
            'bill'=>$bill,
            'items'=>$items,
            'user'=>$user,
            'cardInfo' => $cardInfo,
            'storeInfo' => $storeInfo
        ]);
        
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

                    if ($bill->pay_by == '貨到付款' AND $bill->user_id !=null) {//如果是貨到付款->累計紅利
                        $user = User::find($bill->user_id);
                        $bonus = (int)$bill->get_bonus;
                        $user->bonus = $user->bonus + $bonus;
                        $user->save();
                    }


                }elseif ($bill->shipment == 2) {
                    
                    $bill->shipment = 0;
                    $bill->save();

                    if ($bill->pay_by == '貨到付款' AND $bill->user_id !=null) {//如果是貨到付款->扣除紅利
                        $user = User::find($bill->user_id);
                        $bonus = (int)$bill->get_bonus;
                        $user->bonus = $user->bonus - $bonus;
                        $user->save();
                    }

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

                if ($bill->pay_by == '貨到付款' AND $bill->user_id !=null) {//如果是貨到付款->累計紅利
                    $user = User::find($bill->user_id);
                    $bonus = (int)$bill->get_bonus;
                    $user->bonus = $user->bonus + $bonus;
                    $user->save();
                }

                return response()->json(2);

            }elseif ($bill->shipment == 2) {
                
                $bill->shipment = 0;
                $bill->save();

                if ($bill->pay_by == '貨到付款' AND $bill->user_id !=null) {//如果是貨到付款->扣除紅利
                    $user = User::find($bill->user_id);
                    $bonus = (int)$bill->get_bonus;
                    $user->bonus = $user->bonus - $bonus;
                    $user->save();
                }

                return response()->json(0);
            }    
        }
        
    }



    public function marking(Request $request,$id)
    {
        $bill = Bill::where('bill_id',$id)->firstOrFail();
        $mark = $request->mark;
        $bill->allReturn = $request->mark;
        $bill->save();

        if ($bill->allReturn == null) {
            return response()->json(0);
        }else{
            return response()->json(1);
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

    public function cancelBill($id)
    {
        $bill = Bill::where('bill_id',$id)->firstOrFail();
        $user = User::findOrFail($bill->user_id);

        if ($bill->status == 1 || $bill->shipment != 0){
            return response()->json('error');   
        }

        $amount = $bill->bonus_use * 50;
        $user->updateBonus($amount,false);

        $bill->updateShipment(Bill::SHIPMENT_VOID);
        return response()->json('success');

    }

    public function orderHistory($user_id){
        $user = User::findOrFail($user_id);
        $bills = Bill::where('user_id',$user_id)->orderBy('id','desc')->get();

        $bonus = 5000;
        foreach ($bills as $bill) {

            if($bill->shipment != 3){
                $bonus -= $bill->bonus_use * 50;
            }

            if($bill->status == 1){
                $bonus += $bill->get_bonus;
            }else if($bill->pay_by == '貨到付款' && $bill->shipment == 2){
                $bonus += $bill->get_bonus;
            }
        }

        return view('order.history',[
            'user' => $user,
            'bills' => $bills,
            'bonus' => $bonus
        ]);
    }

    public function regulateUserBonus(Request $request,$user_id){
        $user = User::findOrFail($user_id);
        if($request->has('bonus')){
            
            $correct_bonus = (int)$request->bonus;
            
            if ($user->bonus > $correct_bonus){
                
                $user->bonus = $correct_bonus;
                
                if($correct_bonus < 0){
                    $user->bonus = 0;
                }

                $user->save();

            }
            
        }

        return redirect('order/history/' . $user_id);
        
    }

}
