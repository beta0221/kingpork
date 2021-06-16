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
	$ecpay_invoice->Invoice_Method 			= 'INVOICE' ;
	$ecpay_invoice->Invoice_Url 			= Ecpay_Invoice_Env::Invoice_Url . '/Invoice/Issue';
	$ecpay_invoice->MerchantID 			= Ecpay_Invoice_Env::MerchantId ;
	$ecpay_invoice->HashKey 			= Ecpay_Invoice_Env::HashKey ;
	$ecpay_invoice->HashIV 				= Ecpay_Invoice_Env::HashIV ;
	
// 3.寫入發票相關資訊
	$aItems	= array();
	// 商品資訊
	$ecpay_invoice->Send['Items'] = $data['Items'];
	$ecpay_invoice->Send['RelateNumber'] = $data['RelateNumber'] ;
	$ecpay_invoice->Send['CustomerID'] = '' ;
	$ecpay_invoice->Send['CustomerIdentifier'] = '' ;
	$ecpay_invoice->Send['CustomerName'] ='' ;
	$ecpay_invoice->Send['CustomerAddr'] = '' ;
	$ecpay_invoice->Send['CustomerPhone'] = '' ;
	$ecpay_invoice->Send['CustomerEmail'] = $data['CustomerEmail'] ;
	$ecpay_invoice->Send['ClearanceMark'] = '' ;
	$ecpay_invoice->Send['Print'] = '0' ;
	$ecpay_invoice->Send['Donation'] = '0' ;
	$ecpay_invoice->Send['LoveCode'] = '' ;
	$ecpay_invoice->Send['CarruerType'] = '' ;
	$ecpay_invoice->Send['CarruerNum'] = '' ;
	$ecpay_invoice->Send['TaxType'] = 1 ;
	$ecpay_invoice->Send['SalesAmount'] = $data['SalesAmount'] ;
	$ecpay_invoice->Send['InvoiceRemark'] = 'v1.0.190822' ;	
	$ecpay_invoice->Send['InvType'] = '07' ;
	$ecpay_invoice->Send['vat'] = '' ;
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