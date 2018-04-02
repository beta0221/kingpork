<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\Products;
use Session;
use Illuminate\Support\Facades\Auth;
use DB;
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
                        'total' => $records[$x]->price,         //總價
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
        $bill_id = time();
        $bill = new Bill;
        $bill->user_id = Auth::user()->id;
        $bill->bill_id = $bill_id;
        $bill->user_name = Auth::user()->name;
        $bill->item = json_encode($kart);
        $bill->price = $total;
        $bill->save();

        // DB::table('kart')->where('user_id',Auth::user()->id)->delete();

        Session::flash('success','訂單已成功送出');

        // return redirect()->route('bill.index');
        
        // return redirect()->route('ecomApi',[
        //     'price'=>$total,
        //     'bill_id'=>$bill_id
        // ]);

        $HashKey = '5294y06JbISpM5x9';
        $HashIV = 'v77hoKGq4kWxNNIS';
        $MerchantID = '2000132';

        $MerchantTradeNo = 'kp' . time() ;
        date_default_timezone_set('Asia/Taipei');
        $MerchantTradeDate = date('Y\/m\/d H:i:s');
        $PaymentType = 'aio';
        $TotalAmount = $total;
        $TradeDesc = 'ecpay商城購物';
        $ItemName = '商品名稱1#商品名稱2';
        $ReturnURL = 'http://localhost:8000/receive.php';
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
            
            return view('bill.payBill',['SPToken'=>$SPToken]);

        }else{
            print_r(json_decode((string) $body));
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
