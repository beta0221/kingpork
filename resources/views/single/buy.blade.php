<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>【金園排骨】西門町傳承一甲子的古早味</title>

	{{Html::style('css/_singleBuy.css')}}
</head>
<body>
	<div class="background"></div>

	{{-- <div onclick="document.getElementById('use-chrome-bg').style.display='none';" id="use-chrome-bg" class="use-chrome-bg">
		<img src="{{asset('images/use_chrome.png')}}">
	</div> --}}

	<div class="U-logo">
		<img src="{{asset('images/logo.png')}}" alt="金園排骨">
	</div>
	<div class="content">
		<div class="title-bar">
			<a href="{{route('menustudy')}}"><span><</span></a><span>確認訂單</span>
		</div>

		<div class="slider" style="background-color: gray">{{-- 1920 x 1080 --}}
			{{-- <video autoplay muted loop class="slider-group" >
  				<source src="{{asset('vedios/head.mp4')}}" type="video/mp4">
  			您的瀏覽器不支援此影片
			</video> --}}
			<img style="position: absolute;top:0;left:0;width:100%" src="/images/moon_fast123.jpg" alt="圖片">
		</div>

		<div class="product-outter">
			<div class="product-title">
				<h1><大標題></h1>
			</div>
			<div class="product-content">
				@foreach($productCategory->products as $index => $product)
				@if($product->public == 1)
				<div class="product-row" price="{{$product->price}}" onclick="product_select('{{$product->slug}}',{{$product->price}});">

					<div class="left-name-box">

						<div class="left-top-box">
							<span>{{$product->name}}</span>
						</div>

						<div class="left-bottom-box">
							{!!($product->discription == null)?'':'<span class="' . 'product-discription">'. $product->discription .'</span>'!!}
						</div>
						
					</div>
					<div class="right-price-box">

						<div class="right-top-box">
							<span style="color: #c80013" class="productPrice">${{$product->price}}</span>
						</div>
						
						<div class="right-bottom-box">
							<span class="productPrice productPrice_avg">
								{{($product->format!=null)?'(均價$'.$product->format.')':''}}
							</span>
						</div>
					</div>
					<div class="right-buy-box">
						<button>選擇</button>
					</div>
					

				</div>
				@endif
				@endforeach
			</div>
		</div>
		<div class="clear"></div>
		
		<div class="current-price-bar">
			<div class="current-price">
				<span>商品：</span><span id="current-price" style="float: right;color: red">-</span>
			</div>
			<div style="border-bottom: 0.5pt solid #c8c8c8" class="current-transport">
				<span>運費：</span><span id="current-transport" style="float: right;color: red">-</span>
			</div>
			<div class="current-total">
				<span>總額：</span><span id="current-total" style="float: right;color: red">-</span>
			</div>
		</div>

		<div class="clear"></div>

		<div class="buynow-form">
			<form id="form-buynow" action="/buynow/form" method="POST">
				{{csrf_field()}}
				<div class="form-stack">
					<input id="input-item" style="display: none;" value="" name="item[]" type="text">
					<input id="transport-item" style="display: none;" type="text" value="99999" name="item[]">
					<span>數量：</span>
					<div onclick="quantity(-1);" class="quantity-minus">-</div><div class="quantity-quantity">
					<span id="span-quantity">1</span><input id="input-quantity" style="display: none;" value="1" name="quantity[]" type="text"><input id="transport-quantity" style="display: none;" type="number" value="1" name="quantity[]"></div><div class="quantity-plus" onclick="quantity(1);">
					+</div>
				</div>
			
				<div style="border-bottom: 0.5pt solid #c8c8c8;" class="clear"></div>
				<div class="clear"></div>
				<div class="form-stack">
					<span><font color="red">*</font>姓名</span><input id="ship_name"  class="required" name="ship_name" type="text" placeholder="填寫收件人姓名">
				</div>
				<div class="form-stack">
					<span><font color="red">*</font>手機</span><input id="ship_phone" class="required" name="ship_phone" type="text" placeholder="填寫收件人聯繫電話">
				</div>
				<div class="form-stack">
					<span><font color="red">*</font>地址</span>
					<select class="ship_county" name="ship_county" style="width: 30%;">
						<option value="">縣市</option>
						<option value="基隆市">基隆市</option>
						<option value="台北市">台北市</option>
						<option value="新北市">新北市</option>
						<option value="桃園市">桃園市</option>
						<option value="新竹市">新竹市</option>
						<option value="新竹縣">新竹縣</option>
						<option value="苗栗縣">苗栗縣</option>
						<option value="台中市">台中市</option>
						<option value="彰化縣">彰化縣</option>
						<option value="南投縣">南投縣</option>
						<option value="雲林縣">雲林縣</option>
						<option value="嘉義市">嘉義市</option>
						<option value="嘉義縣">嘉義縣</option>
						<option value="台南市">台南市</option>
						<option value="高雄市">高雄市</option>
						<option value="屏東縣">屏東縣</option>
						<option value="台東縣">台東縣</option>
						<option value="花蓮縣">花蓮縣</option>
						<option value="宜蘭縣">宜蘭縣</option>
						</option>
					</select>
					<select class="ship_district" name="ship_district" style="width: 30%;">
						<option value="">地區</option>
					</select>
				</div>
				<div class="form-stack">
					<span></span><input id="ship_address" class="required" name="ship_address" type="text" placeholder="街道門牌資訊">
				</div>
				{{-- <div class="form-stack">
					<span><font color="red">*</font>E-mail</span><input id="ship_email" name="ship_email" type="text" placeholder="選填，收件人電子郵件">
				</div> --}}
				<div class="form-stack">
					<span><font color="red">*</font>付款方式</span><span><font color="orange">貨到付款</font></span><input id="ship_pay_by" name="ship_pay_by" style="display: none;" type="text" value="cod">
				</div>
				<div class="form-stack">
					<span>&nbsp備註</span><textarea name="ship_memo" id="" placeholder="選填，配送時間或其他通知事項"></textarea>
				</div>
				<div class="form-stack">
					<span id="submit-btn" onclick="checkingForm();">確定送出</span>
				</div>
			</form>
		</div>


		<div style="display: none;" class="alert-area">
			<span><font color="red">＊</font>號處不得空白</span>
		</div>


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
<script src="{{asset('js/jquery/jquery-3.2.1.min.js')}}"></script>
<script>

// let firstSlug = "";
// let firstPrice = 0;

$(document).ready(function(){
	// product_select(firstSlug, firstPrice);
});
</script>
<script src="{{asset('js/_singleBuy.js')}}"></script>
</html>