@extends('main')

@section('title','| 官方商城')

@section('stylesheets')
{{Html::style('css/Style_landingPage.css')}}
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
<div class="productArea container">
	{{-- <h2 style="text-align: center;margin-bottom: 20px;letter-spacing: 4px;">我要購買<span style="margin: 0 12px 0 12px;">/</span>GO SHOPPING</h2> --}}
	<div class="row">
		<div class="product col-md-4 col-12">
			<div class="P- P-pork">
				<a href="{{route('productCategory.show',1)}}">
					<img src="{{asset('images/productsIMG/pork.png')}}" alt="金園排骨">
				</a>
				<div onclick="location.href='{{route('productCategory.show',1)}}';" class="P-buy">我要買</div>
			</div>
		</div>
		<div class="product col-md-4 col-12">
			<div class="P- P-both">
				<a href="{{route('productCategory.show',3)}}">
					<img src="{{asset('images/productsIMG/both.png')}}" alt="金園幸福雙響">
				</a>
				<div onclick="location.href='{{route('productCategory.show',3)}}';" class="P-buy">我要買</div>
			</div>
		</div>
		<div class="product col-md-4 col-12">
			<div class="P- P-chicken">
				<a href="{{route('productCategory.show',2)}}">
					<img src="{{asset('images/productsIMG/chicken.png')}}" alt="金園排骨厚切雞腿排">
				</a>
				<div onclick="location.href='{{route('productCategory.show',2)}}';" class="P-buy">我要買</div>
			</div>
		</div>
	</div>
</div>
{{-- products end --}}

{{-- brand story start --}}
{{-- <div class="brandStory" id="brandStory">
	<div class="container">
		<div class="row">
			<div class="story col-md-12">
				<p>【金園排骨】是老字號的經典台灣品牌，我們遵循傳統手工製法，每片肉品堅持不添加任何防腐劑與化學製劑，就是要將最古早的原汁原味用心呈現給您！老字號的金園排骨，從阿公阿嬤，從爸爸媽媽，從我們小時候開始，一代一代口味的承傳，這正印證台灣念真情，也證明台灣懷念的古早味－金園排骨。</p>
				<p>俗語說【人說情歌總是老的好，走遍天涯海角忘不了，人說情人卻是老的好，曾經滄海桑田分不了】金園排骨源自西門町傳承一甲子的古早味，陪伴著台灣人的成長，多少成功的企業家難以忘記的老口味，金園排骨像一首老歌，常常值得懷念，金園排骨更像老情人一樣，更值得細心回味。</p>
			</div>
		</div>
	</div>
</div> --}}
{{-- brand story end --}}

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
				<img class="shop-now-img" src="{{asset('images/king2-4.png')}}" alt="金園排骨萬年店">

			</div>
			<div id="story-2-box" class=" shop-intro-box">
				<img src="{{asset('images/king2-2.png')}}" alt="金園排骨春日店">

			</div>
			<div id="story-3-box" class="shop-intro-box">
				<img src="{{asset('images/king2-3.png')}}" alt="金園排骨長庚店">

			</div>
			<div id="story-4-box" class="shop-intro-box">
				<img src="{{asset('images/king2-1.png')}}" alt="金園排骨總公司">

			</div>
		</div>

	</div>	
	
	<div class="col-12 shop-intro-story-2">
		<div class="arrow-up"></div>
		<div id="story-1">
			<h2>金園排骨 -【萬年店】</h3>
			<h4>食記：<a href="http://tong5410.pixnet.net/blog/post/334926782-%E3%80%90%E5%8F%B0%E5%8C%97%E3%80%82%E8%A5%BF%E9%96%80%E3%80%91%E8%80%81%E5%AD%97%E8%99%9F%E7%9A%84%E9%87%91%E5%9C%92%E6%8E%92%E9%AA%A8%E5%BA%97%EF%BC%8C%E5%A4%9A%E5%B9%B4%E5%BE%8C" target="_blank">【台北。西門】老字號金園排骨，多年後...</a></h4>
			<h4>聯絡電話：02-2381-9797</h4>
			<h4>地址：台北市萬華區西寧南路70號</h4>
			<h4>【萬年商業大樓Ｂ1】</h4>
			<div class="google-map">
				<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d962.9620002394962!2d121.5057460611311!3d25.043638155559304!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xa8da6f98aafe0d4b!2z6YeR5ZyS5o6S6aqo!5e0!3m2!1szh-TW!2stw!4v1531730898720" width="100%" height="100%" frameborder="0" style="border:0" allowfullscreen></iframe>
			</div>
		</div>
		<div id="story-2">
			<h2>金園排骨 -【春日店】</h3>
			<h4>食記：<a href="https://yukiblog.tw/read-3918.html" target="_blank">桃園【金園排骨/春日店】西門町古早味...</a></h4>
			<h4>客服專線：03-339-0363 ; 03-339-0347</h4>
			<h4>e-mail：may@sacred.com.tw</h4>
			<h4>　　　　kingpork@sacred.com.tw</h4>
			<h4>地址：桃園市桃園區春日路278號</h4>
			<h4>店休日：三大節(端午/中秋/年節)</h4>
			
		</div>
		<div id="story-3">
			<h2>金園排骨 -【長庚店】</h3>
			<h4>客服專線：03-318-3315</h4>
			<h4>e-mail：may@sacred.com.tw</h4>
			<h4>　　　　kingpork@sacred.com.tw</h4>
			<h4>地址：桃園市龜山區復興街5號</h4>
			<h4>【林口長庚地下美食街】</h4>
			<h4>店休日：週日</h4>

		</div>
		<div id="story-4">
			<h2>金園排骨股份有限公司</h3>
			<h4>客服專線：0800-552-999</h4>
			<h4>傳真電話：03-334-8965;03-337-5338</h4>
			<h4>e-mail：may@sacred.com.tw</h4>
			<h4>　　　　kingpork@sacred.com.tw</h4>
			<h4>地址：桃園市桃園區大有路59號３樓</h4>
			<h4>上班日：8:30-18:00</h4>
			

		</div>
	</div>

	

</div> {{-- end of shop intro --}}


{{-- shop intro end --}}

{{-- posts start --}}
{{-- <div class="posts">
	<div class="container">
		<div class="row">
			<div class="pp1 post col-md-4">
				<div class="post1 blog-post"></div>
			</div>
			<div class="pp2 post col-md-4">
				<div class="post2 blog-post"></div>
			</div>
			<div class="pp3 post col-md-4">
				<div class="post3 blog-post"></div>
			</div>
		</div>
	</div>
</div> --}}
{{-- posts end --}}

{{-- process start --}}
{{-- <div class="process" id="process">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
				<div class="howtoOrder">
					<h2>如何購買<span>/</span>HOW TO ORDER</h2>
					<div class="orderBar">
						<div class="stepsBox">
							<div class="steps" id="step1">
								<img src="{{asset('images/step1.png')}}" alt="">
								<div>線上選購</div>
							</div>
							<div class="glip"></div>
							<div class="steps" id="step2">
								<img src="{{asset('images/step2.png')}}" alt="">
								<div>放入購物車</div>
							</div>
							<div class="glip"></div>
							<div class="steps" id="step3">
								<img src="{{asset('images/step3.png')}}" alt="">
								<div>送出訂單</div>
							</div>
							<div class="glip"></div>
							<div class="steps" id="step4">
								<img src="{{asset('images/step4.png')}}" alt="">
								<div>宅配到府</div>
							</div>
						</div>	
					</div>
				</div>

			</div>
		</div>
	</div>
</div> --}}
{{-- process end --}}

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