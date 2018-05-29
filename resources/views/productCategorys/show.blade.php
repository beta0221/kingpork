@extends('main')

@section('title','| 購物趣')

@section('stylesheets')
<style>
.content{
	margin:60px 0 80px 0;
}
/*-----------------------------------------------*/
.productIMG{
	/*border:1pt solid #000;*/
	overflow: hidden;
	width: 100%;
	padding-top:100%;
	border-radius: 0.3em;
	box-shadow: 4px 4px 16px 2px rgba(0, 0, 0, 0.15);
}
#productIMG{
	border-radius:0.3em;

}
.productItem{
	height: 56px;
	line-height: 56px;
	color: #000;
	padding: 0px 92px 0 12px;
	font-size: 22px;
	cursor: pointer;
}
.productItem:hover{
	background-color: rgba(0,0,0,0.1);
}
.nowItem{
	background-color: rgba(0,0,0,0.1);
}
.productPrice{
	float: right;
	color: #c80013;
}
.productPrice_avg{
	color:#000;
	font-size: 13px;
}
.titleRow{
	text-align: center;
	height: 100px;
	background: linear-gradient(0deg,rgba(217,83,79,0.9),rgba(217,83,79,1));
	border-radius: 0.3em 0.3em 0 0;
}
.titleRow h1{
	line-height: 100px;
	letter-spacing: 2px;
	color: white;
}
/*-------------------------------------------------*/
.productIMG>img{
	height: 100%;
	width: 100%;
	position: absolute;
	top: 0;
}
.hr{
	margin: 60px 0 60px 0;
}
.addToKartBtn{
	position: absolute;
	color: white;
	top: 8px;
	right: 8px;
	padding: 0;
	height: 40px;
	width: 76px;
	font-size: 18px;
	cursor: pointer;
	line-height: 40px;
	border:none;
	border-radius: .3em;
	background:#f0ad4e;
	display: none;
}
.addToKartBtn img{
	height: 20px;
}
.deleteKartBtn{
	background:rgba(0,0,0,0.3);
}
.goToKartBtn{
	position: absolute;
	bottom: 12px;
	right: 28px;
	padding: 0 32px;
	height: 80px;
    font-size: 40px;
    border-radius: 0.3rem;
    color: #fff;
    background-color: #d9534f;
    font-weight: 500;
    letter-spacing: 2px;
    line-height: 80px;
    text-align: center;
    border:none;
    cursor: pointer;
    transition: all .1s ease-in-out;
}
.goToKartBtn img{
	height: 40px;
	transform: translate(8px,-5px);
}
.goToKartBtn:hover{
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.5);
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
.catImg img{
	height: 100%;
	top: 0%;
}
#currentCat{
	filter:opacity(100%);
	border:4pt solid rgba(195,28,34,1);;
}
/*.P-pork::before{
	content: "厚切排骨";
	color: #fff;
	font-size: 28pt;
	letter-spacing: 4px;
	left: 18px;
	top: 12px;
	position: absolute;
	z-index: 1;
}
.P-both::before{
	content: "雙響組合";
	color: #fff;
	font-size: 28pt;
	letter-spacing: 4px;
	left: 18px;
	top: 12px;
	position: absolute;
	z-index: 1;
}
.P-chicken::before{
	content: "鮮嫩雞腿排";
	color: #fff;
	font-size: 28pt;
	letter-spacing: 4px;
	left: 18px;
	top: 12px;
	position: absolute;
	z-index: 1;
}*/
.flash,.flashRed{
	height: 80px;
	width: 40%;
	border-radius: 0.3rem;
	box-shadow: 4px 4px 16px 2px rgba(0, 0, 0, 0.3);
	position: absolute;
	bottom: 12px;
	left: 28px;
	padding: 16px 24px;
	color: #fff;
	display: none;
	transition: ease-in-out 1s;
}
.flash{
	background-color: green;
}
.flashRed{
	background-color: red;
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
						<img src="{{asset('images/productsIMG/pork2.png')}}" alt="">	
					</a>
				</div>
				
			</div>
			<div class="col-md-4">
				<div id="{{Request::is('productCategory/3') ? 'currentCat' : ''}}" class="catImg P-both">
					<a href="{{route('productCategory.show',3)}}">
						<img src="{{asset('images/productsIMG/both2.png')}}" alt="">	
					</a>
				</div>
				
			</div>
			<div class="col-md-4">
				<div id="{{Request::is('productCategory/2') ? 'currentCat' : ''}}" class="catImg P-chicken">
					<a href="{{route('productCategory.show',2)}}">
						<img src="{{asset('images/productsIMG/chicken2.png')}}" alt="">
					</a>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="productIMG">
					<img id="productIMG" src="{{asset('images/productsIMG') . '/'}}{{Request::is('productCategory/1') ? 'pork.png' : ''}}{{Request::is('productCategory/3') ? 'both.png' : ''}}{{Request::is('productCategory/2') ? 'chicken.png' : ''}}" alt="">
				</div>
			</div>
			<div class="col-md-6">
				<div class="menuBoard"></div>
				<div class="titleRow">
					<h1>
						{{Request::is('productCategory/1') ? '厚切手打豬排' : ''}}
						{{Request::is('productCategory/3') ? '幸福雙響組合' : ''}}
						{{Request::is('productCategory/2') ? '無骨嫩雞腿排' : ''}}		
					</h1>
				</div>

				<hr style="margin: 0;width: 95%;margin: 0 auto;">
				
				@foreach($productCategory->products as $product)
					<div onclick="showProduct({{$product->id}});" class="productItem">
						<span>{{$product->name}}</span>
						<span class="productPrice">${{$product->price}}元</span>
						<span class="productPrice productPrice_avg">（均價${{$product->format}}）</span>
						<button id="add_{{$product->id}}" class="addToKartBtn" onclick="addToKart({{$product->id}})" product_id="{{$product->id}}">
							加入<img src="{{asset('images/cart.png')}}">
						</button>
					</div>
				@endforeach
				

				<div class="flash">
					<span></span>
				</div>
				<div class="flashRed">
					<span></span>
				</div>
					
				<button id="goToKartBtn" class="goToKartBtn" onclick="location.href='{{route('kart.index')}}'">前往結帳<img src="{{asset('images/point.png')}}" alt=""></button>
					
			</div>
		</div>
		<hr class="hr">
		<div class="row">
			<div class="col-md-10 offset-md-1 aboutContent">
				{!!$productCategory->content!!}
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
	$(document).ready(function(){

		$.ajaxSetup({
  			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
  			}
		});

		$('.productItem').click(function(){			//點擊後加入nowItem Class
			$('.nowItem').removeClass('nowItem');
			$(this).addClass('nowItem');
		});

		$('.addToKartBtn').each(function(){
			var id = $(this).attr('product_id');
			
			$.ajax({
				type:'GET',
				url:'/checkIfKart/'+$(this).attr('product_id'),
				dataType:'json',
				success: function (response) {
					if (response.msg == true) {
						$('#add_'+id).empty().append('取消<img src="{{asset('images/cart.png')}}">');
						$('#add_'+id).addClass('deleteKartBtn');
						$('#add_'+id).attr('onclick','deleteFromKart('+id+')');
					}
	            },
	            error: function () {
	                // alert('錯誤');
	            },
			});
		});
		setTimeout(function(){
			$('.addToKartBtn').css('display','block');
		},500);
		
	});

	function showProduct(id){

		$.ajax({
			type:'GET',
			url:'{{url('products')}}' +'/' + id,
			dataType:'json',
			success: function (response) {
                $('#productIMG').attr('src','{{asset('images/productsIMG') . '/'}}'+response.image);
            },
            error: function () {
                // alert('錯誤');
            },
		});

	};

	function addToKart(id){
		
		$.ajax({
			type:'POST',
			url:'{{route('kart.store')}}',
			dataType:'json',
			data: {
				'product_id':id,
			},
			success: function (response) {
                
                $('#add_'+id).empty().append('取消<img src="{{asset('images/cart.png')}}">');
                $('#add_'+id).addClass('deleteKartBtn');
                $('#add_'+id).attr('onclick','deleteFromKart('+id+')');
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

	function deleteFromKart(id){
		
		$.ajax({
			type:'POST',
			url:'/kart/'+id,
			dataType:'json',
			data: {
				_method: 'delete',
			},
			success: function (response) {
                if (response.status == 1) {
                	$('#add_'+id).empty().append('加入<img src="{{asset('images/cart.png')}}">');
	                $('#add_'+id).removeClass('deleteKartBtn')
	                $('#add_'+id).attr('onclick','addToKart('+id+')');
                	// navbar cart 減一
	                var inKart = parseInt($('#inKart').html()) - 1;
	                $('#inKart').empty().append(inKart);
	                
                }
            },
            error: function () {
                alert('無法從購物車中刪除');
            }
		});
	}

</script>
@endsection