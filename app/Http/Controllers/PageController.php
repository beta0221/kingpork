<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Banner;
use App\Contact;
use App\Product;
use Session;
use Mail;
use Excel;
use App\Products;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin',['only'=>'kingblog']);
    }

    public function getLanding(){

    	$banners = Banner::where('public',1)->get();
        if ($banners == null) {
            return view('pages.landingPage',['banners'=>'default.png']);
        }
	    return view('pages.landingPage',['banners'=>$banners]);
    	
    }

    public function getContact(){
    	return view('pages.contact');
    }

    public function showProductPage(){
    	return view('pages.productPage');
    }

    public function guide(){
        return view('pages.guide');
    }

    public function aboutLine(){
        return view('pages.LinePage');
    }

    public function contactUs(Request $request){

        $this->validate($request,[
            'name'=>'required',
            'email'=>'required|E-mail',
            'title'=>'required',
            'text'=>'required'
        ]);

        //recaptcha
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => '6LfOZnoUAAAAAFNdAX43Z17487emgfmW5r1Rj9CQ',
            'response' => $request->input('g-recaptcha-response')
        );
        $options = array(
            'http' => array (
                'method' => 'POST',
                'header'=>"Content-Type: application/x-www-form-urlencoded",
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $verify = file_get_contents($url, false, $context);
        $captcha_success=json_decode($verify);
        
        if ($captcha_success->success==false) {
            return response("Go away Robot!");
        } else if ($captcha_success->success==true) {
        
            $contact = new Contact;
            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->title = $request->title;
            $contact->message = $request->text;
            $contact->save();

            $data =[
                'name'=>$request->name,
                'email'=>$request->email,
                'title'=>$request->title,
                'text'=>$request->text
            ];
            Mail::send('emails.messageNotification',$data,function($message) use ($data){
                $message->from('kingpork80390254@gmail.com','金園排骨-客服通知');
                $message->to('beta0221@gmail.com');
                $message->subject($data['title']);
            });
            
            Session::flash('success','訊息已成功送出，我們將會儘速回覆您。');
            return view('pages.contact');

        }
        

    }

    public function kingblog(){
        return view('admin.kingblog');
        // return('hello');
    }

    // public function skyScanner(Request $request){
    //     $data=[];
    //     Mail::send('emails.sky',$data,function($message){
    //         $message->from('kingpork80390254@gmail.com','便宜機票通知');
    //         $message->to('beta0221@gmail.com');
    //         $message->subject('便宜機票通知');
    //     });
    //     return('1');
    // }



    public function productFeed(){
        

        $cellData = [
            ['id','title','description','availability','price'],
        ];

        
        $products = Products::all();
        foreach ($products as $row) {
            $newArray = [$row->id,$row->name,$row->short,$row->public,$row->price];
            array_push($cellData,$newArray);
        }

        $title = "product_catalog";

        Excel::create($title, function($excel)use($cellData) {

            $excel->sheet('Sheetname', function($sheet)use($cellData) {

                $sheet->rows($cellData);
            });

        })->download('csv');
    }













    public function getUserExcel(Request $request){

        $this->validate($request,[
            'from'=>'required|integer',
            'to'=>'required|integer',
        ]);

        $data = [
            ['姓名','電郵','手機','加入會員日','購買次數','總購買金額','最後一次購買日期','最後一次購買金額'],
        ];

        $users = DB::table('users')->where('id','>=',$request->from)->where('id','<=',$request->to)->get();

        foreach ($users as $user) {
            
            $name = $user->name;
            $email = $user->email;
            $phone = $user->phone;
            $joinDate = $user->created_at;
            $buySum = DB::table('bills')->where('user_id',$user->id)->count();
            $priceSum = DB::table('bills')->where('user_id',$user->id)->sum('price');
            $lastPurchase = DB::table('bills')->select('created_at')->orderBy('id','desc')->first()->created_at;//take(1)->pluck('created_at');
            $lastPurchasePrice = DB::table('bills')->select('price')->orderBy('id','desc')->first()->price;
            $row = [$name,$email,$phone,$joinDate,$buySum,$priceSum,$lastPurchase,$lastPurchasePrice];
            array_push($data,$row);
        }
        Excel::create("會員$request->from - $request->to", function($excel)use($data) {

            $excel->sheet('Sheetname', function($sheet)use($data) {

                $sheet->rows($data);

            });

        })->download('csv');


        // return response(json_encode($data,true));

        
    }





}

