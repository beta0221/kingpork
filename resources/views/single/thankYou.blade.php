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
	</style>
</head>
<body>
	<div class="U-logo">
		<img src="{{asset('images/logo.png')}}" alt="金園排骨">
	</div>

	<div class="U-title">
		感謝您的購買~	
	</div>
	
	<div class="U-img">
		<img src="{{asset('images/thankYou.png')}}">	
	</div>
	
	<div class="U-text">
		<font>我們衷心感謝您購買我們的產品，您將會收到一封電子確認信，內含您的購買明細。<br>若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。</font>	
	</div>
	
	<div class="billTable">
		<table style="width: 100%">	

			<tr class="product-title-TR">
				<th>訂單編號</th>	
				<th>
					<table style="width: 100%;">
						<tr>
							<td class="TNT1">產品</td>
							<td class="TNT2">價格</td>
							<td class="TNT3">數量</td>
						</tr>
					</table>
				</th>
				<th>紅利折扣</th>
				<th>總金額</th>
			</tr>
			<tr>
				<td class="TDdate">{{$finalBill['bill_id']}}</td>
				<td class="TDproduct">
					<table style="width: 100%;">
						
						@foreach($finalBill['itemArray'] as $item)
							<tr class="product-TR">
								<td class="TNT1">{{$item['name']}}</td>
								<td class="TNT2">{{$item['price']}}</td>
								<td class="TNT3">{{$item['quantity']}}</td>
							</tr>
						@endforeach
						
					</table>
				</td>
				<td>{{$finalBill['bonus_use']}}</td>
				<td class="TDtotal">{{$finalBill['price']}}</td>
			</tr>
		</table>
	</div>

	<div class="goTo">
		<a href="/buynow/1" style="" class="payByBtn btn btn-success">繼續購物</a>
	</div>

</body>
</html>