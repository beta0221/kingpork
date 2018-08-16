<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-121883818-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-121883818-1');
</script>
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
			height: 120px;
			background-color: rgba(56,6,6,0.9);
		}
		.U-logo img{
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translate(-50%,-50%);
			height: 60%;
		}
		.U-title{
			height: 100px;
			line-height: 100px;
			font-size: 40px;
			font-weight: 500;
		}
		.U-img{
			height: 80px;		
		}
		.U-img img{
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translate(-50%,-50%);
			height: 80%;
		}
		.U-text{
			height: 120px;
			padding: 18px 10px 0 10px;
			font-size: 16px;
		}
		.billTable{
			/*height: 120px;*/
		}
		.billTable table{
			background-color: #c8c8c8;
			border-collapse: collapse;
		}
		.product-title-TR{
			border-bottom: 0.5pt solid rgba(0,0,0,0.5);
		}
		.goTo{
			height: 80px;
		}
		.goTo a{
			line-height: 80px;
			text-decoration: none;
			color: #fff;
			border-radius: 3px;
			background-color: #31A857;
			padding:4px 8px;
		}
		.footer{
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
	</style>
</head>
<body>
	<div class="background"></div>
	<div class="U-logo">
		<img src="{{asset('images/logo.png')}}" alt="金園排骨">
	</div>

	<div class="U-title">
		<span>感謝您的購買~</span>
	</div>
	
	<div class="U-img">
		<img src="{{asset('images/thankYou.png')}}">	
	</div>
	
	<div class="U-text">
		<font>我們衷心感謝您購買我們的產品。<br>若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。</font>	
	</div>
	
	<div class="billTable">
		<table class="outter-table" style="width: 100%">	

			<tr class="product-title-TR">
				<th width="30%">訂單編號</th>	
				<th>
					<table width="100%" style="width: 100%;">
						<tr>
							<td width="60%" class="TNT1">產品</td>
							<td width="20%" class="TNT2">價格</td>
							<td width="20%" class="TNT3">數量</td>
						</tr>
					</table>
				</th>
				<th width="20%">總金額</th>
			</tr>
			<tr>
				<td class="TDdate">{{$finalBill['bill_id']}}</td>
				<td class="TDproduct">
					<table style="width: 100%;">
						
						@foreach($finalBill['itemArray'] as $item)
							<tr class="product-TR">
								<td width="60%" class="TNT1">{{$item['name']}}</td>
								<td width="20%" class="TNT2">{{$item['price']}}</td>
								<td width="20%" class="TNT3">{{$item['quantity']}}</td>
							</tr>
						@endforeach
						
					</table>
				</td>
				<td class="TDtotal">{{$finalBill['price']}}</td>
			</tr>
		</table>
	</div>

	<div class="goTo">
		<a href="/buynow/4" class="">繼續購物</a>
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
			<a href="#"><img src="{{asset('images/line.png')}}"></a>
			<a href="https://www.facebook.com/KINGPORK/" target="_blank"><img src="{{asset('images/facebook.png')}}" ></a>
		</div>
	</div>
</body>
</html>