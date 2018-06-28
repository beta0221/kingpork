@extends('main')

@section('title','| 購物趣')

@section('stylesheets')
{{Html::style('css/_showProduct.css')}}
@endsection

@section('content')

<div class="content">
	<div class="container">
		<div class="row productsBar">

			<div class="col-md-4 col-4">
				<div id="{{Request::is('productCategory/1') ? 'currentCat' : ''}}" class="catImg P-pork">
					<a href="{{route('productCategory.show',1)}}">
						<img src="{{asset('images/productsIMG/pork2.png')}}" alt="">	
					</a>
				</div>
				
			</div>
			<div class="col-md-4 col-4">
				<div id="{{Request::is('productCategory/3') ? 'currentCat' : ''}}" class="catImg P-both">
					<a href="{{route('productCategory.show',3)}}">
						<img src="{{asset('images/productsIMG/both2.png')}}" alt="">	
					</a>
				</div>
				
			</div>
			<div class="col-md-4 col-4">
				<div id="{{Request::is('productCategory/2') ? 'currentCat' : ''}}" class="catImg P-chicken">
					<a href="{{route('productCategory.show',2)}}">
						<img src="{{asset('images/productsIMG/chicken2.png')}}" alt="">
					</a>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-6 col-12">
				<div class="productIMG">
					<img id="productIMG" src="{{asset('images/productsIMG') . '/'}}{{Request::is('productCategory/1') ? 'pork.png' : ''}}{{Request::is('productCategory/3') ? 'both.png' : ''}}{{Request::is('productCategory/2') ? 'chicken.png' : ''}}" alt="">
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
							</h1>
						</div>


						<div class="productBox">
						@foreach($productCategory->products as $product)



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
										<span class="productPrice productPrice_avg">(均價${{$product->format}})</span>
									</div>

								</div>
								<div class="right-button-box">

									<button id="add_{{$product->id}}" class="addToKartBtn" onclick="addToKart({{$product->id}})" product_id="{{$product->id}}">

										加入<img src="{{asset('images/cart.png')}}">

									</button>

								</div>
								{{-- 

								

								

								

								 --}}

							</div>



						@endforeach
						</div>
							
						<button id="goToKartBtn" class="goToKartBtn" onclick="location.href='{{route('kart.index')}}'">前往結帳<img src="{{asset('images/point.png')}}" alt=""></button>
							
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