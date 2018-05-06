<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\Products;
use App\Kart;
use App\User;
use Session;
use DB;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Mail;


// require 'vendor/autoload.php';

class BillController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth',['except'=>['billPaied','creditPaied']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if(Auth::user()){

            $records = Bill::all()->where('user_id',Auth::user()->id);
            // ->orderBy('created_at', 'desc')
                
                if (count($records) == 0) {
                    return('沒有訂單');
                }

            $i = 0;
            $bill=[];
            foreach($records as $record)
            {   
                $bill[$i] = json_decode($record->item,true);
                $i++;
            }

            $products = [];
            $finalBill = [];
            for ($x=0; $x < count($records); $x++) { 
                for ($y=0; $y < count($bill[$x]); $y++) { 
                    
                    $products[$x][$y]=Products::where('slug',$bill[$x][$y]['slug'])->get();

                    $finalBills[$x][$y] = [

                        'name' => $products[$x][$y][0]->name, //產品名稱
                        'price' => $products[$x][$y][0]->price, //產品單價
                        'quantity' => $bill[$x][$y]['quantity'], //產品數量
                        'bill_id' => $records[$x]->bill_id,     //訂單編號
                        'total' => $records[$x]->price,         //總價
                        'status' => $records[$x]->status,       //付款狀態
                        'shipment' => $records[$x]->shipment,   //出貨狀態
                        'pay_by' => $records[$x]->pay_by,       //付款方式
                        'SPToken' => $records[$x]->SPToken,     //SPToken
                        'created_at' => $records[$x]->created_at, //訂購日期

                    ];
                }
            }

            return view('bill.index',['finalBills'=>$finalBills]);

        // }else{

        //     return redirect('login');

        // }
        
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
        $this->validate($request,[
            'item.*'=>'required',
            'quantity.*'=>'required|integer|min:1',
            'ship_name'=>'required',
            'ship_phone'=>'required',
            'ship_address'=>'required',
            'ship_email'=>'required|E-mail',
            'ship_pay_by'=>'required',
        ]);
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
        $cc = DB::table('products')->whereIn('slug', $itemArray)->get();
        $n = 0;
        $total = 0;
        foreach($cc as $c){
            $total = $total + ($c->price * $quantityArray[$n]);
            $n++;
        }
        $kart = [];
        for ($i=0; $i < count($itemArray); $i++) { 
            $kart[$i] = [
                'slug' => $itemArray[$i],
                'quantity' => $quantityArray[$i],
            ];
        }

        $MerchantTradeNo = 'kp' . time() ;//先給訂單編號

        switch ($request->ship_pay_by) {
            case 'atm':                             //  Pay By ATM !!!
            //test
            $HashKey = '5294y06JbISpM5x9';
            $HashIV = 'v77hoKGq4kWxNNIS';
            $MerchantID = '2000132';
            //kingPork
            // $HashKey = '6HWkOeX5RsDZnDFn';
            // $HashIV = 'Zfo3Ml2OQXRmnjha';
            // $MerchantID = '1044372';

            date_default_timezone_set('Asia/Taipei');
            $MerchantTradeDate = date('Y\/m\/d H:i:s');
            $PaymentType = 'aio';
            $TotalAmount = $total;
            $TradeDesc = 'ecpay商城購物';
            $ItemName = '商品名稱1#商品名稱2';
            $ReturnURL = 'http://45.76.104.218/api/billPaied';
            $ChoosePayment = 'ALL';
            $EncryptType = '1';

            $all = 'HashKey='.$HashKey . '&' .
                   'ChoosePayment='.$ChoosePayment . '&' . 
                   'EncryptType='.$EncryptType . '&' . 
                   'ItemName='.$ItemName . '&' . 
                   'MerchantID='.$MerchantID . '&' . 
                   'MerchantTradeDate='.$MerchantTradeDate . '&' . 
                   'MerchantTradeNo='.$MerchantTradeNo . '&' . 
                   'PaymentType='.$PaymentType . '&' . 
                   'ReturnURL='.$ReturnURL . '&' . 
                   'TotalAmount='.$TotalAmount . '&' . 
                   'TradeDesc='.$TradeDesc . '&' . 
                   'HashIV='.$HashIV;

            $CheckMacValue = hash('sha256', strtolower(urlencode($all)));

            $client = new \GuzzleHttp\Client();
            $response = $client->post(
                'https://payment-stage.ecpay.com.tw/SP/CreateTrade',
                // 'https://payment.ecpay.com.tw/SP/CreateTrade',
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
                        'CheckMacValue' => $CheckMacValue,
                        'EncryptType' => $EncryptType
                    ]
                ]
            );

            $body = $response->getBody();
            $phpBody = json_decode($body);
           
            if ($phpBody->{'RtnCode'} == 1) {
                $SPToken = $phpBody->{'SPToken'};
                $bill = new Bill;
                $bill->user_id = Auth::user()->id;
                $bill->bill_id = $MerchantTradeNo;
                $bill->user_name = Auth::user()->name;
                $bill->item = json_encode($kart);
                $bill->price = $total;
                $bill->SPToken = $SPToken;//SPToken
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
                $bill->ship_three_name = $request->ship_three_name ;
                $bill->ship_three_id = $request->ship_three_id ;
                $bill->ship_three_company = $request->ship_three_company ;
                $bill->ship_memo = $request->ship_memo ;
                $bill->pay_by = 'ATM';
                $bill->save();
            }else{
                print_r(json_decode((string) $body));
            }    
                break;

            case 'credit':                             //  Pay By Credit !!!
                
                $bill = new Bill;
                $bill->user_id = Auth::user()->id;
                $bill->bill_id = $MerchantTradeNo;
                $bill->user_name = Auth::user()->name;
                $bill->item = json_encode($kart);
                $bill->price = $total;
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
                $bill->ship_three_name = $request->ship_three_name;
                $bill->ship_three_id = $request->ship_three_id;
                $bill->ship_three_company = $request->ship_three_company;
                $bill->ship_memo = $request->ship_memo;
                $bill->pay_by = 'CREDIT';
                $bill->save();


                break;
            case 'cod':                             //  Pay By Cod !!!
                
                $bill = new Bill;
                $bill->user_id = Auth::user()->id;
                $bill->bill_id = $MerchantTradeNo;
                $bill->user_name = Auth::user()->name;
                $bill->item = json_encode($kart);
                $bill->price = $total;
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
                $bill->ship_three_name = $request->ship_three_name;
                $bill->ship_three_id = $request->ship_three_id;
                $bill->ship_three_company = $request->ship_three_company;
                $bill->ship_memo = $request->ship_memo;
                $bill->pay_by = '貨到付款';
                $bill->save();

                $user = User::find(Auth::user()->id);
                $user->bonus = $user->bonus+10;
                $user->save();

                $i = 0;
                $itemArray = [];
                foreach($kart as $item)
                {
                    $product = Products::where('slug', $item['slug'])->firstOrFail();   
                    $itemArray[$i] = [
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $item['quantity'],
                    ];
                    $i++;
                }
                $data = array(
                    'user_name'=>Auth::user()->name,
                    'ship_gender'=>$request->ship_gender,
                    'ship_name'=>$request->ship_name,
                    'ship_phone'=>$request->ship_phone,
                    'ship_county'=>$request->ship_county,
                    'ship_district'=>$request->ship_district,
                    'ship_address'=>$request->ship_address,
                    'email' => $request->ship_email,
                    'items' => $itemArray,
                    'bill_id' =>$MerchantTradeNo,
                    'price' => $total,
                    'pay_by'=>'貨到付款',
                );
                Mail::send('emails.cod',$data,function($message) use ($data){
                    $message->from('beta0221@gmail.com','金園排骨');
                    $message->to($data['email']);
                    $message->subject('金園排骨-購買確認通知');
                });
                break;
            }

        Kart::where('user_id',Auth::user()->id)->delete();

        Session::flash('success','訂單已成功送出');
        return redirect()->route('bill.show', $MerchantTradeNo);
    }

    public function billPaied(Request $request)     // !!! API !!!
    {

        $MerchantID = $request->MerchantID;
        $MerchantTradeNo = $request->MerchantTradeNo;
        $StoreID = $request->StoreID;
        $RtnCode = $request->RtnCode;
        $RtnMsg = $request->RtnMsg;
        $TradeNo = $request->TradeNo;
        $TradeAmt = $request->TradeAmt;
        $PaymentDate = $request->PaymentDate;
        $PaymentType = $request->PaymentType;
        $PaymentTypeChargeFee = $request->PaymentTypeChargeFee;
        $TradeDate = $request->TradeDate;
        $SimulatePaid = $request->SimulatePaid;
        $CustomField1 = $request->CustomField1;
        $CustomField2 = $request->CustomField2;
        $CustomField3 = $request->CustomField3;
        $CustomField4 = $request->CustomField4;
        
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
        'SimulatePaid='.$SimulatePaid;

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
            $the->allReturn = $allReturn;
            $the->save();

            $user = User::where('name',$the->user_name);
            $user->bonus = $user->bonus+10;
            $user->save();
        }
        return('1|OK');
    }

    public function creditPaied(Request $request)
    {
        
        if ($request->status == 0) {
            $the = Bill::where('bill_id',$request->lidm)->firstOrFail();
            $the->status = 1;
            $allReturn = 
            'status='.$request->status.'|'.
            'errcode='.$request->errcode.'|'.
            'authCode='.$request->authCode.'|'.
            'authAmt='.$request->authAmt.'|'.
            'lidm='.$request->lidm.'|'.
            'xid='.$request->xid.'|'.
            'merID='.$request->merID.'|'.
            'Last4digitPAN='.$request->Last4digitPAN.'|'.
            'errDesc='.$request->errDesc.'|'.
            'checkValue='.$request->checkValue;
            $the->allReturn = $allReturn;
            $the->save();

            return redirect()->route('bill.show', $request->lidm);
        }else{
            return redirect()->route('bill.show', $request->lidm);
        }
        
    }                                              // !!! API !!!

    public function checkBill($id)
    {
        //test
        $HashKey = '5294y06JbISpM5x9';
        $HashIV = 'v77hoKGq4kWxNNIS';
        $MerchantID = '2000132';
        //kingPork
        // $HashKey = '6HWkOeX5RsDZnDFn';
        // $HashIV = 'Zfo3Ml2OQXRmnjha';
        // $MerchantID = '1044372';
        $MerchantTradeNo = $id;
        $PlatformID = '';
        $TimeStamp = time();
        $all = 'HashKey=' . $HashKey . '&' .
                'MerchantID=' . $MerchantID . '&' .
                'MerchantTradeNo=' . $MerchantTradeNo . '&' .
                'PlatformID=' . '' . '&' .
                'TimeStamp=' . $TimeStamp . '&' .
                'HashIV='.$HashIV;

        $CheckMacValue = hash('sha256', strtolower(urlencode($all)));
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            'https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/V5',
            // 'https://payment.ecpay.com.tw/Cashier/QueryTradeInfo/V5',
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
        $bill->status = 's';
        $bill->save();
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
        $data = array(
            'user_name'=>Auth::user()->name,
            'ship_gender'=>$bill->ship_gender,
            'ship_name'=>$bill->ship_name,
            'ship_phone'=>$bill->ship_phone,
            'ship_county'=>$bill->ship_county,
            'ship_district'=>$bill->ship_district,
            'ship_address'=>$bill->ship_address,
            'email' => $bill->ship_email,
            'items' => $itemArray,
            'bill_id' =>$bill->bill_id,
            'price' => $bill->price,
            'pay_by'=>'ATM轉帳繳費',
            'TradeDate'=>$request->TradeDate,
            'BankCode'=>$request->BankCode,
            'vAccount'=>$request->vAccount,
            'ExpireDate'=>$request->ExpireDate,
        );
        Mail::send('emails.atm',$data,function($message) use ($data){
            $message->from('beta0221@gmail.com','金園排骨');
            $message->to($data['email']);
            $message->subject('金園排骨-購買確認通知');
        });
        return response()->json('s');
    }
    public function sendMailC(Request $request)
    {

        $bill = Bill::where('bill_id',$request->bill_id)->firstOrFail();
        if ($bill->SimulatePaid != 1) {
            $bill->SimulatePaid = 1;
            $bill->save();

            $user = User::find(Auth::user()->id);
            $user->bonus = $user->bonus+10;
            $user->save();

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
            $data = array(
                'user_name'=>Auth::user()->name,
                'ship_gender'=>$bill->ship_gender,
                'ship_name'=>$bill->ship_name,
                'ship_phone'=>$bill->ship_phone,
                'ship_county'=>$bill->ship_county,
                'ship_district'=>$bill->ship_district,
                'ship_address'=>$bill->ship_address,
                'email' => $bill->ship_email,
                'items' => $itemArray,
                'bill_id' =>$bill->bill_id,
                'price' => $bill->price,
                'pay_by'=>'信用卡繳費',
            );
            Mail::send('emails.cod',$data,function($message) use ($data){
                $message->from('beta0221@gmail.com','金園排骨');
                $message->to($data['email']);
                $message->subject('金園排骨-購買確認通知');
            });
            return response()->json('s');
        }else{
            return response()->json('1');
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
            'price' => $bill->price,
            'itemArray' => $itemArray,
            'SPToken'=> $bill->SPToken,
            'pay_by'=>$bill->pay_by,
            'status'=>$bill->status,
        ];

        if ($bill->pay_by == 'CREDIT') {
            $HV1 = md5('rhRwy1KRNsQbjgXR'.'|'.$bill->bill_id);
            $V2 = md5($HV1 . "|" . "008786350353296" . "|" . "77543256" . "|" . $bill->price);
            $checkValue = substr($V2,16,32);
            return view('bill.payBill', ['finalBill'=>$finalBill,'checkValue'=>$checkValue]);
        }else{
            return view('bill.payBill', ['finalBill'=>$finalBill]);  
        }
        
    }

    public function findMemory()
    {

        $bill = Bill::where('user_id',Auth::user()->id)->orderBy('id','desc')->first();
        
        if($bill){
            return response()->json([
                'ship_name' => $bill->ship_name,
                'ship_gender' => $bill->ship_gender,
                'ship_phone' => $bill->ship_phone,
                'ship_county' => $bill->ship_county,
                'ship_district' => $bill->ship_district,
                'ship_address' => $bill->ship_address,
                'ship_email' => $bill->ship_email,
                'ship_receipt' => $bill->ship_receipt,
                'ship_three_name' => $bill->ship_three_name,
                'ship_three_id' => $bill->ship_three_id,
                'ship_three_company' => $bill->ship_three_company,
            ]);
        }else{
            return response()->json('0');
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
        $bill = Bill::where('bill_id',$id)->firstOrFail();
        if ($bill->status == '0') {
            $delete = Bill::where('bill_id',$id)->delete();
            if($delete){
                return response()->json('1');
            }else{
                return response()->json('0');
            }
        }else{
            return response()->json('s');
        }
        
        
    }
}
