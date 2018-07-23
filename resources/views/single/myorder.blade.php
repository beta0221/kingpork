<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>【金園排骨】西門町傳承一甲子的古早味</title>
	<style>
		* {
		    font-family: 微软雅黑;
		    margin: 0px;
		    padding: 0px;
		    position: relative;
		    box-sizing: border-box;
		}
		body{
			font-size: 14px;
			margin: 0 auto;
			min-width: 320px;
			max-width: 640px;
			background: #fff;
			text-align: center;
			
		}
		div{
			/*border:1pt solid #000;*/
		}
		.background{
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-size: cover;
			background-position: 0% 100%;
			z-index: -3;
			background-image: url(../images/mainBg.png);
		}
		.U-logo{
			height: 100px;
			background-color: rgba(56,6,6,0.9);
		}
		.U-logo img{
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translate(-50%,-50%);
			height: 60%;
		}
		.title-bar{
			background-color: rgba(255,255,255,0.5);
			text-align:center;
			height: 40px;
		}
		.title-bar span{
			line-height: 40px;
		}
		.title-bar a {
			position: absolute;
			left: 10px;
			text-decoration: none;
			line-height: 40px;
		}
		.footer{
			text-align:center;
			margin-top: 20px;
			background-color: rgba(56,6,6,0.9);
			color: #fff;
		}
		.footer-1,.footer-2,.footer-3,.footer-4{
			height: 56px;
			line-height: 56px;
		}
		.footer-1 img{
			height: 80%;
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translate(-50%,-50%);
		}
		.footer-4 img{
			height: 95%;
		}
		/* -------------- */
		.content{
			min-height: 80vh;
			padding-top: 40px;
		}
		.search-input{
			margin: 20px 0 20px 0;
		}
		.content input{
			border-radius: 3px;
			border:none;
			height: 24px;
			padding: 0 4px;
		}
		.content button{
			color: #fff;
			font-size: 20px;
			width: 80%;
			height: 40px;
			background-color: #31A857;
			border-radius: 3px;
			margin-bottom: 20px;
		}
		.content table{
			background-color: #c8c8c8;
			font-size: 12px;
			border-collapse: collapse; 
		}
		.bill-table{
			margin-top: 20px;
		}
		.bill-tr{
			border-top: 1px solid rgba(0,0,0,0.1);
			height: 56px;
		}
		.bill-title-tr{
			height: 56px;
		}

	</style>
</head>
<body>
	<div class="background"></div>
	<div class="U-logo">
		<img src="{{asset('images/logo.png')}}" alt="金園排骨">
	</div>

	<div class="title-bar">
		<a href="/buynow/1"><span><</span></a><span>訂單查詢</span>
	</div>

	<div class="content">
		<form action="{{URL::current()}}" method="GET">
			<span style="font-size: 20px;">請輸入“訂單編號”或“手機號碼”</span>
			<div class="search-input">
				<span>訂單編號：</span><input type="text" name="billNum" placeholder="擇一查詢" value="{{isset($_GET['billNum'])?$_GET['billNum']:''}}"><br>
				<span>或</span><br>
				<span>手機號碼：</span><input type="text" name="phone" placeholder="擇一查詢" value="{{isset($_GET['phone'])?$_GET['phone']:''}}">
			</div>
			<button>查詢</button>
		</form>
		<hr>

		@if(Session::has('noResult'))
		{{Session('noResult')}}
		@endif


		@if(isset($bills))
			<table class="bill-table" width="100%">
				<tr class="bill-title-tr">
					<td width="20%">日期</td>
					{{-- <td width="20%">訂單編號</td> --}}
					<td width="60%">
						<table width="100%">
							<tr>
								<td width="60%">產品</td>
								<td width="20%">價格</td>
								<td width="20%">數量</td>
							</tr>
						</table>
					</td>
					<td width="10%">總價</td>
					<td width="10%">狀態</td>
				</tr>
				<?php $i=0 ?>
				@foreach($bills as $bill)
				<tr class="bill-tr">
					<td width="20%">{{$bill->created_at}}</td>
					{{-- <td>{{$bill->bill_id}}</td> --}}
					<td width="60%">
						<table width="100%">
							@foreach($items[$i] as $item)
							<tr>
								<td width="60%">{{$item['name']}}</td>
								<td width="20%">{{$item['price']}}</td>
								<td width="20%">{{$item['quantity']}}</td>
							</tr>
							@endforeach
						</table>
					</td>
					<td width="10%"><font color="#0275d8">{{$bill->price}}</font></td>
					<td width="10%">
						@if($bill->shipment == 0)
							<font color="gray">-</font>
						@elseif($bill->shipment == 1)
							<font color="#eb9316">準備中</font>
						@elseif($bill->shipment == 2)
							<font color="#5cb85c">已出貨</font>
						@elseif($bill->shipment == 3)
							<font color="green">已收貨</font>
						@endif
					</td>
				</tr>
				<?php $i++ ?>
				@endforeach
			</table>
		@endif
		
		
	</div>

	<div class="connect">
		<font>若您對交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>，或來電客服專線。</font>	
	</div>

	<div class="footer">
		<div class="footer-1">
			<img src="{{asset('images/logo.png')}}" alt="金園排骨">
		</div>
		<div class="footer-2">
			<span>地址:桃園市桃園區大有路59號3樓</span>
		</div>
		<div class="footer-3">
			<span>服務電話:0800-552-999</span>
		</div>
		<div class="footer-4">
			<a href="{{url('about-line')}}"><img src="{{asset('images/line.png')}}"></a>
			<a href="https://www.facebook.com/KINGPORK/" target="_blank"><img src="{{asset('images/facebook.png')}}" ></a>
		</div>
	</div>

</body>
</html>