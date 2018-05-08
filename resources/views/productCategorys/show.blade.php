@extends('main')

@section('title','| 類別商品')

@section('stylesheets')
<style>
.content{
	margin:60px 0 80px 0;
}
/*-----------------------------------------------*/
.productIMG{
	/*border:1pt solid #000;*/
	width: 100%;
	padding-top:100%;
	border-radius: 0.3em;
	box-shadow: 4px 4px 16px 2px rgba(0, 0, 0, 0.15);
	overflow: hidden;
}
.productItem{
	display: inline-block;
	background: linear-gradient(0deg,rgba(195,28,34,0.5),rgba(195,28,34,1));
	color: #fff;
	padding: 4px 8px;
	margin: 0 4px 8px 4px;
	border-radius: 0.3em;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	cursor: pointer;
}
.productItem:hover{
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.5);
}
.nowItem{
	background-color: rgba(0,0,0,0.1);
}
.productRow{
	/*border:1pt solid #000;*/
	height: 112px;
	padding:8px 0 0 4px;
}
/*-------------------------------------------------*/
.productIMG>img{
	height: 100%;
	width: 100%;
	position: absolute;
	top: 0;
}
.hr{
	margin: 40px 0 40px 0;
}
.addBar{
	/*border:1pt solid #000;*/
	height: 56px;
	width: calc(100% - 30px);
	position: absolute;
	bottom: 0;
	left: 15px;
	padding:8px 4px 0 4px;
}
#addToKartBtn,#goToKartBtn,.place{
	float: right;
	margin: 0 4px 0 4px;
}
#addToKartBtn,#goToKartBtn{
	border-radius: 0.3em;
	display: none;
	padding: 4px 8px;
	cursor: pointer;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	border:none;
	color: #fff;
	outline: none;
}
#addToKartBtn:hover,#goToKartBtn:hover{
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.5);
}
#addToKartBtn{
	background: linear-gradient(0deg,rgba(225,139,31,0.6),rgba(225,139,31,1));
}
#goToKartBtn{
	background: linear-gradient(0deg,rgba(195,28,34,0.5),rgba(195,28,34,1));
}
.place{
	display: inline-block;
	padding: 7px 8px;
}
/*---------------------------------------------------*/
.menuBoard{
	background-color: rgba(255,255,255,0.5);
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	position: absolute;
	top: 0;
	left: 15px;
	height: 100%;
	width: calc(100% - 30px);
	border-radius: 0.3em;
}
.data{
	/*border:1pt solid #000;*/
	margin: 8px 0 8px 0;
	height: 56px;
	padding:8px 0 0 16px;
}
.data>span{
	font-size: 18pt;
}
.data>h3{
	display: inline-block;
}
/*---------------------------------------------------*/
.productsBar{
	height: 160px;
	/*border:1pt solid #000;*/
	margin-bottom: 60px;
}
.catImg{
	overflow: hidden;
	height: 100%;
	border-radius: 0.3em;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	filter: opacity(50%);
}
.catImg:hover{
	filter:opacity(100%);
}
.catImg>img{
	max-width: 100%;
	top: -50%;
}
#currentCat{
	filter:opacity(100%);
	border:4pt solid rgba(195,28,34,1);;
}
.P-pork::before{
	content: "排骨";
	color: #fff;
	font-size: 22pt;
	left: 16px;
	top: 8px;
	position: absolute;
	z-index: 1;
}
.P-both::before{
	content: "幸福雙響";
	color: #fff;
	font-size: 22pt;
	left: 16px;
	top: 8px;
	position: absolute;
	z-index: 1;
}
.P-chicken::before{
	content: "雞腿";
	color: #fff;
	font-size: 22pt;
	left: 16px;
	top: 8px;
	position: absolute;
	z-index: 1;
}
.flash,.flashRed{
	height: 56px;
	width: calc(100% - 30px);
	border-radius: 0.3rem;
	
	box-shadow: 4px 4px 16px 2px rgba(0, 0, 0, 0.3);
	position: absolute;
	bottom: 56px;
	padding: 16px 24px;
	color: #fff;
	display: none;
	transition: ease-in-out 1s;
}
.flash{
	background-color: rgba(41,112,245,0.8);
}
.flashRed{
	background-color: rgba(201,44,63,0.8);
}
</style>
@endsection

@section('content')



<div class="content">
	<div class="container">
		<div class="row productsBar">

			<div class="col-md-4">
				<div id="{{Request::is('productCategory/1') ? 'currentCat' : ''}}" class="catImg P-pork">
					<a href="{{route('productCategory.show',1)}}">
						<img src="{{asset('images/productsIMG/both.jpg')}}" alt="">	
					</a>
				</div>
				
			</div>
			<div class="col-md-4">
				<div id="{{Request::is('productCategory/3') ? 'currentCat' : ''}}" class="catImg P-both">
					<a href="{{route('productCategory.show',3)}}">
						<img src="{{asset('images/productsIMG/both.jpg')}}" alt="">	
					</a>
				</div>
				
			</div>
			<div class="col-md-4">
				<div id="{{Request::is('productCategory/2') ? 'currentCat' : ''}}" class="catImg P-chicken">
					<a href="{{route('productCategory.show',2)}}">
						<img src="{{asset('images/productsIMG/chicken.jpg')}}" alt="">
					</a>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="productIMG">
					<img id="productIMG" src="{{asset('images/productsIMG') . '/'}}{{Request::is('productCategory/1') ? 'pork.jpg' : ''}}{{Request::is('productCategory/3') ? 'both.jpg' : ''}}{{Request::is('productCategory/2') ? 'chicken.jpg' : ''}}" alt="">
				</div>
			</div>
			<div class="col-md-6">
				<div class="menuBoard"></div>
				<div class="productRow">
					@foreach($productCategory->products as $product)
						<div onclick="showProduct({{$product->id}});" class="productItem"><span>{{$product->name}}</span></div>
					@endforeach
				</div>

				<hr style="margin: 0;width: 95%;margin: 0 auto;">
				
				<div class="data name">
					<h2>
						{{Request::is('productCategory/1') ? '厚切手打豬排' : ''}}
						{{Request::is('productCategory/3') ? '幸福雙享組合' : ''}}
						{{Request::is('productCategory/2') ? '無骨嫩雞腿排' : ''}}		
					</h2>
				</div>
				<hr style="margin: 0;width: 95%;margin: 0 auto;">
				<div class="data format"><span>規格：</span><h3></h3></div>
				<hr style="margin: 0;width: 95%;margin: 0 auto;">
				<div class="data price"><span>售價：</span><h3></h3></div>
				<hr style="margin: 0;width: 95%;margin: 0 auto;">
				<div class="data bonus"><span>紅利：</span><h3></h3></div>
				
				<div class="flash">
					<span></span>
				</div>
				<div class="flashRed">
					<span></span>
				</div>

				<div class="addBar">
					
					<button id="goToKartBtn" onclick="location.href='{{route('kart.index')}}'">前往結帳</button>
					<button id="addToKartBtn" onclick="">加入購物車</button>
					<div class="place"><span></span></div>
					
				</div>
			</div>
		</div>
		<hr class="hr">
		<div class="row">
			<div class="col-md-10 offset-md-1 aboutContent">
				@if(Request::url() == config('app.url').'/productCategory/1')
				<p>小簡介：上等里肌肉 &rarr; 手工抓捏、拍打&rarr; 再真空按摩五十分鐘 &rarr; 造就出獨持口感</p>
				<p><strong>【特級厚切手打豬排（調理生排骨肉）】</strong></p>
				<p>◆重量／容量：200g&plusmn;5%／包</p>
				<p>◆內容物：豬排肉、地瓜粉、水、黑胡椒、醬油、 砂糖、L-麩酸鈉 (味精)</p>
				<p>◆產地：台灣</p>
				<p>◆有效期限：180天</p>
				<p>◆食用方式：無須退凍，泡水5分鐘即可退凍（視季節當時水溫而論）平底鍋，中火煎雙面各煎1~2分鐘</p>
				<p>◆保存方式：冷凍</p>
				<p>◆包裝方式：單片真空包裝</p>
				<p><img src="/images/articleIMG/123-350.jpg" /></p>
				<p>---------------------------------------------------------------------------------</p>
				<p>貼心小教學</p>
				<p>---------------------------------------------------------------------------------◎本產品造型、顏色以實物為主</p>
				<p>◎商品圖片僅供實物參考。內容物組成以實物及商品說明為主</p>
				<p>◎注意事項：本為食品特殊類別，ㄧ經拆封或食物、包裝碰撞變形或保存不良導致變質</p>
				<p 、非運送過程失溫導致食品變質者，恕無法退換貨，敬請見諒與配合。</p>
				<p>◎退貨事項：除商品本身有瑕疵可辦理退貨，商品一經使用或損毀即不可退貨 退貨必須保 留紙箱及商品</p>
				<p>--------------------------------------------------------------------------------</p>
				<p>金園廚房</p>
				<p>【一】排骨肉片切絲先入鍋拌炒至8分熟即可加入配料成為湯麵或鮮肉湯。<br /><br />【二】排骨肉片切絲當一般肉絲用，可炒韭黃、高麗菜等新鮮蔬果。<br /><br />【三】排骨肉片切丁炒飯，風味更佳。<strong><br /><br /></strong>【四】中秋佳節烤肉的最佳美味肉品(不需醃製，可以直接烤喔!)。</p>
				@endif
				@if(Request::url() == config('app.url').'/productCategory/2')
				雞腿
				@endif
				@if(Request::url() == config('app.url').'/productCategory/3')
				幸福雙響
				@endif
			</div>
		</div>
	</div>
</div>


@endsection


@section('scripts')
<script>
	function showProduct(id){

		$.ajaxSetup({
  			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
  			}
		});
		$.ajax({

			type:'GET',
			url:'{{url('products')}}' +'/' + id,
			dataType:'json',
			success: function (response) {

				$('.name>h2').empty().append(response.name);
                $('.format>h3').empty().append(response.format);
                $('.price>h3').empty().append(response.price);
                $('.bonus>h3').empty().append(response.bonus);
                $('.aboutContent').empty().append(response.content);
                $('#productIMG').attr('src','{{asset('images/productsIMG') . '/'}}'+response.image);

                if (response.add == true) {
                	$('#addToKartBtn').css('display','none');
                	$('.place>span').empty().append('已加入購物車');
                }else{
                	$('#addToKartBtn').css('display','inline-block');
                	$('.place>span').empty();
                	$('#addToKartBtn').attr('onclick','addToKart('+ response.id +');');	
                }
                $('#goToKartBtn').css('display','inline-block');
            },
            error: function () {
                alert('錯誤');
            },
		});

	};

	function addToKart(id){
		$.ajaxSetup({
  			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  			}
		});
		$.ajax({
			type:'POST',
			url:'{{route('kart.store')}}',
			dataType:'json',
			data: {
				'product_id':id,
			},
			success: function (response) {
                // alert(response.msg);
                $('.flash>span').empty().append(response.msg);
                $('.flash').css('display','block');

                setTimeout(function(){
                	$('.flash>span').empty();
					$('.flash').css({'display':'none'});                	
                },2000);
                $('#addToKartBtn').css('display','none');
                $('.place>span').empty().append('已加入購物車');
                // navbar cart 加一
                var inKart = parseInt($('#inKart').html()) + 1;
                $('#inKart').empty().append(inKart);
            },
            error: function (data) {
                
                $('.flashRed>span').empty().append('無法加入購物車，請先登入會員');
                $('.flashRed').css('display','block');
                setTimeout(function(){
                	$('.flashRed>span').empty();
					$('.flashRed').css({'display':'none'});                	
                },2000);
            }
		});
	}

$(document).ready(function(){
	$('.productItem').click(function(){
		$('.nowItem').removeClass('nowItem');
		$(this).addClass('nowItem');
	});
});
</script>
@endsection