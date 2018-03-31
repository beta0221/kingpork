
@include('ECPay.ECPay')


<?php
/**
*   ATM產生訂單範例
*/
$price = Request::get('price');
$bill_id = Request::get('bill_id');
	// $price = $_GET['price'];
	// $bill_id = $_GET['bill_id'];
    
    try {
        
    	$obj = new ECPay_AllInOne();
   
        //服務參數
        $obj->ServiceURL  = "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5";   //服務位置
        $obj->HashKey     = '5294y06JbISpM5x9' ;                                           //測試用Hashkey，請自行帶入ECPay提供的HashKey
        $obj->HashIV      = 'v77hoKGq4kWxNNIS' ;                                           //測試用HashIV，請自行帶入ECPay提供的HashIV
        $obj->MerchantID  = '2000132';                                                     //測試用MerchantID，請自行帶入ECPay提供的MerchantID
        $obj->EncryptType = '1';                                                           //CheckMacValue加密類型，請固定填入1，使用SHA256加密
        //基本參數(請依系統規劃自行調整)
        // $MerchantTradeNo = "Test".time() ;
        $obj->Send['ReturnURL']         = "http://www.ecpay.com.tw/receive.php" ;    //付款完成通知回傳的網址
        $obj->Send['MerchantTradeNo']   = $bill_id;                          //訂單編號
        $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');               //交易時間
        $obj->Send['TotalAmount']       = $price;                            //交易金額
        $obj->Send['TradeDesc']         = "ECPay-ATM" ;                      //交易描述
        $obj->Send['ChoosePayment']     = ECPay_PaymentMethod::ATM ;         //付款方式:ATM
        //訂單的商品資料
        array_push($obj->Send['Items'], 
        	array(
        		'Name' => 'test',
        		'Price' => (int)$price,
                'Currency' => "元",
                'Quantity' => (int) "0",
                'URL' => "dedwed"
            ));
        //ATM 延伸參數(可依系統需求選擇是否代入)
        $obj->SendExtend['ExpireDate'] = 3 ;     //繳費期限 (預設3天，最長60天，最短1天)
        $obj->SendExtend['PaymentInfoURL'] = ""; //伺服器端回傳付款相關資訊。
        
        //產生訂單(auto submit至ECPay)
        $html = $obj->CheckOut();
        echo $html;
    
    } catch (Exception $e) {
    	echo $e->getMessage();
    } 
 
?>