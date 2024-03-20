@extends('main')

@section('title','| 官方商城')

@section('stylesheets')
{{Html::style('css/Style_landingPage_0320.css')}}
{{Html::style('css/owl-carousel/owl.carousel.css')}}
@endsection

@section('content')


{{-- banner start --}}
<div class="banner owl-carousel owl-theme">
	@foreach($banners as $banner)
		<div class="item"><a href="{{$banner->link}}"><img src="{{asset('images/banner') . '/' . $banner->image}}" alt="{{$banner->alt}}"></a></div>
    @endforeach
</div>
<div class="marquee-landing">
	<span class="runner"></span>
</div>
{{-- banner end --}}

{{-- products start --}}
<div style="height:20px"></div>
<?php 
	$rowA = [
		1=>'金園排骨',
		3=>'幸福雙響組合',
		2=>'厚切雞腿排',
		30=>'厚切里肌'
	];
	$rowB = [
		32=>'鹽酥雞腿肉',
		33=>'鹽酥雞Pizza',
		9=>'鯖魚',
		20=>'活凍金鑽蝦',
	];
	$rowC = [
		13=>'義式蕃茄鍋底',
		14=>'泰式酸辣湯底',
		15=>'養生麻油酒香鍋底',
		16=>'特級麻辣養生鍋底',
	];
?>
	<div class="new-product-bar">
		@foreach ($rowA as $key => $item)
		@include('partials._landingPageProductCell',[
			'key' => $key,
			'item' => $item
		])
		@endforeach
	</div>

	<div class="new-product-bar">
		@foreach ($rowB as $key => $item)
		@include('partials._landingPageProductCell',[
			'key' => $key,
			'item' => $item
		])
		@endforeach
	</div>

	<div class="new-product-bar">
		@foreach ($rowC as $key => $item)
		@include('partials._landingPageProductCell',[
			'key' => $key,
			'item' => $item
		])
		@endforeach
	</div>

<div class="productArea container">

	<div class="row second-row">
		<div class="product col-md-6 col-12 group-buy">
			<div class="P-">
				<a href="/group-buy/">
					<img src="{{asset('images/vip-450*300.png')}}" alt="金園排骨">
				</a>
				<div onclick="location.href='/group-buy/';" class="P-buy">我要團購</div>
			</div>
		</div>

		<div class="product col-md-6 col-12 send-gift">
			<div class="P-">
				<a href="/send-gift/">
					<img src="{{asset('images/gift-450*300.png')}}" alt="金園排骨">
				</a>
				<div onclick="location.href='/send-gift/';" class="P-buy">我要送禮</div>
			</div>
		</div>

	</div>


</div>
{{-- products end --}}

{{-- shop intro start --}}
<div class="shop-intro">

	<div class="intro-title-bar">

		<div class="intro-title intro-title-line intro-title-line-left"></div>
		<div class="intro-title intro-title-text">
			<img src="{{asset('images/culture.png')}}" alt="各店簡介">
		</div>
		<div class="intro-title intro-title-line intro-title-line-right"></div>
	</div>

	
	<div class="col-12 shop-intro-story">
		<h1 style="font-size: 28px;"><font color="#860606">【金園排骨】</font>傳承了西門町一甲子的美好古早味</h1>
		<h2 style="font-size: 24px;line-height: 30px;">印證了台灣豐富、深刻、濃厚的在地人情味，陪伴許多人的成長，也紀錄了時代的變化，美好滋味猶如史詩般雋永絕對值得您細心品嘗回味。</h2>
	</div>

	<div class="shop-intro-container">

		<div class="shop-intro-row">
			
			<div id="story-1-box" class=" shop-intro-box">
				<img class="shop-now-img" src="{{asset('images/king2-1.png')}}" alt="金園排骨總公司">

			</div>
			<div id="story-2-box" class=" shop-intro-box">
				<img src="{{asset('images/king2-2.png')}}" alt="金園排骨春日店">

			</div>
			<div id="story-3-box" class="shop-intro-box">
				<img src="{{asset('images/king2-3.png')}}" alt="金園排骨長庚店">

			</div>
			<div id="story-4-box" class="shop-intro-box">
				<img src="{{asset('images/king2-4.png')}}" alt="金園排骨萬年店">

			</div>
		</div>

	</div>	
	
	<div class="col-12 shop-intro-story-2">
		<div class="arrow-up"></div>
		<div id="story-1">


			<h2>金園排骨股份有限公司</h3>
			<h4>客服　：0800-552-999</h4>
			<h4>傳真　：03-334-8965;03-337-5338</h4>
			<h4>E-mail ：may@sacred.com.tw</h4>
			<h4>　　　　kingpork@sacred.com.tw</h4>
			<h4>地址　：桃園市桃園區大有路59號３樓</h4>
			<h4>上班日：8:30-18:00</h4>

			<div class="google-map">
				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3615.8630138224594!2d121.3224238588966!3d25.00477030090872!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x34681e5a371e6323%3A0x3192af8956be9763!2zMzMw5qGD5ZyS5biC5qGD5ZyS5Y2A5aSn5pyJ6LevNTnomZ8!5e0!3m2!1szh-TW!2stw!4v1528712582756" width="100%" height="100%" frameborder="0" style="border:0" allowfullscreen></iframe>
			</div>


		</div>
		<div id="story-2">
			<h2>金園排骨 -【春日店】</h3>
			<h4>食記　：<a href="https://yukiblog.tw/read-3918.html" target="_blank">桃園【金園排骨/春日店】西門町古早味...</a></h4>
			<h4>客服　：03-339-0363 ; 03-339-0347</h4>
			<h4>E-mail ：may@sacred.com.tw</h4>
			<h4>　　　　kingpork@sacred.com.tw</h4>
			<h4>地址　：桃園市桃園區春日路278號</h4>
			<h4>店休日：三大節(端午/中秋/年節)</h4>
			
		</div>
		<div id="story-3">
			<h2>金園排骨 -【長庚店】</h3>
			<h4>客服　：03-318-3315</h4>
			<h4>E-mail ：may@sacred.com.tw</h4>
			<h4>　　　　kingpork@sacred.com.tw</h4>
			<h4>地址　：桃園市龜山區復興街5號</h4>
			<h4>　　　 【林口長庚地下美食街】</h4>
			<h4>店休日：週日</h4>

		</div>
		<div id="story-4">
			<h2>金園排骨 -【萬年店】</h3>
			<h4>食記　：<a href="http://tong5410.pixnet.net/blog/post/334926782-%E3%80%90%E5%8F%B0%E5%8C%97%E3%80%82%E8%A5%BF%E9%96%80%E3%80%91%E8%80%81%E5%AD%97%E8%99%9F%E7%9A%84%E9%87%91%E5%9C%92%E6%8E%92%E9%AA%A8%E5%BA%97%EF%BC%8C%E5%A4%9A%E5%B9%B4%E5%BE%8C" target="_blank">【台北。西門】老字號金園排骨，多年後...</a></h4>
			<h4>電話　：02-2381-9797</h4>
			<h4>地址　：台北市萬華區西寧南路70號</h4>
			<h4>　　　 【萬年商業大樓Ｂ1】</h4>

		</div>
	</div>

</div> {{-- end of shop intro --}}


@endsection

@section('scripts')
{{-- {{Html::script('js/parallax/jquery.parallax-1.1.3.js')}} --}}
{{Html::script('js/owl-carousel/owl.carousel.min.js')}}
{{Html::script('js/landingPage.js')}}
<script>
	$(document).ready(function(){
		$(".owl-carousel").owlCarousel({
			loop:true,
			nav:true,
			items:1,
			navText:[],
			autoplay:true,
			autoplaySpeed:1000,
			smartSpeed:500,
		})

		// $('#brandStory').parallax("100%", 0.3);
		// $('#process').parallax("0%", 0.3);
	});
</script>
@endsection