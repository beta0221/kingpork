<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\Products;
use App\Kart;
use App\User;
use Session;
use DB;
use App\Helpers\ECPay;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
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
            'ship_address'=>'required',
            'ship_email'=>'required|E-mail',
            'ship_pay_by'=>'required',
        ]);

        $additionalProducts = Products::getAdditionalProductSlug();
        $hasAdditionalProduct = false;
        foreach ($request->item as $slug) {
            if(in_array($slug,$additionalProducts)){
                $hasAdditionalProduct = true;
            }
        }
        if($hasAdditionalProduct){
            $totalPrice = Products::totalPriceBySlug($request->item,$additionalProducts);
            if($totalPrice < 500){
                return redirect()->route('kart.index');
            }
        }

        date_default_timezone_set('Asia/Taipei');
        $MerchantTradeNo = time() . rand(10,99);//先給訂單編號

        $user_id = null;
        $user_name = $request->user_name;
        $useBonus = 0;
        $total = 0;
        $getBonus = 0;
        $kart = [];

        foreach ($request->item as $index => $slug) {
            $quantity = $request->quantity[$index];
            $kart[] = [
                'slug' => $slug,
                'quantity' => $request->quantity[$index],
            ];

            $product = Products::where('slug', $slug)->firstOrFail();
            $getBonus += ($product->bonus * (int)$quantity);
            $total += $product->price;
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

        $bill = new Bill;
        $bill->user_id = $user_id;
        $bill->bill_id = $MerchantTradeNo;
        $bill->user_name = $user_name;
        $bill->item = json_encode($kart);
        $bill->bonus_use = $useBonus;
        $bill->price = $total;
        $bill->get_bonus = $getBonus;
        $bill->ship_name = $request->ship_name;
        $bill->ship_gender = $request->ship_gender;
        $bill->ship_phone = $request->ship_phone ;
        $bill->ship_county = $request->ship_county ;
        $bill->ship_district = $request->ship_district ;
        $bill->ship_address = $request->ship_address ;
        $bill->ship_email = $request->ship_email ;
        $bill->ship_arrive = $request->ship_arrive ;
        $bill->ship_arriveDate = $request->ship_arriveDate ;
        $bill->ship_time = $request->ship_time ;
        $bill->ship_receipt = $request->ship_receipt ;
        $bill->ship_three_id = $request->ship_three_id ;
        $bill->ship_three_company = $request->ship_three_company ;
        $bill->ship_memo = $request->ship_memo ;
        $bill->pay_by = $request->ship_pay_by;
        if($request->ship_pay_by == 'cod'){
            $bill->pay_by = '貨到付款';
        }

        $bill->save();


        
        if($user){
            Kart::where('user_id',$user->id)->delete(); //清除購物車
            if($bonus != 0){
                $user->updateBonus($bonus);  //扣除使用者紅利點數
            }
        }
        
        //寄送信件

        switch ($request->ship_pay_by) {
            case 'ATM':
            case 'CREDIT':
                return redirect()->route('payBill',['bill_id'=>$MerchantTradeNo]);
                break;
            case 'cod':
                return redirect()->route('billThankyou',['bill_id'=>$MerchantTradeNo]);
            default:
                break;
        }
        

    }

    public function view_payBill($bill_id){

        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $ecpay = new ECPay($bill);

        if(!$token = $ecpay->getToken()){
            return '錯誤頁面';
        }

        return view('bill.payBill_v2',[
            'bill_id' => $bill_id,
            'token' => $token,
            'ecpaySDKUrl'=> $ecpay->getEcpaySDKUrl(),
        ]);
    }

    public function payBill(Request $request,$bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        if(!$request->has('PayToken')){ return '錯誤頁面'; }
        $ecpay = new ECPay($bill);

        $resultUrl = $ecpay->createPayment($request->PayToken);
        if(!$resultUrl){ return '錯誤頁面'; }
        return redirect($resultUrl);
    }

    /** 付款完成api */
    public function api_ecpay_pay(Request $request,$bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $ecpay = new ECPay($bill);
        $ecpay->handleAtmPayRequest($request);

        Log::info("收到綠界回傳的api了！");
        Log::info("訂單編號：" . $bill_id);
        Log::info(json_encode($request->all()));

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

        return view('bill.detail',[
            'bill' => $bill,
            'products' => $products,
            'atmInfo' => $atmInfo,
            'cardInfo' => $cardInfo
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_deprecate(Request $request)
    {

        Log::info(json_encode($request->getContent()));

        $this->validate($request,[
            'item.*'=>'required',
            'quantity.*'=>'required|integer|min:0',
            'ship_name'=>'required',
            'ship_phone'=>'required',
            'ship_address'=>'required',
            'ship_email'=>'required|E-mail',
            'ship_pay_by'=>'required',
        ]);


        $additionalProducts = Products::getAdditionalProductSlug();
        $hasAdditionalProduct = false;
        foreach ($request->item as $slug) {
            if(in_array($slug,$additionalProducts)){
                $hasAdditionalProduct = true;
            }
        }
        if($hasAdditionalProduct){
            $totalPrice = Products::totalPriceBySlug($request->item,$additionalProducts);
            if($totalPrice < 500){
                return redirect()->route('kart.index');
            }
        }

        $i = 0;
        $itemArray = [];
        foreach($request->item as $item){
            $itemArray[$i] = $item;
            $i++;
        } 


        $j = 0;
        $quantityArray = [];
        foreach($request->quantity as $quantity){
            $quantityArray[$j] = $quantity;
            $j++;
        }

        $cc = DB::table('products')->whereIn('slug', $itemArray)->orderBy('id')->get();
        $n = 0;
        $total = 0;
        $short = '';


        foreach($cc as $c){
            $short = $short.$c->short.'*'.$quantityArray[$n].';';
            $total = $total + (int)($c->price * (int)$quantityArray[$n]);
            $n++;
        }

        if (!in_array('99999',$itemArray) AND $total <= 499) {
            return('錯誤：請檢查網頁JAVASCRIPT是否正常運作');
        }


        //total 打折 (全館95折)
        //$total = ceil($total * 0.95);



        $kart = [];
        for ($i=0; $i < count($itemArray); $i++) { 
            $kart[$i] = [
                'slug' => $itemArray[$i],
                'quantity' => $quantityArray[$i],
            ];
        }

        //如果有25的話判斷總額是否滿1200
        if($result = Products::hasCategory($request->item,25)){
            if (!in_array('99999',$itemArray) AND !in_array('99998',$itemArray) AND $total < 1200) {
                $kart[] = [
                    'slug' => '99998',
                    'quantity' => 1,
                ];
                $total += 150;
            }
        }

        if(Auth::user()){
            $bonus = $request->bonus;               // bonus{
            if ($bonus > Auth::user()->bonus) {
                $bonus = Auth::user()->bonus;
            }
            if (fmod($bonus,50) != 0) {
                $bonus = $bonus - fmod($bonus,50);
            }
            if ($bonus / 50 > $total) {
                $bonus = $total * 50;
            }
            if ($bonus < 0) {
                $bonus = 0;
            }
            $bonusCount = $bonus / 50;
            $total = $total - $bonusCount;          // }bonus    
        }else{
            $bonusCount = 0;
        }

        date_default_timezone_set('Asia/Taipei');

        $MerchantTradeNo = time() . rand(10,99);//先給訂單編號

//---------------------------------------------------------------------
        if (Auth::user()) {
            $user_id = Auth::user()->id;
            $user_name = Auth::user()->name;
        }else{
            $user_id = null;
            $user_name = $request->user_name;
        }


        $itemArray = [];
        $getBonus = 0;
        foreach($kart as $item)
        {
            $product = Products::where('slug', $item['slug'])->firstOrFail();   
            $itemArray[] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $item['quantity'],
            ];
            $getBonus += ($product->bonus * (int)$item['quantity']);
        }



        if ($request->ship_pay_by=='ATM'OR$request->ship_pay_by=='CREDIT') {

            //test
            // $HashKey = '5294y06JbISpM5x9';
            // $HashIV = 'v77hoKGq4kWxNNIS';
            // $MerchantID = '2000132';
            //kingPork
            $HashKey = '6HWkOeX5RsDZnDFn';
            $HashIV = 'Zfo3Ml2OQXRmnjha';
            $MerchantID = '1044372';

            
            $MerchantTradeDate = date('Y\/m\/d H:i:s');
            $PaymentType = 'aio';
            $TotalAmount = $total;
            $TradeDesc = '金園排骨官方商城';
            $ItemName = $short;
            $ReturnURL = 'http://45.76.104.218/api/billPaied';
            $ChoosePayment = 'ALL';
            $NeedExtraPaidInfo='Y';
            $EncryptType = 1;

            $all = 'HashKey='.$HashKey . '&' .
                   'ChoosePayment='.$ChoosePayment . '&' .
                   'EncryptType='.$EncryptType . '&' .
                   'ItemName='.$ItemName . '&' .
                   'MerchantID='.$MerchantID . '&' .
                   'MerchantTradeDate='.$MerchantTradeDate . '&' .
                   'MerchantTradeNo='.$MerchantTradeNo . '&' .
                   'NeedExtraPaidInfo='.$NeedExtraPaidInfo. '&' .
                   'PaymentType='.$PaymentType . '&' . 
                   'ReturnURL='.$ReturnURL . '&' . 
                   'TotalAmount='.$TotalAmount . '&' . 
                   'TradeDesc='.$TradeDesc . '&' . 
                   'HashIV='.$HashIV;

            // $CheckMacValue = strtoupper(hash('sha256', strtolower(urlencode($all))));
            $CheckMacValue = strtolower(urlencode($all));

            $CheckMacValue = str_replace('%2d', '-', $CheckMacValue);
            $CheckMacValue = str_replace('%5f', '_', $CheckMacValue);
            $CheckMacValue = str_replace('%2e', '.', $CheckMacValue);
            $CheckMacValue = str_replace('%21', '!', $CheckMacValue);
            $CheckMacValue = str_replace('%2a', '*', $CheckMacValue);
            $CheckMacValue = str_replace('%28', '(', $CheckMacValue);
            $CheckMacValue = str_replace('%29', ')', $CheckMacValue);
            $CheckMacValue = hash('sha256',$CheckMacValue);
            $CheckMacValue = strtoupper($CheckMacValue);

            // return($CheckMacValue);

            $client = new \GuzzleHttp\Client();
            $response = $client->post(
                // 'https://payment-stage.ecpay.com.tw/SP/CreateTrade',
                'https://payment.ecpay.com.tw/SP/CreateTrade',
                [
                    'form_params' => [
                        'MerchantID' => $MerchantID,
                        'MerchantTradeNo' => $MerchantTradeNo,
                        'MerchantTradeDate' => $MerchantTradeDate,
                        'PaymentType' => $PaymentType,
                        'TotalAmount' => $TotalAmount,
                        'TradeDesc' => $TradeDesc,
                        'ItemName' => $ItemName,
                        'ReturnURL' => $ReturnURL,
                        'ChoosePayment' => $ChoosePayment,
                        'NeedExtraPaidInfo' => $NeedExtraPaidInfo,
                        'CheckMacValue' => $CheckMacValue,
                        'EncryptType' => $EncryptType
                    ]
                ]
            );

            $body = $response->getBody();
            $phpBody = json_decode($body);

            

            // return($body);
            if ($phpBody->{'RtnCode'} == 1) {
                $SPToken = $phpBody->{'SPToken'};
                $bill = new Bill;
                $bill->user_id = $user_id;
                $bill->bill_id = $MerchantTradeNo;
                $bill->user_name = $user_name;
                $bill->item = json_encode($kart);
                $bill->bonus_use = $bonusCount;
                $bill->price = $total;
                $bill->get_bonus = $getBonus;
                $bill->SPToken = $SPToken;              //SPToken
                $bill->ship_name = $request->ship_name;
                $bill->ship_gender = $request->ship_gender;
                $bill->ship_phone = $request->ship_phone ;
                $bill->ship_county = $request->ship_county ;
                $bill->ship_district = $request->ship_district ;
                $bill->ship_address = $request->ship_address ;
                $bill->ship_email = $request->ship_email ;
                $bill->ship_arrive = $request->ship_arrive ;
                $bill->ship_arriveDate = $request->ship_arriveDate ;
                $bill->ship_time = $request->ship_time ;
                $bill->ship_receipt = $request->ship_receipt ;
                // $bill->ship_three_name = $request->ship_three_name ;
                $bill->ship_three_id = $request->ship_three_id ;
                $bill->ship_three_company = $request->ship_three_company ;
                $bill->ship_memo = $request->ship_memo ;
                $bill->pay_by = $request->ship_pay_by;
                $bill->save();
            }else{
                print_r(json_decode((string)$body));
            }    


        }elseif ($request->ship_pay_by == 'cod') {
            

            $bill = new Bill;
            $bill->user_id = $user_id;
            $bill->bill_id = $MerchantTradeNo;
            $bill->user_name = $user_name;
            $bill->item = json_encode($kart);
            $bill->bonus_use = $bonusCount;
            $bill->price = $total;
            $bill->get_bonus = $getBonus;
            $bill->ship_name = $request->ship_name;
            $bill->ship_gender = $request->ship_gender;
            $bill->ship_phone = $request->ship_phone;
            $bill->ship_county = $request->ship_county;
            $bill->ship_district = $request->ship_district;
            $bill->ship_address = $request->ship_address;
            $bill->ship_email = $request->ship_email;
            $bill->ship_arrive = $request->ship_arrive;
            $bill->ship_arriveDate = $request->ship_arriveDate;
            $bill->ship_time = $request->ship_time;
            $bill->ship_receipt = $request->ship_receipt;
            // $bill->ship_three_name = $request->ship_three_name;
            $bill->ship_three_id = $request->ship_three_id;
            $bill->ship_three_company = $request->ship_three_company;
            $bill->ship_memo = $request->ship_memo;
            $bill->pay_by = '貨到付款';
            $bill->save();


            $data = array(
                'user_name'=>$user_name,
                'ship_gender'=>$request->ship_gender,
                'ship_name'=>$request->ship_name,
                'ship_phone'=>$request->ship_phone,
                'ship_county'=>$request->ship_county,
                'ship_district'=>$request->ship_district,
                'ship_address'=>$request->ship_address,
                'email' => $request->ship_email,
                'items' => $itemArray,
                'bill_id' =>$MerchantTradeNo,
                'bonus_use'=>$bonusCount,
                'price' => $total,
                'pay_by'=>'貨到付款',
            );
            Mail::send('emails.cod',$data,function($message) use ($data){
                $message->from('kingpork80390254@gmail.com','金園排骨');
                $message->to($data['email']);
                $message->subject('金園排骨-購買確認通知');
            });

        }

//-----------------------------------------------------------------------
        if (Auth::user()) {
            Kart::where('user_id',Auth::user()->id)->delete();
            $user = User::find(Auth::user()->id);
            $user->bonus = $user->bonus - $bonus;
            $user->save();
        }

        Session::flash('success','訂單已成功送出');
        // return redirect()->route('bill.show', $MerchantTradeNo);
        return redirect()->route('bill.purchaseComplete', $MerchantTradeNo);
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

    // public function creditPaied(Request $request)
    // {
        
    //     if ($request->status == 0) {
    //         $the = Bill::where('bill_id',$request->lidm)->firstOrFail();
    //         $the->status = 1;
    //         $allReturn = 
    //         'status='.$request->status.'|'.
    //         'errcode='.$request->errcode.'|'.
    //         'authCode='.$request->authCode.'|'.
    //         'authAmt='.$request->authAmt.'|'.
    //         'lidm='.$request->lidm.'|'.
    //         'xid='.$request->xid.'|'.
    //         'merID='.$request->merID.'|'.
    //         'Last4digitPAN='.$request->Last4digitPAN.'|'.
    //         'errDesc='.$request->errDesc.'|'.
    //         'checkValue='.$request->checkValue;
    //         $the->allReturn = $allReturn;
    //         $the->save();

    //         $user = User::where('name',$the->user_name)->firstOrFail();//紅利回算機制{
    //         $authAmt = (int)$request->authAmt;
    //         $user->bonus = $user->bonus+$authAmt;
    //         $user->save();                                          // }紅利回算機制

    //         return redirect()->route('bill.show', $request->lidm);
    //     }else{
    //         return redirect()->route('bill.show', $request->lidm);
    //     }
        
    // }                                              // }!!! API !!!




    public function checkBill($id)
    {
        //test
        // $HashKey = '5294y06JbISpM5x9';
        // $HashIV = 'v77hoKGq4kWxNNIS';
        // $MerchantID = '2000132';
        //kingPork
        $HashKey = '6HWkOeX5RsDZnDFn';
        $HashIV = 'Zfo3Ml2OQXRmnjha';
        $MerchantID = '1044372';

        $MerchantTradeNo = $id;
        $PlatformID = '';
        $TimeStamp = time();
        $all = 'HashKey=' . $HashKey . '&' .
                'MerchantID=' . $MerchantID . '&' .
                'MerchantTradeNo=' . $MerchantTradeNo . '&' .
                'PlatformID=' . '' . '&' .
                'TimeStamp=' . $TimeStamp . '&' .
                'HashIV='.$HashIV;

        $CheckMacValue = strtolower(urlencode($all));
        $CheckMacValue = str_replace('%2d', '-', $CheckMacValue);
        $CheckMacValue = str_replace('%5f', '_', $CheckMacValue);
        $CheckMacValue = str_replace('%2e', '.', $CheckMacValue);
        $CheckMacValue = str_replace('%21', '!', $CheckMacValue);
        $CheckMacValue = str_replace('%2a', '*', $CheckMacValue);
        $CheckMacValue = str_replace('%28', '(', $CheckMacValue);
        $CheckMacValue = str_replace('%29', ')', $CheckMacValue);
        $CheckMacValue = hash('sha256',$CheckMacValue);
        $CheckMacValue = strtoupper($CheckMacValue);



        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            // 'https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/V5',
            'https://payment.ecpay.com.tw/Cashier/QueryTradeInfo/V5',
            [
                'form_params' => [
                    'MerchantID' => $MerchantID,
                    'MerchantTradeNo' => $MerchantTradeNo,
                    'TimeStamp' => $TimeStamp,
                    'PlatformID' => $PlatformID,
                    'CheckMacValue' => $CheckMacValue
                ]
            ]
        );
        $body = $response->getBody();
        $phpBody = json_decode($body);
        return($body);
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

        $bill = Bill::where('user_id',Auth::user()->id)
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
                'ship_receipt' => $bill->ship_receipt,
                // 'ship_three_name' => $bill->ship_three_name,
                'ship_three_id' => $bill->ship_three_id,
                'ship_three_company' => $bill->ship_three_company,
                'bonus' => Auth::user()->bonus,
            ]);
        }else{
            return response()->json([
                'ifMemory'=>0,
                'bonus' => Auth::user()->bonus, 
            ]);
        }
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
