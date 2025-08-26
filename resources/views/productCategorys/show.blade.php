@extends('main')

@section('title','| 購物趣')

@section('stylesheets')
{{Html::style('css/_showProduct_0320.css')}}
@endsection

@section('content')
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
<div class="content">

	<div class="product-bar" style="z-index:2">
		@foreach ($rowA as $key => $item)
		@include('partials._productPageCatCell',[
			'key' => $key,
			'item' => $item
		])
		@endforeach
	</div>

	<div class="product-bar" style="z-index:2">
		@foreach ($rowB as $key => $item)
		@include('partials._productPageCatCell',[
			'key' => $key,
			'item' => $item
		])
		@endforeach
	</div>

	<div class="product-bar" style="z-index:2">
		@foreach ($rowC as $key => $item)
		@include('partials._productPageCatCell',[
			'key' => $key,
			'item' => $item
		])
		@endforeach
	</div>

	<div class="container">

		<div class="row" style="{{Request::is('productCategory/19') ? 'display:none;' : ''}}">
			<div class="col-md-6 col-12">
				<div class="productIMG">
					<img id="productIMG" src="{{asset('images/cat')}}/detail/{{$productCategory->id}}.png">
				</div>
			</div>
			<div class="col-md-6 col-12">
				<div class="menuBoard">
					<div class="menuBoard-content">
						<div class="titleRow">
							<h1>
								<?php
									if(Request::is('productCategory/1')){
										echo '厚切手打豬排';
									}else if(Request::is('productCategory/3')){
										echo '幸福雙饗組合';
									}else if(Request::is('productCategory/2')){
										echo '無骨嫩雞腿排';
									}else{
										echo $productCategory->name;
									}
								?>
							</h1>
						</div>


						<div class="productBox">
						<?php $isEmpty = true; ?>
						@foreach($productCategory->products as $product)
							@if($product->public == 1)
							<?php $isEmpty = false; ?>
							<div class="productItem" onclick="showProductImg('{{$product->image}}')">
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

											{{-- 一竿進洞&地區年會 特殊狀況 --}}
											@if ($productCategory->id == 31 || $productCategory->id == 34)
											<del>${{ceil($product->price / 0.88)}}</del>	
											@endif
											
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
						
						@if ($isEmpty == true)
						<div class="text-center mt-4">
							<h3>非常抱歉！</h3>
							<h3>售完補貨中...</h3>
						</div>	
						@endif
						

						</div>
							
							
						</div>
					</div>
				</div>
			</div>

			

			@if(isset($additionalCategory))
			<hr>
			<div class="row">
				<div class="col-md-6 col-12">
					
				</div>

				<div class="col-md-6 col-12">
					<div class="menuBoard">
						<div class="menuBoard-content">
							<div class="titleRow">
								<h1>加價購</h1>
							</div>
							<div class="productBox">
								@foreach($additionalCategory->products as $product)
									@if($product->public == 1)
									<div class="productItem">
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
						</div>
					</div>
				</div>
			</div>
			@endif

			<button id="goToKartBtn" class="goToKartBtn" onclick="location.href='{{route('kart.index')}}'">前往結帳<img src="{{asset('images/point.png')}}" alt="前往結帳"></button>

		<hr class="hr">
		<div class="row">
			<div class="col-md-10 offset-md-1 aboutContent">
				{{-- <div>
					<img src="/images/cat/detail/19.png" style="width:100%" />
					<div class="btn" id="ExpressPayBtn" data-product-id="68" style="position:absolute;right:3%;top:42.5%;font-size:20px;color:#fff;background:orange;cursor:pointer">立即購買</div>
					<div class="btn" id="ExpressPayBtn" data-product-id="69" style="position:absolute;right:3%;top:70%;font-size:20px;color:#fff;background:orange;cursor:pointer">立即購買</div>
				</div> --}}
				{!!$productCategory->content!!}
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
	const kartProductsId = {!!json_encode($kartProductsId)!!};

	$(document).ready(function(){

		initAddToKartBtn();
		setTimeout(function(){
			$('.addToKartBtn').css('display','block');
		},200);

		$('.productItem').click(function(){			//點擊後加入nowItem Class
			$('.nowItem').removeClass('nowItem');
			$(this).addClass('nowItem');
		});
		
		$('.ExpressPayBtn').click(function(){
			var id = $(this).data('product-id');
			$.ajax({
				type:'POST',
				url:'{{route('kart.store')}}',
				dataType:'json',
				data: {
					'product_id':id,
				},
				success: function (response) {
					window.location.href = "/kart";
				},
				error: function (data) {
					
				}
			});
		});

	});

	function initAddToKartBtn(){

		kartProductsId.forEach(productId => {
			$('#add_'+productId).empty().append('取消<img src="/images/cart.png">');
			$('#add_'+productId).addClass('deleteKartBtn');
			$('#add_'+productId).attr('onclick','deleteFromKart('+productId+')');
		});

	}

	function showProductImg(img){
		$('#productIMG').attr('src','{{asset('images/productsIMG') . '/'}}' + img);
	}

	function addToKart(id){
		
		$.ajax({
			type:'POST',
			url:'{{route('kart.store')}}',
			dataType:'json',
			data: {
				'product_id':id,
			},
			success: function (response) {
                if(response == '403'){
					alert('請先選擇主商品，才可進行加價購。');
					return false;
				}
                $('#add_'+id).empty().append('取消<img src="{{asset('images/cart.png')}}">');
                $('#add_'+id).addClass('deleteKartBtn');
                $('#add_'+id).attr('onclick','deleteFromKart('+id+')');
                
				getKartProducts();
                
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

		var _id = id.toString();
		
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

				if(response=='403'){
					window.location.reload();
				}

                if (response.status == 1) {
                	$('#add_'+id).empty().append('加入<img src="{{asset('images/cart.png')}}">');
	                $('#add_'+id).removeClass('deleteKartBtn')
	                $('#add_'+id).attr('onclick','addToKart('+id+')');
                	
					getKartProducts();
	                
                }
            },
            error: function () {
                alert('無法從購物車中刪除');
            }
		});
	}

</script>
@endsection

