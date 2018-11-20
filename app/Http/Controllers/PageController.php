<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Banner;
use App\Contact;
use Session;
use Mail;
use App\Libraries\Recaptchalib;

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

        $secret = "6LfOZnoUAAAAAFNdAX43Z17487emgfmW5r1Rj9CQ";
        $response = null;
        //validate from google
        $reCaptcha = new ReCaptcha($secret);

        if ($request->input('g-recaptcha-response')) {
            $response = $reCaptcha->verifyResponse(
                request()->ip(),
                $request->input('g-recaptcha-response')
            );
        }

        // if ($response != null && $response->success){

        // }


        // $this->validate($request,[
        //     'name'=>'required',
        //     'email'=>'required|E-mail',
        //     'title'=>'required',
        //     'text'=>'required'
        // ]);

        // $contact = new Contact;
        // $contact->name = $request->name;
        // $contact->email = $request->email;
        // $contact->title = $request->title;
        // $contact->message = $request->text;
        // $contact->save();

        // $data =[
        //     'name'=>$request->name,
        //     'email'=>$request->email,
        //     'title'=>$request->title,
        //     'text'=>$request->text
        // ];
        // Mail::send('emails.messageNotification',$data,function($message) use ($data){
        //     $message->from('kingpork80390254@gmail.com','金園排骨-客服通知');
        //     $message->to('beta0221@gmail.com');
        //     $message->subject($data['title']);
        // });
        
        // Session::flash('success','訊息已成功送出，我們將會儘速回覆您。');
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

class ReCaptchaResponse
{
    public $success;
    public $errorCodes;
}
class ReCaptcha
{
    private static $_signupUrl = "https://www.google.com/recaptcha/admin";
    private static $_siteVerifyUrl =
        "https://www.google.com/recaptcha/api/siteverify?";
    private $_secret;
    private static $_version = "php_1.0";
    /**
     * Constructor.
     *
     * @param string $secret shared secret between site and ReCAPTCHA server.
     */
    function ReCaptcha($secret)
    {
        if ($secret == null || $secret == "") {
            die("To use reCAPTCHA you must get an API key from <a href='"
                . self::$_signupUrl . "'>" . self::$_signupUrl . "</a>");
        }
        $this->_secret=$secret;
    }
    /**
     * Encodes the given data into a query string format.
     *
     * @param array $data array of string elements to be encoded.
     *
     * @return string - encoded request.
     */
    private function _encodeQS($data)
    {
        $req = "";
        foreach ($data as $key => $value) {
            $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
        }
        // Cut the last '&'
        $req=substr($req, 0, strlen($req)-1);
        return $req;
    }
    /**
     * Submits an HTTP GET to a reCAPTCHA server.
     *
     * @param string $path url path to recaptcha server.
     * @param array  $data array of parameters to be sent.
     *
     * @return array response
     */
    private function _submitHTTPGet($path, $data)
    {
        $req = $this->_encodeQS($data);
        $response = file_get_contents($path . $req);
        return $response;
    }
    /**
     * Calls the reCAPTCHA siteverify API to verify whether the user passes
     * CAPTCHA test.
     *
     * @param string $remoteIp   IP address of end user.
     * @param string $response   response string from recaptcha verification.
     *
     * @return ReCaptchaResponse
     */
    public function verifyResponse($remoteIp, $response)
    {
        // Discard empty solution submissions
        if ($response == null || strlen($response) == 0) {
            $recaptchaResponse = new ReCaptchaResponse();
            $recaptchaResponse->success = false;
            $recaptchaResponse->errorCodes = 'missing-input';
            return $recaptchaResponse;
        }
        $getResponse = $this->_submitHttpGet(
            self::$_siteVerifyUrl,
            array (
                'secret' => $this->_secret,
                'remoteip' => $remoteIp,
                'v' => self::$_version,
                'response' => $response
            )
        );
        $answers = json_decode($getResponse, true);
        Session::flash('success',$answers);
        // $recaptchaResponse = new ReCaptchaResponse();
        // if (trim($answers ['success']) == true) {
        //     $recaptchaResponse->success = true;
        // } else {
        //     $recaptchaResponse->success = false;
        //     $recaptchaResponse->errorCodes = $answers [error-codes];
        // }
        // return $recaptchaResponse;
    }
}
