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
	padding-bottom: 136px;
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
.kartTable td,.kartTable th{
	height: 80px;
	vertical-align: middle;
	padding: 20px 0 20px 0;
}
.kartTable tr{
	border-bottom: 1pt solid rgba(0,0,0,0.1);
}
.littleIMG{
	height: 100%;
	/*width: auto;*/
	max-width: 100%;
	max-height: 100%;
}
#payBtn,.shipping{
	display: none;
}
/*.sureToBuy,.kartTable{
	display: none;
}*/
.shipping{
	width: 100%;
}
.shipping input{
	display: inline-block;
}
.shipping span{
	margin: 0 4px 0 4px;
}
.shipping td{
	padding: 4px 0 4px 0;
	width: 100%;
}
.shipping label{
	width: 14%;
}
.radio{
	margin:0 4px 0 4px;
}
#arriveDate{
	width: 25%;
	display: none;
}
.ifThree{
	display: none;
	width: 60%;
}
.pay_by{
	display: inline-block;
	border:1pt solid #000;
	width: 40%;
	text-align: center;
	padding: 10px 0 10px 0;
	border-radius: 0.3em;
	margin-top: 4px;
}
.required{
	color: red;
}
.alert{
	position: absolute;
	left: 0;
	bottom: 0%;
	width: 100%;
	height: 56px;
	padding: 0;
	text-align: center;
}
.alerting{
	border:2pt solid red;
}
.loader-bg{
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0,0,0,0.5);
	z-index: 10;
}
.loader-box{
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%,-50%);
	text-align: center;
	color:white;
	z-index: 9999999999;
}
.loader {
  width: 80px;
  height: 80px;
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #0275d8;
  margin-bottom: 20px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}
/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
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


				<form class="kartForm" action="{{route('bill.store')}}" method="POST">
				{{csrf_field()}}
					<table class="kartTable" style="width: 100%">	
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
						
					<table class="shipping">
						<tr>
							<td>
								<label for=""><span class="required">*</span>收件人：</label>
								<input id="ship_name" name="ship_name" type="text" class="form-control" placeholder="收件人" value="{{Auth::user()->name}}" style="width: 25%;">		
							
								<input id="radio1" class="radio" type="radio" name="ship_gender" value="1" checked><span>先生</span>
  								<input id="radio2" class="radio" type="radio" name="ship_gender" value="2"><span>小姐</span>
							</td>
						</tr>
						<tr>
							<td>
								<label for=""><span class="required">*</span>E-mail：</label>
								<input id="ship_email" name="ship_email" type="text" class="form-control" placeholder="E-mail" style="width: 40%;" value="{{Auth::user()->email}}">
							</td>
						</tr>
						<tr>
							<td>
								<label for=""><span class="required">*</span>聯絡電話：</label>
								<input id="ship_phone" name="ship_phone" type="text" class="form-control" placeholder="聯絡電話" style="width: 25%">		
							</td>
						</tr>
						<tr>
							<td>
								<label for=""><span class="required">*</span>地址：</label>
								<select id="ship_county" name="ship_county" class="form-control ship_county" style="width: 12%;display: inline-block;">
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
							
								<select name="ship_district" class="form-control ship_district" style="width: 12%;display: inline-block;">
									<option value="">地區</option>
									
									
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for=""></label>
								<input id="ship_address" name="ship_address" type="text" class="form-control" placeholder="地址" style="width: 40%;display: inline-block;">
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
								<select name="ship_receipt" class="two-three form-control" style="width: 12%; display: inline-block;">
									<option id="two" value="2">二聯</option>
									<option id="three" value="3">三聯</option>
								</select>
								<div class="ifThree">
									<input id="ship_three_name" name="ship_three_name" type="text" class="form-control ship_three" placeholder="購買人" style="width: 30%;">	
									<input id="ship_three_id" name="ship_three_id" type="text" class="form-control ship_three" placeholder="統一編號" style="width: 30%;">
									<input id="ship_three_company" name="ship_three_company" type="text" class="form-control ship_three" placeholder="公司名稱" style="width: 30%;">	
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="" style="vertical-align: top;">　備註：</label>
								<textarea name="ship_memo" class="form-control" style="display: inline-block;width: 40%;height: 56px;max-height: 56px;min-height: 56px;" placeholder="備註"></textarea> 
							</td>
						</tr>
						<tr>
							<td>
								<label for=""><span class="required">*</span>付款方式：</label>
								<div class="pay_by">
									<input id="pay_by_credit" class="radio" type="radio" name="ship_pay_by" value="credit"><span>信用卡</span>
	  								<input id="pay_by_atm" class="radio" type="radio" name="ship_pay_by" value="atm"><span>ATM</span>
	  								<input id="pay_by_cod" class="radio" type="radio" name="ship_pay_by" value="cod"><span>貨到付款</span>	
								</div>
							</td>
						</tr>

					</table>
					

					<div class="submitBtn" style="margin-top: 20px;position: absolute;right: 20px;">
						<span style=";margin: 0 8px 0 8px;font-size: 18pt;">總額：</span>
						<span style="font-size: 18pt;" id="sum"></span>
						<div onclick="sureToBuy()" class="sureToBuy btn btn-primary" style="margin:0 0 0 20px;">
							確定購買
						</div>
						<div onclick="checkForm();" id="payBtn" class="btn">送出訂單</div>
						
					</div>
				</form>
					
				@endif
				<div class="alert"></div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
{{ Html::script('js/_kart.js') }}
@endsection