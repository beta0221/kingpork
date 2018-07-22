<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>【金園排骨】西門町傳承一甲子的古早味</title>

	{{Html::style('css/_single.css')}}
</head>
<body>

	<div class="bar">
			<span class="query">
				<span>訂單查詢</span>
			</span>
			<a style="text-decoration: none;color: #fff;" href="{{Request::is('buynow/1') ? '/buynow/form/1' : ''}}{{Request::is('buynow/2') ? '/buynow/form/2' : ''}}{{Request::is('buynow/3') ? '/buynow/form/3' : ''}}">
				<span class="purchase">
					<span>
						立即下單
					</span>
				</span>
			</a>
			<span class="service">
				<span>線上客服</span>
			</span>
	</div>

	<div class="content">
		<div class="slider">
			<div class="slider-group">	{{-- 5:4 --}}
				<img src="{{asset('images/productsIMG/pork.png')}}">
			</div>
		</div>

		<div class="clear"></div>

		<div class="catBar">
			      <div class="cat P-pork">
			      	<div class="cat-img {{Request::is('buynow/1') ? 'currentCat' : ''}}">
			      		<a href="/buynow/1">
			    			<img src="{{asset('images/productsIMG/pork2.png')}}" alt="厚切手打豬排">
			    		</a>
			      	</div>
			</div><div class="cat P-both">
				<div class="cat-img {{Request::is('buynow/3') ? 'currentCat' : ''}}">
					<a href="/buynow/3">
						<img src="{{asset('images/productsIMG/both2.png')}}" alt="幸福雙響組合">
					</a>
				</div>
			</div><div class="cat P-chicken">
				<div class="cat-img {{Request::is('buynow/2') ? 'currentCat' : ''}}">
					<a href="/buynow/2">
						<img src="{{asset('images/productsIMG/chicken2.png')}}" alt="無骨嫩雞腿排">	
					</a>
				</div>
			</div>
		</div>
		<div class="clear"></div>

		<div class="introduce">
			<div class="introduce-stack">
				<h1 style="font-size: 20px;margin-left: -10px">【金園排骨】西門町傳承一甲子的古早味</h1>
			</div>
			<div class="introduce-stack">

				<span>
				<font style="font-size: 18px;color:red;font-weight: 800">NT${{$min}}~${{$max}} </font>
				<font style="font-size: 12px;text-decoration: line-through;">(NT${{$min/0.8}}~${{$max/0.8}})</font>
				</span>

				<span class="flag">免運費</span>
				<span class="flag">貨到付款</span>

			</div>
			<div class="introduce-stack">
				<img src="{{asset('images/black_cat.png')}}"><span>黑貓冷凍配送</span>
			</div>
			
			<div class="introduce-stack">
				<span>已搶購</span><span>{{$count}}</span><span>件</span>

				<span class="progress-bar-outter">
					<span class="progress-bar-inner"></span>
				</span>

				<span>{{floor($count/$target*100)}}%</span>
			</div>
			<div class="introduce-stack">
				<span>限時搶購</span>
				<span id="timer_H" class="timmer">{{$countDown['from_H']}}</span>
				<span>時</span>
				<span id="timer_i" class="timmer">{{$countDown['from_i']}}</span>
				<span>分</span>
				<span id="timer_s" class="timmer">{{$countDown['from_s']}}</span>
				<span>秒</span>
			</div>
		</div>
		<div class="clear"></div>

		<div class="categoryContent">
			{!!$productCategory->content!!}
		</div>
	</div>
</body>
<script src="{{asset('js/jquery/jquery-3.2.1.min.js')}}"></script>
<script src="{{asset('js/_single.js')}}"></script>
</html>