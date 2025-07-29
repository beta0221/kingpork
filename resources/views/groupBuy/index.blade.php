@extends('main')

@section('title','| VIP團購區')

@section('stylesheets')
{{Html::style('css/_groupBuy.css')}}
{{Html::style('css/_kart_1023_2.css')}}
{{Html::style('css/_groupBuy_kart.css')}}
@endsection

@section('content')


<div class="product-display-image-outter">
	<div class="product-display-image">
		<img src="/images/vip-banner.png" alt="">
	</div>	
</div>


<div class="intro-title-bar mb-4">

	<div class="intro-title intro-title-line intro-title-line-left"></div>
	<div class="intro-title intro-title-text">
		<img src="{{asset('images/group-buy-vip.png')}}">
	</div>
	<div class="intro-title intro-title-line intro-title-line-right"></div>
</div>


<div class="contentPage">
	
	<div class="container">
			@if (count($errors) > 0)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
			@endif
		<div class="row">

			<div class="col-lg-10 offset-lg-1 col-12 mt-3 mb-3">
				<div class="alert-field"></div>
				<div class="product-displayDiv">

					

					<div class="product-display-title">
						<h2>團購王VIP專區</h2>
					</div>


					@foreach($products as $product)
					
					<div class="product-cell" data-name="{{$product->name}}" data-slug="{{$product->slug}}" data-price="{{$product->price}}">

						<span class="product-cell-name">{{$product->name}}</span>
						<span class="product-cell-des">{{$product->discription}}</span>
						
						<span class="product-cell-select">選擇</span>
						<span class="product-cell-price">${{$product->price}}</span>
					</div>

					@endforeach

					<div class="p-2">
						<div class="btn btn-block btn-lg btn-warning" onclick="nextStep()">下一步</div>
					</div>

				</div>
				
			</div>
		</div>
	</div>
</div>
 


<!-- Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">填寫寄送資料</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
		{{-- form start --}}
			
			<form class="kartForm" action="{{route('bill.store')}}" method="POST">

				<h5 class="p-3 m-0">購買商品：</h5>
				<div class="items-container" style="padding: 16px">

					{{-- <span>排骨</span>
					<input id="itemSlug" style="display: none;" type="text" name="item[]" value="">
					<input class="quantity" type="number" value="1" name="quantity[]">
					<div class="btn btn-danger btn-sm d-inline-block">刪除</div> --}}

				</div>
				<hr class="p-2">

				{{csrf_field()}}
				<input type="hidden" value="0" name="carrier_id">
					
					<table class="shipping">
						<tr>
							<td class="shipping-top-TD">
								<label for=""><span class="required">*</span>收件人：</label>
								<input id="ship_name" name="ship_name" type="text" class="shipping-ship_name form-control" placeholder="收件人" value="{{Auth::user()->name}}" style="">		
							
								<input id="radio1" class="radio" type="radio" name="ship_gender" value="1" checked>
								<span>先生</span>

  								<input id="radio2" class="radio" type="radio" name="ship_gender" value="2">
  								<span>小姐</span>
							</td>
						</tr>

						<tr>
							<td>
								<label for=""><span class="required">*</span>E-mail：</label>
								<input id="ship_email" name="ship_email" type="text" class="shipping-ship_email form-control" placeholder="E-mail" value="{{Auth::user()->email}}">
							</td>
						</tr>

						<tr>
							<td>
								<label for=""><span class="required">*</span>聯絡電話：</label>
								<input id="ship_phone" name="ship_phone" type="text" class="shipping-ship_phone form-control" placeholder="聯絡電話">		
							</td>
						</tr>

						<tr>
							<td>
								<label for=""><span class="required">*</span>地址：</label>

								<select id="favorite_address" name="favorite_address" class="form-control shipping-ship_address">
									@foreach ($addresses as $address)
										<option value="{{$address->id}}">{{$address->county}} {{$address->district}} {{$address->address}}</option>	
									@endforeach
								</select>

								<select id="ship_county" name="ship_county" class="shipping-ship_county form-control ship_county">
									<option value="">縣市</option>
									<option value="基隆市">基隆市</option>
									<option value="台北市">台北市</option>
									<option value="新北市">新北市</option>
									<option value="桃園市">桃園市</option>
									<option value="新竹市">新竹市</option>
									<option value="新竹縣">新竹縣</option>
									<option value="苗栗縣">苗栗縣</option>
									<option value="台中市">台中市</option>
									<option value="彰化縣">彰化縣</option>
									<option value="南投縣">南投縣</option>
									<option value="雲林縣">雲林縣</option>
									<option value="嘉義市">嘉義市</option>
									<option value="嘉義縣">嘉義縣</option>
									<option value="台南市">台南市</option>
									<option value="高雄市">高雄市</option>
									<option value="屏東縣">屏東縣</option>
									<option value="台東縣">台東縣</option>
									<option value="花蓮縣">花蓮縣</option>
									<option value="宜蘭縣">宜蘭縣</option>
								</select>
							
								<select id="ship_district" name="ship_district" class="shipping-ship_district form-control ship_district">
									<option value="">地區</option>
									
									
								</select>
							</td>
						</tr>

						<tr id="ship_address_column" class="blackcat-column">
							<td>
								<label for=""></label>
								<input id="ship_address" name="ship_address" type="text" class="shipping-ship_address form-control" placeholder="地址">
							</td>
						</tr>

						<tr>
							<td>
								<label for=""></label>
								<div id="new_address_button" class="btn btn-warning" style="cursor: pointer" onclick="onClick_newAddress()">
									其他地址
								</div>
								<div id="favorite_address_button" class="btn btn-primary" style="cursor: pointer" onclick="onClick_favoriteAddress()">
									常用地址 <img style="width: 16px" src="/images/step-1-2.png">
								</div>
								<div id="add_favorite_address" style="display: inline-block">
									<span style="vertical-align: middle" style="display: inline-block">設為常用地址</span>
									<input name="add_favorite" type="checkbox" class="form-control" style="width: 24px; height:24px; vertical-align: middle">
								</div>
								<input id="use_favorite_address" class="d-none" type="checkbox" name="use_favorite_address">
							</td>
						</tr>
						
						<tr style="display: none">
							<td>
								<label for="">　希望到貨日:</label>

								<input id="arriveNo" class="radio" type="radio" name="ship_arrive" value="no" checked><span>不指定</span>
								<input id="arriveYes" class="radio" type="radio" name="ship_arrive" value="yes"><span>指定</span>

								<input name="ship_arriveDate" id="arriveDate" type="date" class="form-control">

								

								<input type="text" class="form-control" style="width: 1%;height: 42px;visibility: hidden;">
							</td>
						</tr>

						<tr id="date_alert_tr">
							<td>
								<div class="date_alert date_alert_after">
									<img src="{{asset('images/date_alert.png')}}">
									<font>交貨說明</font>
								</div>
							</td>
						</tr>

						<tr>
							<td>
								<label class="align-top" for="">　出貨時間：</label>
								{{-- <input name="ship_time" class="radio" type="radio" name="time" value="no" checked><span>不指定</span>
  								<input id="1300" name="ship_time" class="radio" type="radio" name="time" value="13:00"><span>13:00前</span>
  								<input id="1400-1800" name="ship_time" class="radio" type="radio" name="time" value="14:00-18:00"><span>14:00-18:00</span> --}}

								  <div class="d-inline-block">
									<div>
										<input class="radio" type="radio" name="ship_time" value="隨時可出貨" checked><span>隨時可出貨</span>
									</div>
									<div>
										<input class="radio" type="radio" name="ship_time" value="出貨前電話通知我"><span>出貨前電話通知我</span>
									</div>
									<div>
										<input class="radio" type="radio" name="ship_time" value="請等我通知日期再出貨"><span>請等我通知日期再出貨</span>
									</div>
								</div>
							</td>
						</tr>

						<tr>
							<td>
								<label for="">　發票：</label>
								<select name="ship_receipt" class="shipping-ship_receipt two-three form-control">
									<option id="two" value="2">二聯</option>
									<option id="three" value="3">三聯</option>
								</select>
								<div class="ifThree">
									{{-- <input id="ship_three_name" name="ship_three_name" type="text" class="form-control ship_three" placeholder="購買人" style="width: 30%;">	 --}}
									<input id="ship_three_id" name="ship_three_id" type="text" class="shipping-ship_three_id form-control ship_three" placeholder="統一編號">
									<input id="ship_three_company" name="ship_three_company" type="text" class="shipping-ship_three_company form-control ship_three" placeholder="公司名稱">	
								</div>
							</td>
						</tr>

						<tr>
							<td>
								<label for="" style="vertical-align: top;">　備註：</label>
								<textarea name="ship_memo" class="shipping-ship_memo form-control" placeholder="備註"></textarea> 
							</td>
						</tr>

						<tr>
							<td>
								<label for="">　使用紅利：</label>
								<input id="bonus" max="" name="bonus" type="number" class="shipping-bonus form-control" value="0">
								<label id="myBonus" for="">　累積紅利：<span>{{Auth::user()->bonus}}</span></label>
							</td>
						</tr>

						<tr>
							<td class="shipping-bottom-TD">
								<label for=""><span class="required">*</span>付款方式：</label>
								<div class="pay_by">
									
	  								<input id="pay_by_atm" class="radio" type="radio" name="ship_pay_by" value="ATM"><span>僅限ATM轉帳</span>
	  								
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<label for=""></label>
								<div class="d-inline-block">
									<span class="required">單筆交易金額，不得超過新台幣5萬元</span>
								</div>
							</td>
						</tr>

					</table>
					

				</form>
			{{-- form end --}}

      </div>
      <div class="modal-footer">

		<div class="priceSum">
			<span >總額：</span>
			<span id="sum"></span>
			
		</div>

        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
        <div onclick="checkForm();" id="" class="btn btn-danger">送出訂單</div>

      </div>
    </div>
  </div>
</div>



@endsection

@section('scripts')
<script>
	// 是否有常用地址
	const hasFavoriteAddress = {{count($addresses) > 0 ? 'true' : 'false'}};
	
	// 加購條件
	const relation = {};
</script>

{{ Html::script('js/bootstrap/bootstrap.min.js') }}
{{ Html::script('js/_kart_1206_1.js') }}
{{ Html::script('js/_groupBuy_kart_1209_1.js') }}
@endsection