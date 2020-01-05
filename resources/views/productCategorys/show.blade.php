@extends('main')

@section('title','| 購物趣')

@section('stylesheets')
{{Html::style('css/_showProduct.css')}}
@endsection

@section('content')

<div class="content">

	<div class="product-bar">
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-pork" id="{{Request::is('productCategory/1') ? 'currentCat' : ''}}"  onclick="location.href='/productCategory/1'">
						<a href="{{route('productCategory.show',1)}}">		
							<img src="{{asset('images/productsIMG/pork2.png')}}" alt="厚切手打豬排">	
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-both" id="{{Request::is('productCategory/3') ? 'currentCat' : ''}}"  onclick="location.href='/productCategory/3'">
						<a href="{{route('productCategory.show',3)}}">
							<img src="{{asset('images/productsIMG/both2.png')}}" alt="幸福雙響組合">
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-chicken" id="{{Request::is('productCategory/2') ? 'currentCat' : ''}}"  onclick="location.href='/productCategory/2'">
						<a href="{{route('productCategory.show',2)}}">
							<img src="{{asset('images/productsIMG/chicken2.png')}}" alt="無骨嫩雞腿排">	
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-fish" id="{{Request::is('productCategory/9') ? 'currentCat' : ''}}"  onclick="location.href='/productCategory/9'">
						<a href="{{route('productCategory.show',9)}}">
							<img src="{{asset('images/productsIMG/fish2.png')}}" alt="鯖魚">	
						</a>
					</div>
				</div>
			</div>
		</div>
		{{-- <div class="product-cell float-cell"> --}}
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-soup" id="{{Request::is('productCategory/11') ? 'currentCat' : ''}}" onclick="location.href='/productCategory/11'">
						<a href="{{route('productCategory.show',11)}}">
							<img src="{{asset('images/productsIMG/pot2.png')}}" alt="酸白菜鍋底">	
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- <div class="product-bar">
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-soup" id="{{Request::is('productCategory/11') ? 'currentCat' : ''}}" onclick="location.href='/productCategory/11'">
						<a href="{{route('productCategory.show',11)}}">
							<img src="{{asset('images/productsIMG/pot2.png')}}" alt="酸白菜鍋底">	
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-soup" id="{{Request::is('productCategory/11') ? 'currentCat' : ''}}" onclick="location.href='/productCategory/11'">
						<a href="{{route('productCategory.show',11)}}">
							<img src="{{asset('images/productsIMG/pot2.png')}}" alt="酸白菜鍋底">	
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-soup" id="{{Request::is('productCategory/11') ? 'currentCat' : ''}}" onclick="location.href='/productCategory/11'">
						<a href="{{route('productCategory.show',11)}}">
							<img src="{{asset('images/productsIMG/pot2.png')}}" alt="酸白菜鍋底">	
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-soup" id="{{Request::is('productCategory/11') ? 'currentCat' : ''}}" onclick="location.href='/productCategory/11'">
						<a href="{{route('productCategory.show',11)}}">
							<img src="{{asset('images/productsIMG/pot2.png')}}" alt="酸白菜鍋底">	
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="product-cell">
			<div>
				<div>
					<div class="catImg P-soup" id="{{Request::is('productCategory/11') ? 'currentCat' : ''}}" onclick="location.href='/productCategory/11'">
						<a href="{{route('productCategory.show',11)}}">
							<img src="{{asset('images/productsIMG/pot2.png')}}" alt="酸白菜鍋底">	
						</a>
					</div>
				</div>
			</div>
		</div>
	</div> --}}

	<div class="container">

		<div class="row">
			<div class="col-md-6 col-12">
				<div class="productIMG">
					<img id="productIMG" src="{{asset('images/productsIMG') . '/'}}{{Request::is('productCategory/1') ? 'pork.png' : ''}}{{Request::is('productCategory/3') ? 'both.png' : ''}}{{Request::is('productCategory/2') ? 'chicken.png' : ''}}{{Request::is('productCategory/9') ? 'fish3.png' : ''}}{{Request::is('productCategory/11') ? 'soup-test.png' : ''}}">
				</div>
			</div>
			<div class="col-md-6 col-12">
				<div class="menuBoard">
					<div class="menuBoard-content">
						<div class="titleRow">
							<h1>
								{{Request::is('productCategory/1') ? '厚切手打豬排' : ''}}
								{{Request::is('productCategory/3') ? '幸福雙饗組合' : ''}}
								{{Request::is('productCategory/2') ? '無骨嫩雞腿排' : ''}}
								{{Request::is('productCategory/9') ? '鯖魚' : ''}}
								{{Request::is('productCategory/11') ? '酸白菜鍋底' : ''}}
							</h1>
						</div>


						<div class="productBox">
						@foreach($productCategory->products as $product)


							@if($product->public == 1)
							<div onclick="showProduct({{$product->id}});" class="productItem">
								
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
										<span class="productPrice">${{$product->price}}</span>
									</div>
									
									<div class="right-bottom-box">
										<span class="productPrice productPrice_avg">
											{{($product->format!=null)?'(均價$'.$product->format.')':''}}
											
										</span>
									</div>

								</div>
								<div class="right-button-box">

									<button id="add_{{$product->id}}" class="addToKartBtn" onclick="addToKart({{$product->id}})" product_id="{{$product->id}}">

										加入<img src="{{asset('images/cart.png')}}">

									</button>

								</div>


							</div>
							@endif


						@endforeach
						</div>
							
						<button id="goToKartBtn" class="goToKartBtn" onclick="location.href='{{route('kart.index')}}'">前往結帳<img src="{{asset('images/point.png')}}" alt="前往結帳"></button>
							
						</div>
					</div>
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

@section('fbq')
<script>
	var content_ids = [];
	var products = {!!$productCategory->products!!};

	products.forEach(element => {
		content_ids.push(element.id.toString());
	});

	var fbqObject = {
		content_ids:content_ids,
		content_type:'product',
	};
	function waitForFbq(callback){
			if(typeof fbq !== 'undefined'){
				callback()
			} else {
				setTimeout(function () {
					waitForFbq(callback)
				}, 500)
			}
		}
	waitForFbq(function () {
		fbq('track','ViewContent',fbqObject);
	})
</script>
@endsection