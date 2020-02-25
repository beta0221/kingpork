<?php 
$d = date('md');

header("Content-type: text/x-csv");
header("Content-Disposition: attachment; filename=".$d."-shipping.csv");

$orders = $_POST['orders'];
$orders = json_decode($orders);

$content = "訂單日期,訂單編號,備註,代收貨款,配送時段,收件人,電話,品名,收件地址,出貨日期,到貨日期,金額\n";

foreach ($orders as $order) {
	$content = $content . $order."\n";
}

$content = mb_convert_encoding($content , "BIG5" , "auto");
echo $content;
exit;
?>

