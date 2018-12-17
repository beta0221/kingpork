@extends('main')

@section('title','| VIP團購區')

@section('stylesheets')
{{Html::style('css/_sendGift.css')}}
@endsection

@section('content')

{{-- {{$giftProduct}} --}}



{{-- <form action="{{route('bill.store')}}" method="POST">
	{{csrf_field()}}
	<input type="text" name="item[]" value="30002">
	<input id="quantity" type="number" name="quantity[]">
	<input type="text" name="ship_name" value="*">
	<input type="text" name="ship_phone" value="*">
	<input type="text" name="ship_address" id="ship_address">
	<input type="text" name="ship_email" value="{{Auth::user()->email}}">

	<input type="radio" name="ship_arrive" value="no" checked>
	<input name="ship_time" class="radio" type="radio" name="time" value="no" checked>

	<select name="ship_receipt" class="form-control">
		<option value="2">二聯</option>
		<option value="3">三聯</option>
	</select>

	<div class="ifThree">
		<input id="ship_three_id" name="ship_three_id" type="text" class="shipping-ship_three_id form-control ship_three" placeholder="統一編號">
		<input id="ship_three_company" name="ship_three_company" type="text" class="shipping-ship_three_company form-control ship_three" placeholder="公司名稱">	
	</div>

	<textarea name="ship_memo" class="shipping-ship_memo form-control" placeholder="備註"></textarea> 

	<input type="radio" name="ship_pay_by" value="CREDIT"><span>信用卡</span>
	<input type="radio" name="ship_pay_by" value="ATM"><span>ATM</span>
	<input type="submit" value="確定送出">
</form> --}}

<div class="container">
	<div class="row">
		<div class="col-lg-10 offset-lg-1 col-12 mt-3 mb-3">

			<div class="product-displayDiv">
				<div class="product-display-image">
				<img src="/images/productsIMG/{{$giftProduct->image}}" alt="">
				</div>
				<div class="product-display-Info">
					<div class="product-display-Info-name">
						{{$giftProduct->name}}
					</div>
					<div class="product-display-Info-dis">
						{{$giftProduct->discription}}
					</div>
					<div class="product-display-Info-price">
						{{$giftProduct->price}}
					</div>
					
				</div>
			</div>


			<table id="sendListTable" class="send-listTable">
				<tr>
					<td>收件人</td>
					<td>地址</td>
					<td>聯絡電話</td>
					<td>數量</td>
					<td>刪除</td>
				</tr>
				<tr class="trList">
					<td><input class="form-control" type="text"></td>
					<td><input class="form-control" type="text"></td>
					<td><input class="form-control" type="text"></td>
					<td><input class="form-control" type="number" value="1"></td>
					<td></td>
				</tr>
			</table>
			<div id="addListBtn" class="btn btn-block btn-sm btn-success mt-1">新增</div>
			<div id="submitBtn" class="btn btn-block btn-primary">確定</div>


		</div>
	</div>
</div>


@endsection

@section('scripts')
{{ Html::script('js/_sendGift.js') }}
@endsection