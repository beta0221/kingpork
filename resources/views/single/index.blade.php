<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer',"{{config('app.gtm_id')}}");</script>
	<!-- End Google Tag Manager -->


	<!-- Global site tag (gtag.js) - Google Analytics -->
	{{-- <script async src="https://www.googletagmanager.com/gtag/js?id={{config('app.ga_id')}}"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', '{{config('app.ga_id')}}');
	</script> --}}

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>【金園排骨】西門町傳承一甲子的古早味</title>

	{{Html::style('css/_single.css')}}
</head>
<body>
	<div class="background"></div>

	<div class="contact-info-bg">
		<div class="contact-info">
			<div onclick="contact();" id="contact-close">Ｘ</div>
			<span>金園排骨股份有限公司</span><br>
			<span>客服　：0800-552-999</span><br>
			<span>傳真　：03-334-8965;03-337-5338</span><br>
			<span>E-mail ：may@sacred.com.tw</span><br>
			<span>地址　：桃園市桃園區大有路59號３樓</span><br>
			<img src="{{asset('images/line.png')}}"><span>Line線上客服 ID：kingpork</span><br>
			<a href="https://www.facebook.com/KINGPORK/" target="_blank"><img src="{{asset('images/facebook.png')}}" ><span>Facebook線上客服</span></a>
		</div>
	</div>
	<div class="U-logo">
		<img src="{{asset('images/logo.png')}}" alt="金園排骨">
	</div>
	<div class="bar">
			<a href="/searchOrder">
				<span class="query">
					<span style="color: #000">訂單查詢</span>
				</span>
			</a>	
			<a style="text-decoration: none;color: #fff;" href="/buynow/{{$kol}}/form">
				<span class="purchase">
					<span>
						立即下單
					</span>
				</span>
			</a>
			{{-- <a href="{{route('contact')}}"> --}}
				<span onclick="contact();" class="service">
					<span style="color: #000">聯絡我們</span>
				</span>
			{{-- </a> --}}
	</div>

	<div class="content">

		{{-- 1920 x 1080 --}}
		<div class="slider" style="background-color:gray">
			{{-- <video playsinline autoplay muted loop class="slider-group" >
  				<source src="{{asset('vedios/head.mp4')}}" type="video/mp4">
  			您的瀏覽器不支援此影片
			</video> --}}
			<span></span>
			<img style="position: absolute;top:0;left:0;width:100%;" src="/images/moon_fast2.jpg" alt="首頁圖片">
		</div>

		<div class="clear"></div>

		{{-- <div class="catBar">
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
		<div class="clear"></div> --}}

		<div class="introduce">
			<div class="introduce-stack">
				<h1 style="margin-left: -10px"><大標題></h1>
			</div>
			<div >
				<h2 style="margin-bottom:8px"><中標題></h2>
				<h3 style="margin-left:16px">＊<敘述...></h3>
				<h3 style="margin-left:16px">＊<敘述...></h3>
				<h3 style="margin-left:16px">＊<敘述...></h3>
				<h3 style="margin-left:16px">＊<敘述...></h3>
			</div>
			<div class="introduce-stack">

				<span>
					<font style="font-size: 18px;color:red;font-weight: 800">NT$ <菜單研究所特惠價> </font>
					<font style="font-size: 12px;text-decoration: line-through;">(NT$ <官網原價>)</font>
				</span>

				<span class="flag">免運費</span>
				<span class="flag">貨到付款</span>

			</div>


			
			{{-- <div class="introduce-stack">
				<span style="font-size: 16px;font-weight: 600">加購第二組只要</span><font style="font-size: 18px;color:red;font-weight: 800">NT$ 799</font><font style="font-size: 16px;font-weight: 600"> ～！</font>
			</div> --}}
			

			<div class="introduce-stack">
				<img src="{{asset('images/black_cat.png')}}"><span>黑貓冷凍配送</span>
			</div>
			
			
			{{-- <div class="introduce-stack">
				<span>已搶購</span><span>{{$count}}</span><span>件</span>

				<span class="progress-bar-outter">
					<span class="progress-bar-inner"></span>
				</span>

				<span id="target-progress">{{floor($count/$target*100)}}%</span>
			</div> --}}
			

		</div>
		


		<div class="clear"></div>

		<div class="categoryContent">
			{!!$productCategory->content!!}
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
<script src="{{asset('js/_single.js')}}"></script>
</html>