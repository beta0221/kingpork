<?php 
header("Content-type: text/x-csv");
header("Content-Disposition: attachment; filename=output.csv");
$content = "訂單編號,備註,檔次編號,配送時段,收件人,電話,品名,收件地址,出貨日期,到貨日期\n16902390,1,BY123732356F,1,莊惠美 (送貨前請務必與收件人聯繫),0920-330-165,10P+10G,505彰化縣鹿港鎮中山路26號,2018/6/13,2018/6/14";


























$content = mb_convert_encoding($content , "Big5" , "UTF-8");
echo $content;
exit;
?>