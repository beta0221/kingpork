<?php
if (isset($_GET['price'])){
	$price = $_GET["price"];	
}
else{
	$price = 0;
}

if(isset($_GET['bill_id'])){
	$bill_id = $_GET["bill_id"];	
}else{
	$bill_id = '000';
}


echo $price;
echo "";
echo $bill_id;

?>