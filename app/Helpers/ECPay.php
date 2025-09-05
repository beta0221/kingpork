<?php
namespace App\Helpers;

use App\Bill;
// use App\Payment;
use App\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ECPay{

    /** 訂單 */
    private $bill;
    /** 取得token路徑 */
    private $endpoint_GetTokenbyTrade = "https://ecpg-stage.ecpay.com.tw/Merchant/GetTokenbyTrade";
    /** 建立交易路徑 */
    private $endpoint_CreatePayment = "https://ecpg-stage.ecpay.com.tw/Merchant/CreatePayment";
    /** 特店編號 */
    private $MerchantID;
    /**Hashkey */
    private $HashKey;
    /**HashIV */
    private $HashIV;
    /** 串接文件版號 */
    private $Revision = "1.0.0";
    /** 是否使用記憶卡號 0否 1是 */
    private $RememberCard = 1;
    /** 畫面的呈現方式 */
    private $PaymentUIType = 2;
    /** 欲使用的付款方式 1.信用卡付清 3.ATM */
    private $ChoosePaymentList = "1,3";

    //OrderInfo

    /** 特店交易編號 */
    private $MerchantTradeNo;
    /** 交易時間 (yyyy/MM/dd HH:mm:ss) */
    private $MerchantTradeDate;
    /** 交易金額 */
    private $TotalAmount;
    /** 付款回傳結果URL（POST） */
    private $ReturnURL = '';
    /** 交易描述 */
    private $TradeDesc = 'test';
    /** 商品名稱（以#分隔） */
    private $ItemName = '';

    //CardInfo

    /** 3D驗證回傳付款結果URL（POST） */
    private $OrderResultURL;

    //ATMInfo

    /** 允許繳費有效天 */
    private $ExpireDate = 3;

    //ConsumerInfo

    /** 消費者會員編號（當[RememberCard] = 1，此欄位必填） */
    private $MerchantMemberID;
    /** 信用卡持卡人電子信箱 */
    private $Email;
    /** 信用卡持卡人電話 */
    private $Phone;
    /** 信用卡持卡人姓名 */
    private $Name;
    /** 國別碼 */
    private $CountryCode;


    const PAYMENT_INFO_CARD = 'CardInfo';
    const PAYMENT_INFO_ATM = 'ATMInfo';
    
    /** 錯誤訊息 */
    public $errorMsg = "系統錯誤";

    /**
     * 建構子
     * @param Bill $order
     * @return void
     */
    public function __construct(Bill $bill)
    {

        $this->bill = $bill;
        if(config('app.env') == "production"){
            $this->endpoint_GetTokenbyTrade = "https://ecpg.ecpay.com.tw/Merchant/GetTokenbyTrade";
            $this->endpoint_CreatePayment = "https://ecpg.ecpay.com.tw/Merchant/CreatePayment";
        }
        $this->MerchantID = config('ecpay.MerchantId');
        $this->HashKey = config('ecpay.HashKey');
        $this->HashIV = config('ecpay.HashIV');
        $this->MerchantTradeNo = $bill->bill_id;
        $this->MerchantTradeDate = $bill->created_at->format("Y/m/d H:m:s");
        $this->TotalAmount = (int)$bill->price;
        $this->ReturnURL = route('ecpay_ReturnURL',['bill_id'=>$bill->bill_id]);
        $this->OrderResultURL = route('ecpay_OrderResultURL',['bill_id'=>$bill->bill_id]);
        $this->MerchantMemberID = 'USER_' . $bill->user_id;

        switch ($bill->pay_by) {
            case 'CREDIT':
                $this->ChoosePaymentList = "1";
                break;
            case 'ATM':
                $this->ChoosePaymentList = "3";
                break;
            default:
                break;
        }

        if ($bill->user_id) {
            $this->Email = $bill->ship_email;
            $this->Phone = (strpos($bill->ship_phone, '09') === 0) ? $bill->ship_phone : null;
            $this->Name = $bill->ship_name;
            $this->CountryCode = 'TW';
        }

    }

    /**
     * 組合ItemName
     * @return void
     * */
    private function setItemName(){
        $products = $this->bill->products();
        foreach ($products as $product) {
            if(!empty($this->ItemName)){ $this->ItemName .= "#"; }
            $this->ItemName .= $product->name;
        }
    }


    /**
     * 加密
     * @param array $array
     * @return string
     */
    private function array2EncryptedString(array $array){
        $string = json_encode($array);
        $string = urlencode($string);
        $string = openssl_encrypt($string,"AES-128-CBC",$this->HashKey,0,$this->HashIV);
        return $string;
    }

    /**
     * 解密
     * @param string $string
     * @return array
     */
    function string2DecryptedArray(string $string){
        $string = openssl_decrypt($string,"AES-128-CBC",$this->HashKey,0,$this->HashIV);
        $array = json_decode(urldecode($string),true);
        return $array;
    }

    /**
     * 取得請求的body templet
     * @return array
     */
    private function getBody(){
        $body = [];
        $body['MerchantID'] = $this->MerchantID;
        $body['RqHeader'] = [
            'Timestamp'=>time(),
            'Revision'=>$this->Revision,
        ];
        return $body;
    }

    /**
     * 組合CreatePayment請求body 
     * @param string $Paytoken
     * @return string
     */
    private function getBody_CreatePayment(string $Paytoken){
        $body = $this->getBody();
        $Data = [
            "MerchantID" => $this->MerchantID,
            "PayToken" => $Paytoken,
            "MerchantTradeNo" => $this->MerchantTradeNo,
        ];
        $Data = $this->array2EncryptedString($Data);
        $body['Data'] = $Data;

        return json_encode($body);
    }

    /**
     * 組合TradeToken請求body 
     * @return string
     */
    private function getBody_TradeToken(){
        $body = $this->getBody();
        $Data = [
            'MerchantID' => $this->MerchantID,
            'RememberCard' => $this->RememberCard,
            'PaymentUIType' => $this->PaymentUIType,
            'ChoosePaymentList' => $this->ChoosePaymentList,
            'OrderInfo' => [
                "MerchantTradeNo" => $this->MerchantTradeNo,
                "MerchantTradeDate" => $this->MerchantTradeDate,
                "TotalAmount" => $this->TotalAmount,
                "ReturnURL" => $this->ReturnURL,
                'TradeDesc' => $this->TradeDesc,
                'ItemName' => $this->ItemName
            ],
            'CardInfo'=> [
                'OrderResultURL' => $this->OrderResultURL,
            ],
            'ATMInfo' => [
                'ExpireDate' => $this->ExpireDate
            ],
            'ConsumerInfo' => [
                "MerchantMemberID"=>$this->MerchantMemberID,
                "Email"=>$this->Email,
                "Phone"=>$this->Phone,
                "Name"=>$this->Name,
                "CountryCode"=>$this->CountryCode,
            ]
        ];
        $Data = $this->array2EncryptedString($Data);
        $body['Data'] = $Data;

        return json_encode($body);
    }

    /**
     * curl 請求
     * @param string $url
     * @param string $body
     * @return resource
     */
    private function getCurlRequest(string $url,string $body){
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ],
        ]);
        return $curl;
    }

    /** 
     * 取得付款token
     * @return string 
     */
    public function getToken(){
        $this->setItemName();
        $curl = $this->getCurlRequest($this->endpoint_GetTokenbyTrade,$this->getBody_TradeToken());
        $res = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            Log::info($err);
            $this->errorMsg = $err;
            return null;
        }
        Log::info('** Debug getToken **');
        Log::info($res);
        $res = json_decode($res,true);
        
        if(!isset($res['Data'])){ return null; }
        $Data = $this->string2DecryptedArray($res['Data']);
        if(!isset($Data['RtnCode']) || !isset($Data['Token'])){ return null; }
        
        if($Data['RtnCode'] != 1){ 
            $this->errorMsg = $Data['RtnMsg'];
            return null; 
        }
        return $Data['Token'];
        
    }

    /**
     * 進行付款
     * @param string $PayToken
     * @return string
     */
    public function createPayment(string $PayToken){
        $curl = $this->getCurlRequest($this->endpoint_CreatePayment,$this->getBody_CreatePayment($PayToken));
        $res = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            Log::info($err);
            return null;
        }
        
        Log::info('** Debug createPayment **');
        Log::info($res);
        $res = json_decode($res,true);
        if(!isset($res['TransCode']) || !isset($res['TransMsg']) || !isset($res['Data'])){ return null; }
        PaymentLog::insert_row(
            $this->bill->id,
            PaymentLog::TYPE_CREATE_PAYMENT,
            $res['TransCode'],
            $res['TransMsg'],
            $res['Data']
        );
        if($res['TransCode'] != 1){ return null; }
        $Data = $this->string2DecryptedArray($res['Data']);

        Log::info("createPayment:" . $this->bill->bill_id);
        Log::info(json_encode($Data));

        //如果有3D驗證url
        if(!is_null($Data['ThreeDInfo']['ThreeDURL'])){
            return $Data['ThreeDInfo']['ThreeDURL'];
        }        

        if(!isset($Data['OrderInfo']['PaymentType'])){ return null; }
        switch ($Data['OrderInfo']['PaymentType']) {
            case 'Credit':
                return route('billThankyou',['bill_id'=>$this->bill->bill_id]);    
                break;
            case 'ATM':
                return route('billDetail',['bill_id'=>$this->bill->bill_id]);    
                break;
            default:
                return null;
                break;
        }
    }

    /** 
     * 處理付款回傳請求
     * @param Request $request
     * @return bool 是否成功付款
     */
    public function handlePayRequest(Request $request){
        $res = json_decode($request->getContent(),true);
        if(!isset($res['TransCode']) || !isset($res['TransMsg']) || !isset($res['Data'])){ return false; }
        PaymentLog::insert_row(
            $this->bill->id,
            PaymentLog::TYPE_PAY_REQUEST,
            $res['TransCode'],
            $res['TransMsg'],
            $res['Data']
        );
        
        $data = $this->string2DecryptedArray($res['Data']);
        if(!isset($data['RtnCode'])){ return false; }

        if($data['RtnCode'] == 1){
            return true;
        }

        return false;
    }

    /**
     * 取得交易卡片資訊
     * @return array
     */
    public function getCardInfo(Request $request) {
        $res = json_decode($request->getContent(),true);

        if(!isset($res["Data"])) { return null; }
        $data = $this->string2DecryptedArray($res['Data']);

        if (!isset($data["CardInfo"])) { return null; }
        return $data["CardInfo"];
    }

    /**
     * 取得綠界金流交易資訊
     * @return array
     */
    public function getPaymentInfo(){
        if(!$log = $this->bill->paymentLogs()->where('type',PaymentLog::TYPE_CREATE_PAYMENT)->first()){ return null; }
        $data = $this->string2DecryptedArray($log->Data);
        return $data;
    }
    /**
     * 取得綠界付款資訊
     * @return array
     */
    public function getPayRequestInfo() {
        if(!$log = $this->bill->paymentLogs()->where('type',PaymentLog::TYPE_PAY_REQUEST)->first()){ return null; }
        $data = $this->string2DecryptedArray($log->Data);
        return $data;
    }


    /** 
     * 前端JS SDK 路徑 
     * @return string
     * */
    public function getEcpaySDKUrl(){
        if(config('app.env') == "production"){
            return "https://ecpg.ecpay.com.tw/Scripts/sdk-1.0.0.js?t=20210121100116";
        }
        return "https://ecpg-stage.ecpay.com.tw/Scripts/sdk-1.0.0.js?t=20210121100116";
    }

}