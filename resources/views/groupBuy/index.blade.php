@extends('main')

@section('title','| VIP團購區')

@section('stylesheets')
{{Html::style('css/_groupBuy.css')}}
{{Html::style('css/_kart.css')}}
{{Html::style('css/_groupBuy_kart.css')}}
@endsection

@section('content')



<div class="contentPage">
	<div class="container">
		<div class="row">
			<div class="col-lg-10 offset-lg-1 col-12 mt-4 mb-4">
				<div class="alert-field"></div>
				@foreach($products as $product)
					
				<div class="product-cell mt-4 mb-4" data-toggle="modal" data-target="#orderModal" onclick="selectItem({{$product->slug}},{{$product->price}})">
					<div class="product-cell-inBox">
						<img src="{{asset('/images/productsIMG').'/'. $product->image}}">
						<h2 class="product-cell-inBox-name">{{$product->name}}</h2>
						<h3 class="product-cell-inBox-des">{{$product->discription}}</h3>
						<h3 class="product-cell-inBox-price">${{$product->price}}</h3>
						<h3 class="product-cell-inBox-bonus">累積紅利<font size="22pt" color="#c80013">{{$product->bonus}}</font>點！</h3>
					</div>
				</div>

				@endforeach
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
				{{csrf_field()}}
				<input id="itemSlug" style="display: none;" type="text" name="item[]" value="">
				<input style="display:none;" class="quantity" type="number" value="1" name="quantity[]">
					
					<table class="shipping">
						<tr>
							<td class="shipping-top-TD">
								<label for=""><span class="required">*</span>收件人：</label>
								<input id="ship_name" name="ship_name" type="text" class="shipping-ship_name orm-control" placeholder="收件人" value="{{Auth::user()->name}}" style="">		
							
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
							
								<select name="ship_district" class="shipping-ship_district form-control ship_district">
									<option value="">地區</option>
									
									
								</select>
							</td>
						</tr>

						<tr>
							<td>
								<label for=""></label>
								<input id="ship_address" name="ship_address" type="text" class="shipping-ship_address form-control" placeholder="地址">
							</td>
						</tr>
						
						<tr>
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
								<label for="">　時間：</label>
								<input name="ship_time" class="radio" type="radio" name="time" value="no" checked><span>不指定</span>
  								<input id="1300" name="ship_time" class="radio" type="radio" name="time" value="13:00"><span>13:00前</span>
  								<input id="1400-1800" name="ship_time" class="radio" type="radio" name="time" value="14:00-18:00"><span>14:00-18:00</span>
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
								<label id="myBonus" for="">　累積紅利：<span></span></label>
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

					</table>
					

				</form>
			{{-- form end --}}

      </div>
      <div class="modal-footer">

		<div class="priceSum">
			<span style=";margin: 0 8px 0 8px;font-size: 18pt;float: left;">總額：</span>
			<span style="margin: 0 8px 0 8px;font-size: 18pt;float: left;" id="sum-overrite"></span>
			
		</div>

        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
        <div onclick="checkForm();" id="payBtn" class="btn ml-3">送出訂單</div>

      </div>
    </div>
  </div>
</div>



@endsection

@section('scripts')
{{ Html::script('js/bootstrap/bootstrap.min.js') }}
{{ Html::script('js/_kart.js') }}
{{ Html::script('js/_groupBuy_kart.js') }}
@endsection