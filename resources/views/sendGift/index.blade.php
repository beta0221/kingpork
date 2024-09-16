@extends('main')

@section('title','| 送禮專區')

@section('stylesheets')
{{Html::style('css/_sendGift.css')}}
@endsection

@section('content')



{{-- <div class="product-display-image-outter">
	<div class="product-display-image">
		<img src="/images/send-gift.png" alt="">
	</div>	
</div> --}}

<div class="container mt-4">
	<div class="row">
		<div class="col-12">
			<h3>1. 選擇禮盒</h3>
		</div>
		
	</div>
	<div class="row">

		
		@foreach ($products as $i => $product)
			<?php $col = 12 / count($products) ?>
			<div class="col-{{$col}}">
				<div id="product-selection-{{$i}}" class="product-display-image-outter" onclick="selectProduct({{$i}})"> 
					<div class="product-display-image">
						<img src="/images/productsIMG/{{$product->image}}" alt="">
					</div>	
				</div>
			</div>
		@endforeach
	</div>
</div>


{{-- <div class="intro-title-bar mb-4">

	<div class="intro-title intro-title-line intro-title-line-left"></div>
	<div class="intro-title intro-title-text">
		<img src="{{asset('images/remind.png')}}">
	</div>
	<div class="intro-title intro-title-line intro-title-line-right"></div>
</div> --}}

<div class="mt-3 ghost">　</div>
<div class="container">
	<div class="row">
		<div class="col-12 mb-3">

			<div class="product-displayDiv">
				<h3>2. 填寫收件人資訊</h3>
				<h5>＊<font color="#c80013">不限</font>單一地址配送，一筆訂單多筆地址，送禮好便利</h5>
				<h5>＊金園真空包屬於冷凍食品，送禮時請留意收貨端</h5>
				

				<div class="product-display-Info">

					<div class="product-display-Info-name">
						<h2 id="product-name">_</h2>
					</div>

					<div class="product-display-Info-price">
						<h2 id="product-discription">_</h2>
						{{-- <h2>排骨 5 片＋雞腿 5 支</h2> --}}
						<h2 id="product-price" class="price-h2"><font color="#c80013">_</font></h2>
					</div>
					
					<div class="send-listTableDiv">
						<table id="sendListTable" class="send-listTable">
							@if(!Auth::user())
							<div id="before-start-mask"></div>
							@endif
							<tr>
								<td class="send-name">收件人</td>
								<td class="send-ad">地址</td>
								<td class="send-ph">聯絡電話</td>
								<td style="display: none" class="send-time">時段</td>
								<td class="send-qu">數量</td>
								<td class="send-new">
									<div id="addListBtn" class="btn btn-block btn-sm btn-success">新增</div>
								</td>
							</tr>
							<tr class="trList">
								<td><input class="form-control" type="text"></td>
								<td><input class="form-control" type="text"></td>
								<td><input class="form-control" type="text"></td>
								<td style="display: none">
									<select class="form-control" name="" id="">
										<option value="no">不指定</option>
										<option value="13:00">13:00前</option>
										<option value="14:00-18:00">14:00-18:00</option>
									</select>
								</td>
								<td><input class="form-control" type="number" value="1"></td>
								<td class="delBtnTd">-</td>
							</tr>
						</table>

						

						<div class="form-outterDiv mt-4 mb-4 col-lg-8 offset-lg-2 col-12" style="display: none;">
							<hr>
							<div class="price-sum btn btn-block mb-2">總額：＄<span id="price-sum"></span></div>
							<form id="billing-form" action="{{route('bill.store')}}" method="POST">
								{{csrf_field()}}
								<input type="hidden" value="0" name="carrier_id">
								<input id="item-input" type="text" name="item[]" style="display: none;">
								<input id="quantity" type="number" name="quantity[]" style="display: none;">
								<input type="text" name="ship_name" value="*" style="display: none;">
								<input type="text" name="ship_phone" value="*" style="display: none;">
								<input type="text" name="ship_address" id="ship_address" style="display: none;">
								<input name="ship_time" class="radio" type="radio" name="time" value="*" checked style="display: none;">
								
								
								<div id="lastBtn" class="btn btn-block btn-success mb-2">上一步</div>
								
								<span>訂購人：</span>
								<input type="text" class="form-control" name="user_name" value="{{Auth::user()?Auth::user()->name:''}}">	

								<span>E-mail：</span>
								<input type="text" class="form-control" name="ship_email" value="{{Auth::user()?Auth::user()->email:''}}">	

								{{-- <span>希望發貨日：</span> --}}
								<select id="ship_ifDate" class="form-control d-none" name="ship_arrive">
									<option value="no">不指定</option>
									<option value="yes">指定</option>
								</select>
								<span style="display: none;" id="ship_date_notice"><font size="2">1.上班日(週一至週五)上午12點前之訂單並完成付款，可於隔日到貨。<br> 2.平日下午14:00之後的訂單，恕隔日無法到貨。<br> 3.星期五下午14:00之後至週日的訂單，於下週一開始出貨，週二到貨。<br></font></span>
								<input id="ship_date" type="date" name="ship_arriveDate" class="form-control" style="display: none;">


								<span>發票：</span>
								<select id="ship_receipt" name="ship_receipt" class="form-control">
									<option value="2">二聯</option>
									<option value="3">三聯</option>
								</select>

								<div class="ifThree" style="display: none;">
									<input id="ship_three_id" name="ship_three_id" type="text" class="shipping-ship_three_id form-control ship_three" placeholder="統一編號">
									<input id="ship_three_company" name="ship_three_company" type="text" class="shipping-ship_three_company form-control ship_three" placeholder="公司名稱">	
								</div>
								<span>備註：</span>
								<textarea name="ship_memo" class="shipping-ship_memo form-control"></textarea> 
								
								@if(Auth::user())
								<span>可用紅利（{{Auth::user()->bonus}}）：</span>
								<span id="myBonus" style="display: none;">{{Auth::user()->bonus}}</span>
								<input id="bonus-use" value="0" min="0" max="{{Auth::user()->bonus}}" name="bonus" type="number" class="form-control">
								@else
								<span>可用紅利（無）：</span><br>
								<span>使用紅利或累積紅利請登入會員</span><br>
								@endif

								<span><font color="c80013">＊付款方式：</font></span>
								<select name="ship_pay_by" class="form-control">
									<option value="">-</option>
									<option value="CREDIT">信用卡</option>
									<option value="ATM">ATM轉帳</option>
								</select>

								
								
							</form>
						</div>
						@if(!Auth::user())
						<div id="startBtn" class="btn btn-block mt-2 btn-success" style="z-index: 3;">開始填表</div>	
						<div id="nextBtn" class="btn btn-block mt-2" style="display: none;">下一步</div>	
						@else
						<div id="nextBtn" class="btn btn-block mt-2">下一步</div>	
						@endif
						<div id="submitBtn" class="btn btn-block mt-2" style="display: none;">確定送出</div>
					</div>
					

				</div>

			</div>


			


		</div>
	</div>
</div>
@if(!Auth::user())
<div class="beforeDiv" style="display: none;">
	<h3>＊若要累積<font color="#c80013">紅利點數</font>或使用<font color="#c80013">紅利折抵</font>請先<font color="#c80013">登入會員</font></h3>
	<div class="btn-row">
		<div id="no-thanks-btn" class="btn btn-primary">不了，我要直接送禮</div>
		<a class="btn btn-success" href="/login">好，立馬登入會員</a>
	</div>
</div>
@endif

<div class="alert-field" style="display: none;">
	
</div>

@endsection

@section('scripts')
<script>
	const products = {!! $products !!};
	var productPrice = null;
</script>
{{ Html::script('js/_sendGift.js') }}
@endsection