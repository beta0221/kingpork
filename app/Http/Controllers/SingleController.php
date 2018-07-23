<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductCategory;
use App\Bill;
use App\Products;
use App\Kart;
use App\User;
use Session;
use DB;
use Mail;

class SingleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function searchOrder()
    {
        $billNum = isset($_GET['billNum'])?$_GET['billNum']:null;
        $phone = isset($_GET['phone'])?$_GET['phone']:null;

        if ($billNum!=null) {
            $bills = Bill::where('bill_id',$billNum)->get();

        }elseif ($phone!=null) {
            $bills = Bill::where('ship_phone',$phone)->get();
        }

        if ($billNum!=null or $phone!=null) {
            
            if (count($bills) != 0) {

                $j=0;
                $i = 0;
                // $itemArray=[][];
                foreach ($bills as $bill) {
                    $items = json_decode($bill->item,true);

                    
                    
                    foreach($items as $item)
                    {
                        $product = Products::where('slug', $item['slug'])->firstOrFail();   
                        $itemArray[$j][$i] = [
                            'name' => $product->name,
                            'price' => $product->price,
                            'quantity' => $item['quantity'],
                        ];
                        $i++;
                    }
                    $j++;
                }

                return view('single.myorder',['bills'=>$bills,'items'=>$itemArray]);
            }else{
                Session::flash('noResult','很抱歉，找不到這筆訂單');
                return view('single.myorder');
            }

        }

        return view('single.myorder');
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
        $short = '';


        foreach($cc as $c){
            $short = $short.$c->short.'*'.$quantityArray[$n].';';
            $total = $total + ($c->price * $quantityArray[$n]);
            $n++;
        }

        if (!in_array('99999',$itemArray) AND $total <= 499) {
            return('錯誤：請檢查網頁JAVASCRIPT是否正常運作');
        }

        $kart = [];
        for ($i=0; $i < count($itemArray); $i++) { 
            $kart[$i] = [
                'slug' => $itemArray[$i],
                'quantity' => $quantityArray[$i],
            ];
        }

        date_default_timezone_set('Asia/Taipei');

        $MerchantTradeNo = time() . rand(10,99);//先給訂單編號

//---------------------------------------------------------------------

        if ($request->ship_pay_by == 'cod') {
            

            $bill = new Bill;
            // $bill->user_id = null;
            $bill->bill_id = $MerchantTradeNo;
            $bill->user_name = $request->ship_name;
            $bill->item = json_encode($kart);
            $bill->bonus_use = 0;
            $bill->price = $total;
            $bill->ship_name = $request->ship_name;
            $bill->ship_gender = $request->ship_gender;
            $bill->ship_phone = $request->ship_phone;
            $bill->ship_county = $request->ship_county;
            $bill->ship_district = $request->ship_district;
            $bill->ship_address = $request->ship_address;
            $bill->ship_email = $request->ship_email;
            $bill->ship_arrive = 'no';
            $bill->ship_time = 'no';
            $bill->ship_receipt = '2';
            $bill->ship_memo = $request->ship_memo;
            $bill->pay_by = '貨到付款';
            $bill->save();


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
                'user_name'=>$request->ship_name,
                'ship_gender'=>$request->ship_gender,
                'ship_name'=>$request->ship_name,
                'ship_phone'=>$request->ship_phone,
                'ship_county'=>$request->ship_county,
                'ship_district'=>$request->ship_district,
                'ship_address'=>$request->ship_address,
                'email' => $request->ship_email,
                'items' => $itemArray,
                'bill_id' =>$MerchantTradeNo,
                'bonus_use'=>0,
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

        
        Session::flash('success','訂單已成功送出');
        
        return redirect()->route('thankYou', $MerchantTradeNo);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $productCategory = ProductCategory::find($id);
        $max = $productCategory->products->max('price');
        $min = $productCategory->products->min('price');
        $count = Bill::count();
        $target = 370;

        date_default_timezone_set('Asia/Taipei');
        $d = date('d');
        $H = date('H');
        $i = date('i');
        $s = date('s');
        $from_d = 31 - $d;
        $from_H = 24 - $H + $from_d * 24;
        $from_i = 60 - $i;
        $from_s = 60 - $s;
        $countDown=[
            'from_H'=>$from_H,
            'from_i'=>$from_i,
            'from_s'=>$from_s,
        ];

        return view('single.index',['productCategory'=>$productCategory,'max'=>$max,'min'=>$min,'count'=>$count,'target'=>$target,'countDown'=>$countDown]);
    }

    public function showToBuy($id)
    {
        $productCategory = ProductCategory::find($id);
        return view('single.buy',['productCategory'=>$productCategory]);   
    }

    public function thankYou($id)
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

        
        return view('single.thankYou', ['finalBill'=>$finalBill]); 
        // return view('single.thankYou');
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
