<?php




$input = "HashKey=5294y06JbISpM5x9&ChoosePayment=ALL&EncryptType=1&ItemName=商品名稱1#商品名稱2&MerchantID=2000132&MerchantTradeDate=2018/04/01 15:40:18&MerchantTradeNo=aaaaa11111ccccc&PaymentType=aio&ReturnURL=http://localhost:8000/receive.php&TotalAmount=1000&TradeDesc=ecpay商城購物&HashIV=v77hoKGq4kWxNNIS";




echo hash('sha256', strtolower(urlencode($input)));












?>
<html>
	<body>
		
	</body>


	<script src="https://payment-stage.ecpay.com.tw/Scripts/SP/ECPayPayment_1.0.0.js"
	data-MerchantID="2000132"
	data-SPToken="56EDD89CF9624C7688D3F3F0444017E9 "
	data-PaymentType="CREDIT "
	data-PaymentName="信用卡"
	data-CustomerBtn="0" >
		
	</script> 
</html>