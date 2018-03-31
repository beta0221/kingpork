<?php

$price = Request::get('price');
$bill_id = Request::get('bill_id');

if(isset($_GET['price'])){
	$price = $_GET["price"];	
}
if(isset($_GET['bill_id'])){
	$bill_id = $_GET["bill_id"];	
}


echo $price;
echo "";
echo $bill_id;

?>