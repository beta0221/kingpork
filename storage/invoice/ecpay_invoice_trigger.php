<?php

if(!isset($argv[1])){
	exit;
}
$data = json_decode($argv[1],true);

try
{

	$sMsg = '' ;
// 1.載入SDK程式
	include_once('Ecpay_Invoice_Env.php') ;
	include_once('Ecpay_Invoice.php') ;
	$ecpay_invoice = new EcpayInvoice ;
	
// 2.寫入基本介接參數
	$ecpay_invoice->Invoice_Method 			= 'INVOICE_TRIGGER' ;
	$ecpay_invoice->Invoice_Url 			= Ecpay_Invoice_Env::Invoice_Url . '/Invoice/TriggerIssue';
	$ecpay_invoice->MerchantID 			= Ecpay_Invoice_Env::MerchantId ;
	$ecpay_invoice->HashKey 			= Ecpay_Invoice_Env::HashKey ;
	$ecpay_invoice->HashIV 				= Ecpay_Invoice_Env::HashIV ;
	
// 3.寫入發票相關資訊
    $ecpay_invoice->Send['Tsr'] = $data['RelateNumber']; 	// 交易單號
    $ecpay_invoice->Send['PayType'] = '2'; 
// 4.送出
	$aReturn_Info = $ecpay_invoice->Check_Out();
	
// 5.返回
	foreach($aReturn_Info as $key => $value)
	{
		$sMsg .=   $key . ' => ' . $value . ' | ' ;
	}
}
catch (Exception $e)
{
	// 例外錯誤處理。
	$sMsg = $e->getMessage();
}

echo $sMsg;