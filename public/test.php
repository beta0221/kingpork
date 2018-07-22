<html>
<body>
	

<?php

$item_array=['1','2','3','4','5','6']; //假設現在有六個商品（所有批次資料都是以陣列形式存在的）

foreach($item_array as $item){ //foreach($陣列名稱 as $自己取){    裡面這邊只要打” echo $自己取; “ 的話（不含引號）就會把陣列中的所有值循序 echo 出來    }
//下面這邊把php關起來，因為要開始打html
?>

<div>				<!-- html裡面要打php語法來放php中的資料的話就再打一次<?php ?>然後在裡面打php語法 -->
	商品代號： <?php echo $item; ?> <button onclick="show(<?php echo $item; ?>);">連結</button><!-- 我這邊示範的是用js把它alert出來，如果要做超連結也是一樣的概念 -->	
</div>

<!-- 下面這邊的"}"是foreach迴圈關閉的地方 -->
<?php
}
?>

<?php date_default_timezone_set('Asia/Taipei'); ?>
<?php echo time(); ?>
<br>
<?php echo date("m",time()); ?>
<br>
<?php echo date("d"); ?>
<br>
<?php echo date("H"); ?>
<br>
<?php echo date("i"); ?>
<br>
<?php echo date("s"); ?>	
</body>
<script>
	function show(id){
		alert(id);
	}
</script>
</html>