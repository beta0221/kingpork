@extends('main')

@section('title','| 首頁')

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
{{-- banner end --}}

{{-- products start --}}
<div class="productArea container">
	<div class="row">
		<div class="product col-md-4">
			<div class="P- P-pork"><a href="{{route('productCategory.show',3)}}"><img src="{{asset('images/productsIMG/pork.jpg')}}" alt=""></a></div>
		</div>
		<div class="product col-md-4">
			<div class="P- P-both"><a href="{{route('productCategory.show',4)}}"><img src="{{asset('images/productsIMG/both.jpg')}}" alt=""></a></div>
		</div>
		<div class="product col-md-4">
			<div class="P- P-chicken"><a href="{{route('productCategory.show',2)}}"><img src="{{asset('images/productsIMG/chicken.jpg')}}" alt=""></a></div>
		</div>
	</div>
</div>
{{-- products end --}}

{{-- brand story start --}}
<div class="brandStory" id="brandStory">
	<div class="container">
		<div class="row">
			<div class="story col-md-12">
				<p>【金園排骨】是老字號的經典台灣品牌，我們遵循傳統手工製法，每片肉品堅持不添加任何防腐劑與化學製劑，就是要將最古早的原汁原味用心呈現給您！老字號的金園排骨，從阿公阿嬤，從爸爸媽媽，從我們小時候開始，一代一代口味的承傳，這正印證台灣念真情，也證明台灣懷念的古早味－金園排骨。</p>
				<p>俗語說【人說情歌總是老的好，走遍天涯海角忘不了，人說情人卻是老的好，曾經滄海桑田分不了】金園排骨源自西門町傳承一甲子的古早味，陪伴著台灣人的成長，多少成功的企業家難以忘記的老口味，金園排骨像一首老歌，常常值得懷念，金園排骨更像老情人一樣，更值得細心回味。</p>
			</div>
		</div>
	</div>
</div>
{{-- brand story end --}}

{{-- posts start --}}
<div class="posts">
	<div class="container">
		<div class="row">
			<div class="pp1 post col-md-4">
				<div class="post1"></div>
			</div>
			<div class="pp2 post col-md-4">
				<div class="post2"></div>
			</div>
			<div class="pp3 post col-md-4">
				<div class="post3"></div>
			</div>
		</div>
	</div>
</div>
{{-- posts end --}}

{{-- process start --}}
<div class="process" id="process">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
				<div class="howtoOrder">
					<h1>如何購買<span>/</span>HOW TO ORDER</h1>
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
</div>
{{-- process end --}}



@endsection

@section('scripts')
{{Html::script('js/parallax/jquery.parallax-1.1.3.js')}}
{{Html::script('js/owl-carousel/owl.carousel.min.js')}}
<script>
	$(document).ready(function(){
		$(".owl-carousel").owlCarousel({
			loop:true,
			nav:true,
			items:1,
			navText:[],
			autoplay:true,
			autoplaySpeed:2000,
			smartSpeed:1000
		})

		$('#brandStory').parallax("100%", 0.3);
		$('#process').parallax("0%", 0.3);
	});
</script>
@endsection