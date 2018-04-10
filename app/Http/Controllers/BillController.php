<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\Products;
use App\Kart;
use Session;
use DB;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;

// require 'vendor/autoload.php';

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()){

            $records = Bill::all()->where('user_id',Auth::user()->id);
            // ->orderBy('created_at', 'desc')
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
                        'pay_by' => $records[$x]->pay_by,       //付款方式
                        'SPToken' => $records[$x]->SPToken,     //SPToken
                        'created_at' => $records[$x]->created_at, //訂購日期

                    ];
                }
            }

            return view('bill.index',['finalBills'=>$finalBills]);

        }else{

            return redirect('login');

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

        $this->validate($request,[
            'item.*'=>'required',
            'quantity.*'=>'required|integer|min:1',
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

        // [{item:'10001',quantity:'2'},{item:'10002',quantity:'1'},{item:'10003',quantity:'3'}]
        
        $kart = [];
        for ($i=0; $i < count($itemArray); $i++) { 
            
            $kart[$i] = [
                'slug' => $itemArray[$i],
                'quantity' => $quantityArray[$i],
            ];

        }
        
        // return redirect()->route('ecomApi',[
        //     'price'=>$total,
        //     'bill_id'=>$bill_id
        // ]);

        //test
        $HashKey = '5294y06JbISpM5x9';
        $HashIV = 'v77hoKGq4kWxNNIS';
        $MerchantID = '2000132';
        //kingPork
        // $HashKey = '6HWkOeX5RsDZnDFn';
        // $HashIV = 'Zfo3Ml2OQXRmnjha';
        // $MerchantID = '1044372';

        $MerchantTradeNo = 'kp' . time() ;
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
            
            $bill_id = $MerchantTradeNo;
            $bill = new Bill;
            $bill->user_id = Auth::user()->id;
            $bill->bill_id = $bill_id;
            $bill->user_name = Auth::user()->name;
            $bill->item = json_encode($kart);
            $bill->price = $total;
            $bill->SPToken = $SPToken;
            $bill->save();

            DB::table('kart')->where('user_id',Auth::user()->id)->delete();

            Session::flash('success','訂單已成功送出');

            return redirect()->route('bill.show', $bill_id);

        }else{

            print_r(json_decode((string) $body));
        
        }

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
        $CheckMacValue = $request->CheckMacValue;

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
        'CustomField1=' . $CustomField1.'&'.
        'CustomField2=' . $CustomField2.'&'.
        'CustomField3=' . $CustomField3.'&'.
        'CustomField4=' . $CustomField4.'&'.
        'CheckMacValue=' . $CheckMacValue;
        

        if ($RtnCode == 1) {
            $the = Bill::where('bill_id',$MerchantTradeNo)->firstOrFail();
            $the->status = 1;
            $the->pay_by = $PaymentType;
            $the->RtnCode = $RtnCode;
            $the->RtnMsg = $RtnMsg;
            $the->TradeNo = $TradeNo;
            $the->PaymentDate = $PaymentDate;
            $the->PaymentTypeChargeFee = $PaymentTypeChargeFee;
            $the->TradeDate = $TradeDate;
            $the->SimulatePaid = $SimulatePaid;
            // $the->allReturn = $all;
            $the->save();
        }
        

        return('1|OK');

    }                                               // !!! API !!!

    
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
        ];
        
        return view('bill.payBill', ['finalBill'=>$finalBill]);
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
