<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Banner;
use App\Contact;
use Session;
use Mail;

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

    public function kingblog(){
        return view('admin.kingblog');
        // return('hello');
    }

    public function skyScanner(Request $request){
        $data=[];
        Mail::send('emails.sky',$data,function($message){
            $message->from('kingpork80390254@gmail.com','便宜機票通知');
            $message->to('beta0221@gmail.com');
            $message->subject('便宜機票通知');
        });
        return('1');
    }

}
