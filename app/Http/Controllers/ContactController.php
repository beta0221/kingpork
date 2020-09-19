<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use Mail;

class ContactController extends Controller
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
    public function index()
    {
        $contacts = Contact::take(20)->orderBy('id')->get();
        return view('contact.index',['contacts'=>$contacts]);
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
        $email = Contact::find($id)->email;
        $dialogue = Contact::where('email',$email)->get();
        return response()->json($dialogue);
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

        $dialogue = Contact::find($id);
        if ($dialogue->response==null) {
            
            try {
                $mailData = [
                    'name'=>$dialogue->name,
                    'email'=>$dialogue->email,
                    'title'=>$dialogue->title,
                    'mailMessage'=>$dialogue->message,
                    'response'=>$request->text
                ];
                //寄送信件
                Mail::send('emails.contact',$mailData,function($message)use($mailData){
                    $message->from('kingpork80390254@gmail.com','金園排骨');
                    $message->to($mailData['email']);
                    $message->subject('金園排骨-客服回覆');
                });
            } catch (Exception $e) {
                return response()->json($e);
            }


            date_default_timezone_set('Asia/Taipei');
            $dialogue->response = $request->text;
            $dialogue->response_at = date('Y\/m\/d H:i:s');
            $dialogue->save();



        }else{
            return response()->json('錯誤：重複回覆');            
        }
        

        return response()->json('傳送成功');
        
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
