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
<div style="height:20px"></div>
<div class="new-product-bar">
	{{-- <div class="product-cell">
		<div>
			<div>
				<div class="catImg">
					<a href="/category/event_1111">
						<img src="{{asset('images/cat/landing/event_1111.png')}}" alt="雙十一">
					</a>
					<div onclick="location.href='/category/event_1111';" class="P-buy">我要買</div>
				</div>
			</div>
		</div>
	</div> --}}
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg">
						<a href="{{route('productCategory.show',1)}}">		
							<img src="{{asset('images/cat/landing/1.png')}}" alt="金園排骨">
						</a>
						<div onclick="location.href='{{route('productCategory.show',1)}}';" class="P-buy">我要買</div>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg">
						<a href="{{route('productCategory.show',3)}}">
							<img src="{{asset('images/cat/landing/3.png')}}" alt="幸福雙響組合">
						</a>
						<div onclick="location.href='{{route('productCategory.show',3)}}';" class="P-buy">我要買</div>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg">
						<a href="{{route('productCategory.show',2)}}">
							<img src="{{asset('images/cat/landing/2.png')}}" alt="金園排骨厚切雞腿排">
						</a>
						<div onclick="location.href='{{route('productCategory.show',2)}}';" class="P-buy">我要買</div>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg">
						<a href="{{route('productCategory.show',9)}}">
							<img src="{{asset('images/cat/landing/9.png')}}" alt="鯖魚">	
						</a>
						<div onclick="location.href='{{route('productCategory.show',9)}}';" class="P-buy">我要買</div>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg">
						<a href="{{route('productCategory.show',20)}}">
							<img src="{{asset('images/cat/landing/20.png')}}" alt="活凍金鑽蝦">	
						</a>
						<div onclick="location.href='{{route('productCategory.show',20)}}';" class="P-buy">我要買</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="new-product-bar">
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-fish">
						<a href="{{route('productCategory.show',13)}}">
							<img src="{{asset('images/cat/landing/13.png')}}" alt="義式蕃茄鍋底">	
						</a>
						<div onclick="location.href='{{route('productCategory.show',13)}}';" class="P-buy">我要買</div>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-fish">
						<a href="{{route('productCategory.show',14)}}">
							<img src="{{asset('images/cat/landing/14.png')}}" alt="泰式酸辣湯底">	
						</a>
						<div onclick="location.href='{{route('productCategory.show',14)}}';" class="P-buy">我要買</div>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-fish">
						<a href="{{route('productCategory.show',15)}}">
							<img src="{{asset('images/cat/landing/15.png')}}" alt="養生麻油酒香鍋底">	
						</a>
						<div onclick="location.href='{{route('productCategory.show',15)}}';" class="P-buy">我要買</div>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-fish">
						<a href="{{route('productCategory.show',16)}}">
							<img src="{{asset('images/cat/landing/16.png')}}" alt="特級麻辣養生鍋底">
						</a>
						<div onclick="location.href='{{route('productCategory.show',16)}}';" class="P-buy">我要買</div>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg">
						<a href="{{route('productCategory.show',21)}}">
							<img src="{{asset('images/cat/landing/21.png')}}" alt="活凍Q麵">
						</a>
						<div onclick="location.href='{{route('productCategory.show',21)}}';" class="P-buy">我要買</div>
					</div>
				</div>
			</div>
		</div>
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