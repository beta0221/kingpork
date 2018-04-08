@extends('main')

@section('title','| 我的購物車')

@section('stylesheets')
<style>
.contentPage{
    width: 100%;
    height: auto;
}
.outter{
	margin-top: 60px;
	margin-bottom: 60px;
	min-height: 520px;
	/*overflow-y: scroll;*/
	padding-bottom: 80px;
	background-color: rgba(255,255,255,0.5);
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	border-radius: 0.3em;
}
.quantity{
	width: 32px;
	border:1pt solid rgba(0,0,0,0.1);
	border-radius: 4px;
	/*outline: none;*/
}
.delBtn{
	display: inline-block;
	padding: 4px 8px 4px 8px;
	border-radius: 0.3em;
	background: linear-gradient(0deg,rgba(195,28,34,0.5),rgba(195,28,34,1));
	cursor: pointer;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	border:none;
	color: #fff;
	outline: none;
}
#payBtn{
	border:none;
	outline: none;
	cursor: pointer;
	border-radius: 0.3em;
	height: 40px;
	padding-left: 20px;
	padding-right: 20px;
	margin-left: 20px;
	color: #fff;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	background: linear-gradient(0deg,rgba(225,139,31,0.6),rgba(225,139,31,1));
}
.delBtn:hover,#payBtn:hover{
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.5);
}
td,th{
	height: 80px;
	vertical-align: middle;
	padding: 20px 0 20px 0;
}
tr{
	border-bottom: 1pt solid rgba(0,0,0,0.1);
}
.littleIMG{
	height: 100%;
	/*width: auto;*/
	max-width: 100%;
	max-height: 100%;
}
</style>
@endsection

@section('content')

<div class="contentPage">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2 outter">
				
				
				@if(count($products) == 0)
					<div style="position: absolute;top: 50%;transform: translateY(-50%);width: calc(100% - 30px);text-align: center;">
						<h1 style="">您的購物車中目前沒有商品</h1>	
					</div>
				@else
					<table style="width: 100%">	
						<form action="{{route('bill.store')}}" method="POST">
						{{csrf_field()}}
							<tr>
								<th></th>
								<th style="padding-left: 20px;">商品名稱</th>
								<th>數量</th>
								<th>價格</th>
								<th></th>
							</tr>
						@foreach($products as $product)
							<tr id="item{{$product->id}}">

								<td style="width: 80px;overflow: hidden;">
									<div style="width: 80px;height: 80px;">
										<img class="littleIMG" src="{{asset('images/productsIMG') . '/' . $product->image}}" alt="">
									</div>
								</td>

								<td style="padding-left: 20px;">
									<span>{{$product->name}}</span>
									<input style="display: none;" type="text" value="{{$product->slug}}" name="item[]">
								</td>

								<td style="width: 56px;">
									<input id="{{$product->slug}}" class="quantity" type="number" value="1" name="quantity[]" price="{{$product->price}}">
								</td>

								<td style="width:56px;">
									<span class="priceTag" id="priceTag{{$product->slug}}">{{$product->price}}</span>
								</td>

								<td style="width: 56px;">
									<div class="delBtn" data-method="delete" onclick="deleteWithAjax({{$product->id}})">刪除</div>
								</td>

							</tr>

						@endforeach
					</table>	
					
						<div style="margin-top: 20px;position: absolute;right: 20px;">
							<span style=";margin: 0 8px 0 8px;font-size: 18pt;">總額：</span>
							<span style="font-size: 18pt;" id="sum"></span>
							<button id="payBtn" type="submit">結帳去</button>
						</div>
					</form>
					
				@endif
				
				


			</div>
		</div>
	</div>
</div>



@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function(){

		var price = 0;
		$('.priceTag').each(function(){
			price =  price + parseInt($(this).html());
		});
		$('#sum').append(price);

		//------- 
		$('.quantity').change(function(){
			var slug = $(this).attr('id');
			var q = $(this).val();
			var pp = q * parseInt($(this).attr('price'));
			$('#priceTag' + slug).empty().append(pp);

			uploadSum();

		});
	});
	function deleteWithAjax(id){

		$.ajaxSetup({
  			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  			}
		});
		$.ajax({
			type:'POST',
			url:'kart/'+id,
			dataType:'json',
			data: {
				_method: 'delete',
			},
			success: function (response) {
                alert(response.msg);
                $('#item'+id).remove();
                // navbar cart 減一
                var inKart = parseInt($('#inKart').html()) - 1;
                $('#inKart').empty().append(inKart);

                uploadSum();
            },
            error: function () {
                alert('無法從購物車中刪除');
            }
		});
	}

	function uploadSum(){
		var price = 0;
			$('.priceTag').each(function(){
				price =  price + parseInt($(this).html());
			});
			$('#sum').empty().append(price);
			if (price == 0) {
				$('#payBtn').css('display','none');
			}else{
				$('#payBtn').css('display','inline-block');
			}
	}
</script>
@endsection