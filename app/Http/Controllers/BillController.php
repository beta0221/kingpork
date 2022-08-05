<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\BillItem;
use App\FamilyStore;
use App\Products;
use App\Kart;
use App\User;
use Session;
use DB;
use App\Helpers\ECPay;
use App\Jobs\ECPayInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mail;


// require 'vendor/autoload.php';

class BillController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth',['only'=>['index','findMemory','cancelBill']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bills = Bill::where('user_id',Auth::user()->id)->orderBy('id','desc')->paginate(10);
        return view('bill.index',['bills'=>$bills]);
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

    public function pay(){

    }

    public function store(Request $request){

        $this->validate($request,[
            'item.*'=>'required',
            'quantity.*'=>'required|integer|min:0',
            'ship_name'=>'required',
            'ship_phone'=>'required',
            'ship_address'=>'required_if:carrier_id,0',
            'ship_email'=>'required|E-mail',
            'ship_pay_by'=>'required',
            'carrier_id'=>'required',
            'store_number'=>'required_if:carrier_id,1',
            'store_name'=>'required_if:carrier_id,1',
            'store_address'=>'required_if:carrier_id,1',
        ]);

        if($request->carrier_id == Bill::CARRIER_ID_FAMILY_MART && $request->ship_pay_by == 'cod'){
            return ('錯誤');
        }

        $additionalProducts = Products::getAdditionalProductSlug();
        $hasAdditionalProduct = false;
        $hasMainProduct = false;

        foreach ($request->item as $slug) {
            if(in_array($slug,$additionalProducts)){
                $hasAdditionalProduct = true;
            }else{
                if($slug != "99999"){   //排除運費
                    $hasMainProduct = true;
                }
            }
        }

        if($hasAdditionalProduct == true && $hasMainProduct == false){
            return redirect()->route('kart.index');
        }

        date_default_timezone_set('Asia/Taipei');
        $MerchantTradeNo = time() . rand(10,99);//先給訂單編號

        $user_id = null;
        $user_name = $request->user_name;
        $useBonus = 0;
        $total = 0;
        $getBonus = 0;
        $products = [];

        foreach ($request->item as $index => $slug) {
            $quantity = $request->quantity[$index];
            $product = Products::where('slug', $slug)->firstOrFail();
            $product->quantity = (int)$quantity;
            $products[] = $product;

            $getBonus += ($product->bonus * (int)$quantity);
            $total += ($product->price * (int)$quantity);
        }

        if (!in_array('99999',$request->item) AND $total <= 499) { return('錯誤'); }

        $user = $request->user();
        if($user){
            $user_id = $user->id;
            $user_name = $user->name;

            $bonus = $request->bonus;               // bonus{
            if ($bonus > $user->bonus) { $bonus = $user->bonus; }
            if (fmod($bonus,50) != 0) { $bonus = $bonus - fmod($bonus,50); }
            if ($bonus / 50 > $total) { $bonus = $total * 50; }
            if ($bonus < 0) { $bonus = 0; }
            $useBonus = $bonus / 50;
            $total = $total - $useBonus;          // }bonus    
        }
        $bill = Bill::insert_row($user_id,$user_name,$MerchantTradeNo,$useBonus,$total,$getBonus,$request);

        foreach ($products as $product) {
            BillItem::insert_row($bill->id,$product);
        }
        if($request->carrier_id == Bill::CARRIER_ID_FAMILY_MART){
            FamilyStore::insert_row($bill->id,$request);
        }        

        
        if($user){
            Kart::where('user_id',$user->id)->delete(); //清除購物車
            if($bonus != 0){
                $user->updateBonus($bonus);  //扣除使用者紅利點數
            }
        }
        
        //寄送信件
        switch ($request->ship_pay_by) {
            case Bill::PAY_BY_CREDIT:
            case Bill::PAY_BY_ATM:
                return redirect()->route('payBill',['bill_id'=>$MerchantTradeNo]);
                break;
            case 'cod':
            case Bill::PAY_BY_FAMILY:
                return redirect()->route('billThankyou',['bill_id'=>$MerchantTradeNo]);
            default:
                break;
        }

    }

    public function view_payBill($bill_id){

        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $ecpay = new ECPay($bill);

        if(!$token = $ecpay->getToken()){
            return '系統錯誤';
        }

        return view('bill.payBill_v2',[
            'bill_id' => $bill_id,
            'token' => $token,
            'ecpaySDKUrl'=> $ecpay->getEcpaySDKUrl(),
        ]);
    }

    public function payBill(Request $request,$bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        if(!$request->has('PayToken')){ return '錯誤頁面。'; }
        $ecpay = new ECPay($bill);

        $resultUrl = $ecpay->createPayment($request->PayToken);

        // if(!is_null($resultUrl) && $bill->pay_by == 'CREDIT'){
        //     $bill->status = 1;
        //     $bill->save();
        //     $bill->sendBonusToBuyer();
        //     dispatch(new ECPayInvoice($bill,ECPayInvoice::TYPE_ISSUE)); //開立發票
        // }

        if(!$resultUrl){ return '錯誤頁面'; }
        return redirect($resultUrl);
    }

    /** 付款完成api */
    public function api_ecpay_pay(Request $request,$bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $ecpay = new ECPay($bill);
        $isSuccess = $ecpay->handlePayRequest($request);

        if($isSuccess){
            $bill->status = 1;
            $bill->save();
            $bill->sendBonusToBuyer();
            dispatch(new ECPayInvoice($bill,ECPayInvoice::TYPE_ISSUE)); //開立發票
        }

        Log::info("-----綠界回傳-----");
        Log::info("訂單編號：" . $bill_id);
        Log::info(json_encode($request->all()));
        Log::info("-----------------");


        return "1|OK";
    }

    /** 綠界付款完成頁面  */
    public function view_ecpay_thankyouPage($bill_id){
        return redirect()->route('billThankyou',['bill_id'=>$bill_id]);
    }

    public function view_billThankyou($bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $products = $bill->products();

        return view('bill.thankyou',[
            'bill'=>$bill,
            'products' => $products,
        ]);
    }

    public function view_billDetail($bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $products = $bill->products();
        $atmInfo = null;
        $cardInfo = null;
        $storeInfo = null;

        if($data = $bill->getPaymentInfo()){
            switch ($bill->pay_by) {
                case 'ATM':
                    if(isset($data['ATMInfo'])){
                        $atmInfo = (object)$data[ECPay::PAYMENT_INFO_ATM];
                    }
                    break;
                case 'CREDIT':
                    if(isset($data['CardInfo'])){
                        $cardInfo = (object)$data[ECPay::PAYMENT_INFO_CARD];
                    }
                    break;
                default:
                    break;
            }
        }

        if($bill->carrier_id == Bill::CARRIER_ID_FAMILY_MART){
            $storeInfo = $bill->familyStore;
        }

        return view('bill.detail',[
            'bill' => $bill,
            'carrierDict' => Bill::getAllCarriers(),
            'products' => $products,
            'atmInfo' => $atmInfo,
            'cardInfo' => $cardInfo,
            'storeInfo' => $storeInfo
        ]);
    }


    public function getDataLayerForGA($bill_id){

        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();

        $items = json_decode($bill->item,true);

        

        $products=[];
        foreach ($items as $item) {
            // return response()->json($item);
            $product = Products::where('slug',$item['slug'])->first();
            
            $obj = [
                'name'=>$product->name,
                'id'=>(string)$product->id,
                'price'=>$product->price,
                'category'=>$product->productCategory()->first()->name,
                'quantity'=>(int)$item['quantity'],
            ];
            $products[] = $obj;
        }

        

        $actionField = [];
        $actionField['id'] = $bill_id;
        $actionField['revenue'] = $bill->price;

        $purchase = [];
        $ecommerce = [];

        $dataLayer = [];
        $dataLayer['ecommerce'] = [];
        $dataLayer['ecommerce']['purchase'] = [];
        $dataLayer['ecommerce']['purchase']['actionField'] = $actionField;
        $dataLayer['ecommerce']['purchase']['products'] = $products;
        $dataLayer['event'] = 'purchaseComplete';
        $dataLayer['currencyCode'] = 'TWD';


        // return response()->json($dataLayer);
        return $dataLayer;

    }



    public function billPaied(Request $request)     // !!! API !!!{
    {

        $MerchantID = $request->MerchantID;
        $MerchantTradeNo = $request->MerchantTradeNo;
        $StoreID = $request->StoreID;
        $RtnCode = $request->RtnCode; //
        $RtnMsg = $request->RtnMsg; //
        $TradeNo = $request->TradeNo; //
        $TradeAmt = $request->TradeAmt;
        $PaymentDate = $request->PaymentDate; //
        $PaymentType = $request->PaymentType;
        $PaymentTypeChargeFee = $request->PaymentTypeChargeFee; //
        $TradeDate = $request->TradeDate; //
        $SimulatePaid = $request->SimulatePaid; //
        $CustomField1 = $request->CustomField1;
        $CustomField2 = $request->CustomField2;
        $CustomField3 = $request->CustomField3;
        $CustomField4 = $request->CustomField4;
        $CheckMacValue = $request->CheckMacValue;
        
        $auth_code = $request->auth_code;

        $allReturn = 
        'MerchantID='.$MerchantID.'&'.
        'MerchantTradeNo='.$MerchantTradeNo.'&'.
        'StoreID='.$StoreID.'&'.
        'RtnCode='.$RtnCode.'&'.
        'RtnMsg='.$RtnMsg.'&'.
        'TradeNo='.$TradeNo.'&'.
        'TradeAmt='.$TradeAmt.'&'.
        'PaymentDate='.$PaymentDate.'&'.
        'PaymentType='.$PaymentType.'&'.
        'PaymentTypeChargeFee='.$PaymentTypeChargeFee.'&'.
        'TradeDate='.$TradeDate.'&'.
        'SimulatePaid='.$SimulatePaid.'&'.
        'CustomField1='.$CustomField1.'&'.
        'CustomField2='.$CustomField2.'&'.
        'CustomField3='.$CustomField3.'&'.
        'CustomField4='.$CustomField4.'&'.
        'CheckMacValue='.$CheckMacValue; //

        // $data = array(.     //測試用
        //         'MerchantID'=>$MerchantID,
        //         'MerchantTradeNo'=>$MerchantTradeNo,
        //         'StoreID'=>$StoreID,
        //         'RtnCode'=>$RtnCode,
        //         'RtnMsg'=>$RtnMsg,
        //         'TradeNo'=>$TradeNo,
        //         'TradeAmt'=>$TradeAmt,
        //         'PaymentDate' => $PaymentDate,
        //         'PaymentType' => $PaymentType,
        //         'PaymentTypeChargeFee' =>$PaymentTypeChargeFee,
        //         'TradeDate'=>$TradeDate,
        //         'SimulatePaid' => $SimulatePaid,
        //         'CheckMacValue'=>$CheckMacValue,
        //     );
        //     Mail::send('emails.test',$data,function($message) use ($data){
        //         $message->from('beta0221@gmail.com','金園排骨');
        //         $message->to('beta0221@gmail.com');
        //         $message->subject('綠界回傳測試');
        //     });

        if ($RtnCode == 1) {
            $the = Bill::where('bill_id',$MerchantTradeNo)->firstOrFail();
            $the->status = 1;
            
            $the->RtnCode = $RtnCode;
            $the->RtnMsg = $RtnMsg;
            $the->TradeNo = $TradeNo;
            $the->PaymentDate = $PaymentDate;
            $the->PaymentTypeChargeFee = $PaymentTypeChargeFee;
            $the->TradeDate = $TradeDate;
            $the->SimulatePaid = $SimulatePaid;
            $the->auth_code = $auth_code;
            // $the->allReturn = $allReturn;
            $the->save();

            //$user = User::where('name',$the->user_name)->firstOrFail();//紅利回算機制{
            $user = User::find($the->user_id);
            $TradeAmt = (int)$the->get_bonus;
            $user->bonus = $user->bonus+$TradeAmt;
            $user->save();                                          //}紅利回算機制


            // $user = User::find($bill->user_id);
                    
                    
        }
        return('1|OK');
    }




    public function sendMail(Request $request)
    {   
        $bill = Bill::where('bill_id',$request->MerchantTradeNo)->firstOrFail();
        
        if ($request->pay_by=='ATM') {
            $bill->status = 's';
            $bill->save();
        }elseif ($request->pay_by == 'CREDIT') {
            $bill->status = '1';
            $bill->save();
        }
        
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


        if ($request->pay_by == 'ATM') {
            $data = array(
                'user_name'=>$bill->ship_name,
                'ship_gender'=>$bill->ship_gender,
                'ship_name'=>$bill->ship_name,
                'ship_phone'=>$bill->ship_phone,
                'ship_county'=>$bill->ship_county,
                'ship_district'=>$bill->ship_district,
                'ship_address'=>$bill->ship_address,
                'email' => $bill->ship_email,
                'items' => $itemArray,
                'bill_id' =>$bill->bill_id,
                'bonus_use'=>$bill->bonus_use,
                'price' => $bill->price,
                'pay_by'=>'ATM轉帳繳費',
                'TradeDate'=>$request->TradeDate,
                'BankCode'=>$request->BankCode,
                'vAccount'=>$request->vAccount,
                'ExpireDate'=>$request->ExpireDate,
            );
            Mail::send('emails.atm',$data,function($message) use ($data){
                $message->from('kingpork80390254@gmail.com','金園排骨');
                $message->to($data['email']);
                $message->subject('金園排骨-購買確認通知');
            });

            return response()->json('s');

        }elseif ($request->pay_by == 'CREDIT') {
            $data = array(
                'user_name'=>$bill->ship_name,
                'ship_gender'=>$bill->ship_gender,
                'ship_name'=>$bill->ship_name,
                'ship_phone'=>$bill->ship_phone,
                'ship_county'=>$bill->ship_county,
                'ship_district'=>$bill->ship_district,
                'ship_address'=>$bill->ship_address,
                'email' => $bill->ship_email,
                'items' => $itemArray,
                'bill_id' =>$bill->bill_id,
                'bonus_use'=>$bill->bonus_use,
                'price' => $bill->price,
                'pay_by'=>'信用卡繳費',
                'TradeDate'=>$request->TradeDate,
            );
            Mail::send('emails.credit',$data,function($message) use ($data){
                $message->from('kingpork80390254@gmail.com','金園排骨');
                $message->to($data['email']);
                $message->subject('金園排骨-購買確認通知');
            });

            return response()->json('s');
        }
        

        
    }
    // public function sendMailC(Request $request)
    // {
    //     $bill = Bill::where('bill_id',$request->bill_id)->firstOrFail();
    //     if ($bill->SimulatePaid != 1) {
    //         $bill->SimulatePaid = 1;
    //         $bill->save();

    //         $items = json_decode($bill->item,true);
    //         $i = 0;
    //         $itemArray = [];
    //         foreach($items as $item)
    //         {
    //             $product = Products::where('slug', $item['slug'])->firstOrFail();   
    //             $itemArray[$i] = [
    //                 'name' => $product->name,
    //                 'price' => $product->price,
    //                 'quantity' => $item['quantity'],
    //             ];
    //             $i++;
    //         }
    //         $data = array(
    //             'user_name'=>Auth::user()->name,
    //             'ship_gender'=>$bill->ship_gender,
    //             'ship_name'=>$bill->ship_name,
    //             'ship_phone'=>$bill->ship_phone,
    //             'ship_county'=>$bill->ship_county,
    //             'ship_district'=>$bill->ship_district,
    //             'ship_address'=>$bill->ship_address,
    //             'email' => $bill->ship_email,
    //             'items' => $itemArray,
    //             'bill_id' =>$bill->bill_id,
    //             'bonus_use'=>$bill->bonus_use,
    //             'price' => $bill->price,
    //             'pay_by'=>'信用卡繳費',
    //         );
    //         Mail::send('emails.cod',$data,function($message) use ($data){
    //             $message->from('beta0221@gmail.com','金園排骨');
    //             $message->to($data['email']);
    //             $message->subject('金園排骨-購買確認通知');
    //         });
    //         return response()->json('s');
    //     }else{
    //         return response()->json('1');
    //     }
        
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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

        $finalBill = [
            'bill_id' => $bill->bill_id,
            'bonus_use'=>$bill->bonus_use,
            'price' => $bill->price,
            'itemArray' => $itemArray,
            'SPToken'=> $bill->SPToken,
            'pay_by'=>$bill->pay_by,
            'status'=>$bill->status,
        ];

        // if ($bill->pay_by == 'CREDIT') {
            // $HV1 = md5('rhRwy1KRNsQbjgXR'.'|'.$bill->bill_id);
            // $V2 = md5($HV1 . "|" . "008786350353296" . "|" . "77543256" . "|" . $bill->price);
            // $checkValue = substr($V2,16,32);
            // return view('bill.payBill', ['finalBill'=>$finalBill,'checkValue'=>$checkValue]);
        // }
        
        return view('bill.payBill', ['finalBill'=>$finalBill]);  
        
    }


    public function purchaseComplete($id){
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

        $finalBill = [
            'bill_id' => $bill->bill_id,
            'bonus_use'=>$bill->bonus_use,
            'price' => $bill->price,
            'itemArray' => $itemArray,
            'SPToken'=> $bill->SPToken,
            'pay_by'=>$bill->pay_by,
            'status'=>$bill->status,
        ];

        // if ($bill->pay_by == 'CREDIT') {
            // $HV1 = md5('rhRwy1KRNsQbjgXR'.'|'.$bill->bill_id);
            // $V2 = md5($HV1 . "|" . "008786350353296" . "|" . "77543256" . "|" . $bill->price);
            // $checkValue = substr($V2,16,32);
            // return view('bill.payBill', ['finalBill'=>$finalBill,'checkValue'=>$checkValue]);
        // }
        $dataLayer = $this->getDataLayerForGA($id);
        return view('bill.payBill', [
                'finalBill'=>$finalBill,
                'dataLayer'=>json_encode($dataLayer)
            ]
        );  
    }



    public function findMemory()
    {
        $user = Auth::user();
        $bill = Bill::where('user_id',$user->id)
            ->where('ship_name','!=','*')
            ->orderBy('id','desc')
            ->first();
        
        if($bill){
            return response()->json([
                'ifMemory'=>1,
                'ship_name' => $bill->ship_name,
                'ship_gender' => $bill->ship_gender,
                'ship_phone' => $bill->ship_phone,
                'ship_county' => $bill->ship_county,
                'ship_district' => $bill->ship_district,
                'ship_address' => $bill->ship_address,
                'ship_email' => $bill->ship_email,
                'carrier_id' => $bill->carrier_id,
                'ship_receipt' => $bill->ship_receipt,
                'ship_three_id' => $bill->ship_three_id,
                'ship_three_company' => $bill->ship_three_company,
                'bonus' => $user->bonus,
            ]);
        }

        return response()->json([
            'ifMemory'=>0,
            'bonus' => $user->bonus, 
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
        // $bill = Bill::where('bill_id',$id)->firstOrFail();
        // if ($bill->status == '0') {


        //     $user = User::find($bill->user_id);
        //     $user->bonus = $user->bonus + $bill->bonus_use * 50;
        //     $user->save();

        //     $delete = Bill::where('bill_id',$id)->delete();

        //     if($delete){
        //         return response()->json('1');
        //     }else{
        //         return response()->json('0');
        //     }
        // }else{
        //     return response()->json('s');
        // }
        
    }

    public function cancelBill($id)
    {
        $bill = Bill::where('bill_id',$id)->firstOrFail();
        $user = Auth::user();

        if($bill->user_id != $user->id){
            return response()->json('error');
        }
        if ($bill->status == 1 || $bill->shipment != 0){
            return response()->json('error');   
        }

        $amount = $bill->bonus_use * 50;
        $user->updateBonus($amount,false);

        $bill->updateShipment(Bill::SHIPMENT_VOID);
        return response()->json('success');

    }

}
