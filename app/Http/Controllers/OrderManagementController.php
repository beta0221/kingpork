<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\BillItem;
use App\Helpers\ECPay;
use App\Helpers\ExcelHelper;
use App\Inventory;
use App\User;
use App\Products;
use Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        if($request->has('pay_by_family')){
            $pay_by[] = $request->pay_by_family;
        }
        if(!empty($pay_by)){
            $query->whereIn('pay_by',$pay_by);
        }

        if($request->has('carrier_id')){
            $query->where('carrier_id',$request->carrier_id);
        }

        if($request->has('kol')){
            $query->where('kol',$request->kol);
        } else {
            $query->whereNull('kol');
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

        return view('order.index',['orders'=>$bills,'page_amount'=>$page_amount,'carriers'=>Bill::getAllCarriers()]);

    }

    private function materialListText(array $inventoryAmountArray){
        $materialList = [];
        foreach ($inventoryAmountArray as $inventoryAmount) {
            foreach ($inventoryAmount as $key => $value) {
                if(!isset($materialList[$key])){
                    $materialList[$key] = $value;
                }else{
                    $materialList[$key] += $value;
                }
                if(!isset($this->totalMaterialList[$key])){
                    $this->totalMaterialList[$key] = $value;
                }else{
                    $this->totalMaterialList[$key] += $value;
                }
            }
        }
        $text = '';
        foreach ($materialList as $key => $value) {
            $text .= '(' . $key . '*' . $value . ')';
        }
        return $text;
    }

    /**黑貓 Row */
    private function getRow(Bill $bill,$now,$quantity = null){

        $row = "";
        $items = $bill->products();
        $itemsInShort = "";
        $inventoryAmountArray = [];
        foreach($items as $item)       
        {
            $_quantity = $item->quantity;
            if(!is_null($quantity)){
                $_quantity = $quantity;
            }

            if($item instanceof BillItem){
                $inventoryAmountArray[] = $item->sumInventoryAmount();
            }else{
                $inventoryAmountArray[] = $item->sumInventoryAmount($_quantity);
            }

            if($_quantity == 1){
                $itemsInShort .= $item->short;
            }else{
                $itemsInShort .= ($item->short . '*' . $_quantity);
            }
        }
        $materialListText = $this->materialListText($inventoryAmountArray);
        $materialListText .= "[$bill->price]";

        $ship_time = '1';
        if ($bill->ship_time == '14:00-18:00') {
            $ship_time = '2';
        }

        // $arrive = str_replace('-', '/', $bill->ship_arriveDate);
        // if ($bill->ship_arriveDate == null) {
        //     $arrive = date('Y/m/d',strtotime('3 day'));
        // }

        $cash = $bill->price;
        if ($bill->pay_by != '貨到付款') {
            $cash = null;
        }

        $bill->ship_county = str_replace(',','',$bill->ship_county);
        $bill->ship_district = str_replace(',','',$bill->ship_district);
        $bill->ship_address = str_replace(',','',$bill->ship_address);

        $memo = str_replace(',','，',$bill->ship_memo);

        // $row = 
        //     $bill->created_at.",".
        //     $bill->bill_id.",".
        //     '官網'.$bill->pay_by.",".
        //     $cash.",".
        //     $ship_time.",".
        //     $bill->ship_name.",".
        //     $bill->ship_phone.",".
        //     $materialListText.",".
        //     $bill->ship_county.$bill->ship_district.$bill->ship_address.",".
        //     $now.",".
        //     $arrive.",".
        //     $bill->price.",".
        //     $bill->ship_memo . ",".
        //     $itemsInShort;

        
        $bill_id = ($bill->pay_by == Bill::PAY_BY_KOL ? $bill->kolOrderNum : $bill->bill_id);

        $row = 
            $bill->created_at.",".
            $bill_id.",".
            '官網'.$bill->pay_by.",".
            $bill->user_name.",".
            $bill->ship_name.",".
            $bill->ship_county.$bill->ship_district.$bill->ship_address.",".
            $bill->ship_phone.",".
            $materialListText.",".
            $itemsInShort.",".
            $cash.",".
            $ship_time.",".
            $memo;

        return $row;

    }
    private $totalMaterialList = [];
    public function csv_download(Request $request)
    {

        $bills = Bill::whereIn('bill_id',$request->selectArray)->orderBy('id','asc')->get();
        
        $now = date("Y-m-d");
        $orders = [];
        $count = 0;
        foreach($bills as $bill)
        {   
            //全家冷凍超取防呆
            if($bill->carrier_id == Bill::CARRIER_ID_FAMILY_MART){ continue; }

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
                $count += count($gifts);
                continue;
            }

            //一般訂單
            $orders[] = $this->getRow($bill,$now);
            $count += 1;

        }
        foreach ($this->totalMaterialList as $key => $value) {
            $orders[] = ",,,,,,,,,,,,,".$key . "*" . $value;
        }
        $orders[] = ",,,,,,,,,,,,,"."總比數：" . $count;

        $orders = json_encode($orders);
        return response()->json($orders);

    }


    public function ExportExcelForAccountant(Request $request){

        date_default_timezone_set('Asia/Taipei');
        $cellData = [
            ['訂單編號','客戶','交易日期','客戶','購買人','商品貨號','','商品名稱','數量','單位','單價','抵扣紅利','含稅金額','','含稅金額','收件人','郵遞區號','送貨地址','聯絡電話','行動電話','代收宅配單號','代收貨款','付款方式','','','','發票號碼','發票收件人','發票種類','發票統編','買受人名稱','','','','','','信用卡後4碼','','部門','備註'],
        ];
        $bill_id_array = json_decode($request->bill_id);
        $now = date("Y-m-d");
        $inventoryAmountArray = [];

        if($bills = Bill::whereIn('bill_id',$bill_id_array)->orderBy('id','asc')->get()){

            foreach ($bills as $billIndex => $bill) {
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
                    $receiver = $bill->ship_name . '(' . ($billIndex + 1) . ')';
                    $address = $bill->ship_county . $bill->ship_district . $bill->ship_address;
                    $phone = $bill->ship_phone;

                    if($item instanceof BillItem){
                        if(!$product = Products::find($item->product_id)){ continue; }
                        $item->erp_id = $product->erp_id;
                        $inventoryAmountArray[] = $item->sumInventoryAmount();
                    }

                    $cellData[] = $this->getAccountantRow($bill,$item,$index,$item->quantity,$now,$receiver,$address,$phone);
                }

            }
            $this->materialListText($inventoryAmountArray);
            $cellData[] = [];
            foreach ($this->totalMaterialList as $key => $value) {
                $cellData[] = [$key . "*" . $value];
            }
            $cellData[] = ["總比數：" . count($bills)];
        }

        Excel::create('會計訂單輸出-' . $now, function($excel)use($cellData) {
            $excel->sheet('Sheet1', function($sheet)use($cellData) {
                $sheet->rows($cellData);
            });
        })->download('xls');

    }


    public function ExportExcelForShipmentNum(Request $request) {
        date_default_timezone_set('Asia/Taipei');
        $cellData = [
            ['物流','物流單號','預計出貨日期','訂單編號']
        ];
        $now = date("Y-m-d");
        $bill_id_array = json_decode($request->bill_id);

        if($bills = Bill::whereIn('bill_id',$bill_id_array)->orderBy('id','asc')->get()){
            foreach ($bills as $billIndex => $bill) {
                $cellData[] = ['黑貓', $bill->shipmentNum, '', $bill->kolOrderNum];
            }
        }

        $file = '金園排骨貨運單號-' . $now;
        Excel::create($file, function($excel)use($cellData, $file) {
            $excel->sheet($file, function($sheet)use($cellData) {
                $sheet->rows($cellData);
            });
        })->download('csv');

    }


    public function ExportExcelForFamily(Request $request){
        
        $shopId = "099";

        date_default_timezone_set('Asia/Taipei');
        $now = date("Ymd");
        $shipDate= date('Y/m/d',strtotime('1 day'));
        $defaultSize = 'S060';

        $cellData = [
            ['廠商訂單編號','商品價值','預約出貨日期','取件人姓名','取件人手機','材積代號','店名','取貨付款','物料清單','品項'],
        ];

        $bill_id_array = json_decode($request->bill_id);
        if($bills = Bill::whereIn('bill_id',$bill_id_array)->orderBy('id','asc')->get()){

            foreach ($bills as  $bill) {
                if($bill->carrier_id != Bill::CARRIER_ID_FAMILY_MART){ continue; }
                if(!$storeName = $bill->familyStore->name){ continue; }

                $items = $bill->products();
                $inventoryAmountArray = [];
                $itemsInShort = "";
                foreach ($items as $item) {
                    $inventoryAmountArray[] = $item->sumInventoryAmount();
                    if($item->quantity == 1){
                        $itemsInShort .= $item->short;
                    }else{
                        $itemsInShort .= ($item->short . '*' . $item->quantity);
                    }
                }
                $materialListText = $this->materialListText($inventoryAmountArray);

                $pay = "取貨不付款";
                if($bill->pay_by == Bill::PAY_BY_FAMILY){
                    $pay = "取貨付款";
                }
                $ship_phone = str_replace('-','',$bill->ship_phone);
                $cellData[] = [$bill->bill_id,$bill->price,$shipDate,$bill->ship_name,$ship_phone,$defaultSize,$storeName,$pay,$materialListText,$itemsInShort];
            }
        }

        Excel::create($shopId.'-'.$now, function($excel)use($cellData) {
            $excel->sheet('Sheet1', function($sheet)use($cellData) {
                $sheet->rows($cellData);
            });
        })->download('xlsx');
    }

    private function getAccountantRow($bill,$product,$index,$quantity,$now,$receiver,$address,$phone){
        $bill_id = ($bill->pay_by == Bill::PAY_BY_KOL ? $bill->kolOrderNum : $bill->bill_id);
        //$billDate = $bill->created_at;
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
        $erpCustomerId = $bill->getErpCustomerId();
        $erpDeparmentName = $bill->getErpDeparmentName();

        $newRow = [$bill_id,$erpCustomerId,$now,$erpCustomerId,$buyer,$erp_id,null,$productName,$quantity,'組',$price,$bonus,$totalPrice,null,$totalPrice,$receiver,null,$address,$phone,$phone,null,$onDeliveryPrice,$payType,null,null,null,null,$receiver,$invoiceType,$invoice_id,$invoice_company,null,null,null,null,null,null,null,$erpDeparmentName,$bill->ship_memo];
        return $newRow;
    }


    public function ExportExcelForHCT(Request $request) {
        date_default_timezone_set('Asia/Taipei');
        $cellData = [
            ['序號','訂單號','姓名','收件人地址','收件人電話','物料清單','商品別編號','商品數量','材積/重量/總長','代收貨款','指定配送日期','指定配送時間','到貨時段','品名']
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
                        
                        $cellData[] = $this->getHCTRow($bill,$product,$index,$quantity,$now,$receiver,$address,$phone);
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

                    $cellData[] = $this->getHCTRow($bill,$item,$index,$item->quantity,$now,$receiver,$address,$phone);
                }
            }
        }

        Excel::create('新竹物流出貨單輸出-' . $now, function($excel)use($cellData) {
            $excel->sheet('Sheet1', function($sheet)use($cellData) {
                $sheet->rows($cellData);
            });
        })->download('xls');

    }

    private function getHCTRow($bill,$product,$index,$quantity,$now,$receiver,$address,$phone){

        $onDeliveryPrice = 0;
        if($bill->pay_by == '貨到付款' && $index == 0){
            $onDeliveryPrice = $bill->price;
        }

        $inventoryAmountArray = [];
        if($product instanceof BillItem){
            $inventoryAmountArray[] = $product->sumInventoryAmount();
        }else{
            $inventoryAmountArray[] = $product->sumInventoryAmount($quantity);
        }


        $itemsInShort = "";
        if($quantity == 1){
            $itemsInShort .= $product->short;
        }else{
            $itemsInShort .= ($product->short . '*' . $quantity);
        }
        
        $materialListText = $this->materialListText($inventoryAmountArray);
        $materialListText .= "[$bill->price]";

        $arriveAt = '上午';
        if ($bill->ship_time == '14:00-18:00') {
            $arriveAt = '下午';
        }

        $newRow = [
            $bill->ship_memo,   //序號 (放備註)
            $bill->bill_id,
            $receiver,
            $address,
            $phone,
            $materialListText,
            null,
            1,
            60, // 材積 [30 60 90 120] 後續依照條件判斷
            $onDeliveryPrice,
            null, //指定配送日期 YYYYMMDD
            null, //指定配送時間 1 => 9-13; 2 => 13-17; 3 => 17-20; 
            $arriveAt, //到貨時段（自己看的
            $itemsInShort  //品名
        ];
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

    /**結案訂單 */
    public function voidBill($bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $bill->voidBill();
        return redirect('/order/showAll/'.$bill_id);
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

        Log::notice('<變更訂單狀態>');
        Log::notice($id);
        Log::notice('</變更訂單狀態>');

        $bill->nextShipmentPhase();
        return response()->json($bill->shipment);
        
    }


    public function updateShipment(Request $request){

        if(!$request->has('selectArray')){ return response()->json('error',400); }

        $bills = Bill::whereIn('bill_id',$request->selectArray)->get();

        Log::notice('<變更訂單狀態>');
        Log::notice(json_encode($request->selectArray));
        Log::notice('</變更訂單狀態>');

        foreach ($bills as $bill) {
            $bill->nextShipmentPhase();
        }

        return response()->json('success');
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

            if($bill->shipment != Bill::SHIPMENT_VOID){
                $bonus -= $bill->bonus_use * 50;
            }

            if($bill->shipment != Bill::SHIPMENT_VOID){
                
                if($bill->status == 1){
                    $bonus += $bill->get_bonus;
                }else if($bill->isCodGroup() && $bill->shipment == Bill::SHIPMENT_DELIVERED){
                    $bonus += $bill->get_bonus;
                }
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
            $user->bonus = $correct_bonus;
                
            if($correct_bonus < 0){
                $user->bonus = 0;
            }

            $user->save();
            
        }

        return redirect('order/history/' . $user_id);
        
    }

    public function uploadKolOrder(Request $request) {
        $this->validate($request,[
            'excel_data' => 'required',
            'kol' => 'required'
        ]);

        $data = json_decode($request->excel_data, true);
        $excelOrder = new ExcelHelper($data);

        if($existKolOrderNumList = $excelOrder->validateOrderNum($request->kol)) {
            if (count($existKolOrderNumList) > 0) {
                return view('order.uploadResult',[
                    'error' => [
                        '錯誤：已存在單號' => $existKolOrderNumList
                    ]
                ]);
            }
        }

        $excelOrder->save($request->kol);

        // return response($excelOrder->orderList);

        return view('order.uploadResult',[
            'success' => '上傳成功'
        ]);
    }

    public function uploadShipmentNum(Request $request) {
        $this->validate($request,[
            'shipmentNumData' => 'required'
        ]);

        $rows = json_decode($request->shipmentNumData, true);

        $bill_id_array = array_map(function($row){
            return $row['訂單編號'];
        }, $rows);
        $existingBill_id_array = Bill::whereIn('bill_id', $bill_id_array)->pluck('bill_id')->toArray();
        $nonExistingBill_id_array = array_values(array_diff($bill_id_array, $existingBill_id_array));
        
        if (!empty($nonExistingBill_id_array)) {
            return response()->json([
                '錯誤!不存在單號' => $nonExistingBill_id_array
            ]);
        }

        DB::transaction(function() use ($rows){
            foreach ($rows as $row) {
                $bill_id = $row['訂單編號'];
                $shipmentNum = $row['託運單號'];
                Bill::where('bill_id', $bill_id)->update(['shipmentNum' => $shipmentNum]);
            }
        });

        return view('order.uploadResult',[
            'success' => '上傳成功'
        ]);
    }


}
