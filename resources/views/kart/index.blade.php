@extends('main')

@section('title','| 我的購物車')

@section('stylesheets')
{{Html::style('css/_kart_0820_1.css')}}
{{Html::style('css/_process.css')}}
<style>
	.contentPage{
	    width: 100%;
	    height: auto;
	}

	.bind-product-outter {
		background-color: rgba(255,255,255,0.5);
		box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
		border-radius: 0 0 0.3em 0.3em;
	}

	.bind-product-title {
		background-color: #f0ad4e;
		border-radius: 0.3em 0.3em 0 0;
		z-index: 9;
	}

	.bind-product {
		background-color: rgba(0,0,0,0.1);
		border-radius: 0.3em;
	}

	.bind-product img {
		height: 100%;
    	max-width: 100%;
    	max-height: 100%;
    	border-radius: 0.2rem;
	}

	.outter{
		margin-top: 32px;
		margin-bottom: 60px;
		/*min-height: 520px;*/
		min-height: 60vh;
		/*overflow-y: scroll;*/
		padding-bottom: 140px;
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
		cursor: pointer;
		background-color: #d9534f;
		box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
		border:none;
		color: #fff;
		outline: none;
	}
	#payBtn{
		/* float: right; */
		box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
		cursor: pointer;
		width: 160px;
		height: 80px;
		font-size: 22px;
		line-height: 80px;
		padding: 0;
		text-align: center;
		background-color: #ec971f;
		border-radius: 0.25rem;
		color: #fff;
	}
	.delBtn:hover,#payBtn:hover{
		box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.5);
	}
</style>
@endsection

@section('content')

<div class="contentPage">
	<div class="container">

		<div class="row">
			<div class="col-lg-10 offset-lg-1 col-12 outter">
				
				@if(count($products) == 0)
					<div style="position: absolute;top: 50%;transform: translateY(-50%);width: calc(100% - 30px);text-align: center;">
						<h1 style="">您的購物車中目前沒有商品</h1>	
					</div>
				@else
				<ul class="process">
					<li class="process-4">
						<div class="process-bg process-1 processing"></div>
						<img src="{{asset('images/step-1-1.png')}}">
					</li>
					<li class="process-g">
						<img src="{{asset('images/arrow-right.png')}}">
					</li>
					<li class="process-4">
						<div class="process-bg process-2"></div>
						<img src="{{asset('images/step-1-2.png')}}">
					</li>
					<li class="process-g">
						<img src="{{asset('images/arrow-right.png')}}">
					</li>
					<li class="process-4">
						<div class="process-bg"></div>
						<img src="{{asset('images/step-1-3.png')}}">
					</li>
					<li class="process-g">
						<img src="{{asset('images/arrow-right.png')}}">
					</li>
					<li class="process-4">
						<div class="process-bg"></div>
						<img src="{{asset('images/step-1-4.png')}}">
					</li>
				</ul>
				<ul class="process">
					<il class="process-4"><p>STEP.1</p><p>放入購物車</p></il>
					<il class="process-g">　</il>
					<il class="process-4"><p>STEP.2</p><p>填寫寄送資料</p></il>
					<il class="process-g">　</il>
					<il class="process-4"><p>STEP.3</p><p>結帳付款</p></il>
					<il class="process-g">　</il>
					<il class="process-4"><p>STEP.4</p><p>完成，貨物送出</p></il>
				</ul>

				<p>　</p>
				<h3 id="h-title" style="text-align: center;margin: 0 0 10px 0;">我的購物車</h3>

				<form class="kartForm" action="{{route('bill.store')}}" method="POST">
					{{csrf_field()}}
					<table class="kartTable">	
						<tr class="product-title-TR">
							<th></th>
							<th class="product-name-TH">商品名稱</th>
							<th>數量</th>
							<th>價格</th>
							<th></th>
						</tr>
						@foreach($products as $product)
						<tr class="product-TR" id="item{{$product->id}}">
							<td class="product-img-TD">
								<div>
									<img class="littleIMG" src="{{asset('images/productsIMG') . '/' . $product->image}}" alt="">
								</div>
							</td>

							<td class="product-name-TD">
								<span>{{$product->name}}</span>
								<input style="display: none;" type="text" value="{{$product->slug}}" name="item[]">
							</td>

							
							<td class="product-quantity-TD">
								@if(in_array($product->category_id, [12]))
									<span>1</span>
									<input hidden 
										id="{{$product->slug}}"
										data-id="{{$product->id}}"
										data-price="{{$product->price}}"
										class="quantity"
										type="number"
										value="1"
										name="quantity[]">
								@else
									<input 
										id="{{$product->slug}}"
										data-id="{{$product->id}}"
										data-price="{{$product->price}}"
										class="quantity quantity-input-{{$product->id}}"
										type="number"
										min="1"
										value="1"
										name="quantity[]">
								@endif
							</td>

							<td class="product-price-TD">
								<span class="priceTag" id="priceTag{{$product->slug}}">{{$product->price}}</span>
							</td>

							<td class="product-del-TD">
								<div class="delBtn" data-method="delete" onclick="deleteWithAjax({{$product->id}})">刪除</div>
							</td>
						</tr>
						@endforeach

						<tr id="transport-fee">
							<td class="product-TR"></td>
							<td class="product-name-TD">
								運費(未滿799)
								<input id="transport-item" style="display: none;" type="text" value="99999" name="item[]">
							</td>
							<td class="product-quantity-TD">
								<input id="transport-quantity" style="display: none;" type="number" value="1" name="quantity[]">
							</td>
							<td class="product-price-TD">
								<span>150</span>
							</td>
							<td></td>
						</tr>

					</table>	
					
					<table class="shipping">

						{{-- <tr class="family-column" style="display: none">
							<td>
								<label for=""></label>
								<span style="font-size: 14px;" class="shipping-ship_email">(全家取貨需出示身份證核對資料)</span>
							</td>
						</tr> --}}

						<tr style="border-bottom: 1px solid darkgrey;">
							<td>
								<label for="">　常用資料：</label>
								<!-- 快速選擇模式 -->
								<div id="recipient_selection_mode" class="d-inline-block mt-4">
									<select id="quick_recipient_select" name="favorite_address" class="form-control" onchange="onQuickRecipientChange()" style="margin-bottom: 10px;">
										@foreach ($addresses as $address)
											@if($address->ship_name)
											<option value="{{$address->id}}" 
												data-county="{{$address->county}}" 
												data-district="{{$address->district}}" 
												data-address="{{$address->address}}"
												data-ship-name="{{$address->ship_name}}"
												data-ship-phone="{{$address->ship_phone ?? ''}}"
												data-ship-email="{{$address->ship_email ?? ''}}"
												data-ship-receipt="{{$address->ship_receipt ?? ''}}"
												data-ship-three-id="{{$address->ship_three_id ?? ''}}"
												data-ship-three-company="{{$address->ship_three_company ?? ''}}"
												data-ship-gender="{{$address->ship_gender ?? ''}}"
											>{{$address->ship_name}} - {{$address->county}}{{$address->district}}{{$address->address}}</option>
											@endif
										@endforeach
										<option value="">手動輸入新資料</option>
									</select>
								</div>
							</td>
						</tr>

						<tr>
							<td></td>
						</tr>
						<tr>
							<td>
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
								<label for=""></label>
								<span style="font-size: 14px;" class="shipping-ship_email">(電子發票將寄送至此信箱)</span>
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
							<td class="">
								<label for=""><span class="required">*</span>運送方式：</label>
								<span>冷凍宅配</span>
								<input type="hidden" value="0" name="carrier_id">
								{{-- <select id="shipping-carrier" name="carrier_id" class="shipping-carrier form-control">
									@foreach ($carriers as $id => $name)
									<option value="{{$id}}">{{$name}}</option>
									@endforeach
								</select> --}}
							</td>
						</tr>

						<tr class="family-column" style="display: none">
							<td>
								<label for="">全家冷凍超取</label>
								<div class="btn btn-primary" onclick="call_windows()">選擇門市</div>
							</td>
						</tr>
						
						<tr class="family-column" style="display: none">
							<td>
								<label for=""><span class="required">*</span>門市代號：</label>
								<input class="shipping-store form-control" type="text" name="store_number" readonly>
							</td>
						</tr>
						<tr class="family-column" style="display: none">
							<td>
								<label for=""><span class="required">*</span>門市名稱：</label>
								<input class="shipping-store form-control" type="text" name="store_name" readonly>
							</td>
						</tr>
						<tr class="family-column" style="display: none">
							<td>
								<label for=""><span class="required">*</span>門市地址：</label>
								<input class="shipping-store form-control" type="text" name="store_address" readonly>
							</td>
						</tr>

						<tr class="blackcat-column" id="address_selection_row">
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
								<label class="align-top" for=""></label>
							</td>
						</tr>
						<tr>
							<td>
								<label class="align-top" for=""></label>
								<span>⭐ 感謝您對金園的長期支持</span>
							</td>
						</tr>
						<tr>
							<td>
								<label class="align-top" for=""></label>
								<span>一年一度的《雙11紅利點數3倍送》</span>
							</td>
						</tr>
						<tr>
							<td>
								<label class="align-top" for=""></label>
								<span>希望能滿足您期待</span>
							</td>
						</tr>
						<tr>
							<td>
								<label class="align-top" for=""></label>
								<span>因豬肉供應不足，無法即時出貨，敬請見諒</span>
							</td>
						</tr>
						<tr>
							<td>
								<label class="align-top" for=""></label>
								<span>請選擇您可接受的出貨選項再下訂單</span>
							</td>
						</tr>
						<tr>
							<td>
								<label class="align-top" for=""></label>
							</td>
						</tr>

						<tr>
							<td>
								<label class="align-top" for="">　出貨時間：</label>
								{{-- <input name="ship_time" class="radio" type="radio" name="time" value="no" checked><span>不指定</span>
								<input id="1300" name="ship_time" class="radio" type="radio" name="time" value="13:00"><span>13:00前</span>
								<input id="1400-1800" name="ship_time" class="radio" type="radio" name="time" value="14:00-18:00"><span>14:00-18:00</span> --}}
								<?php 
									$options = [
										"排骨訂單50片以下，優先出貨",
										"排骨訂單50片以上，出貨前電話通知",
										"排骨訂單180片以上，11/20後出貨"
									];
								?>
								<div class="d-inline-block">
									{{-- <div>
										<span>感謝您對金園的長期支持，一年一度的《雙11紅利點數3倍送》滿足您期待，因豬肉供應不足，無法即時出貨，敬請見諒，請選擇您可接受的出貨選項再下訂單</span>
									</div> --}}
									@foreach ($options as $i => $option)
									<div>
										<input class="radio" type="radio" name="ship_time" value="{{$option}}" {{$i == 0 ? 'checked' : ''}}><span>{{$option}}</span>
									</div>	
									@endforeach
									{{-- <div>
										<input class="radio" type="radio" name="ship_time" value="隨時可出貨" checked><span>隨時可出貨</span>
									</div>
									<div>
										<input class="radio" type="radio" name="ship_time" value="出貨前電話通知我"><span>出貨前電話通知我</span>
									</div>
									<div>
										<input class="radio" type="radio" name="ship_time" value="請等我通知日期再出貨"><span>請等我通知日期再出貨</span>
									</div> --}}
								</div>

							</td>
						</tr>

						<tr>
							<td>
								<label class="align-top" for=""></label>
								<span>⭐ 訂購其他商品不受限制</span>
							</td>
						</tr>

						<tr>
							<td>
								<label for="">　發票：</label>
								<select id="ship_ship_receipt" name="ship_receipt" class="shipping-ship_receipt two-three form-control">
									<option id="two" value="2">二聯</option>
									<option id="three" value="3">三聯</option>
								</select>
								<div class="ifThree">
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
									<input id="pay_by_credit" class="radio" type="radio" name="ship_pay_by" value="CREDIT"><span>信用卡</span>
									<input id="pay_by_atm" class="radio" type="radio" name="ship_pay_by" value="ATM"><span>ATM</span>
									<input id="pay_by_cod" class="radio blackcat-column" type="radio" name="ship_pay_by" value="cod"><span class="blackcat-column">貨到付款</span>
									{{-- <input id="pay_by_family" class="radio family-column" type="radio" name="ship_pay_by" value="FAMILY" style="display: none"><span class="family-column" style="display: none">全家取貨付款</span> --}}
								</div>
							</td>
						</tr>

						<!-- 保存為常用地址選項 -->
						<tr id="add_favorite_address">
							<td>
								<label for=""></label>
								<div id="" style="display: inline-block">
									<span style="vertical-align: middle" style="display: inline-block">設為常用收件人</span>
									<input name="add_favorite" type="checkbox" class="form-control" style="width: 24px; height:24px; vertical-align: middle">
								</div>
								<input id="use_favorite_address" class="d-none" type="checkbox" name="use_favorite_address">
							</td>
						</tr>

						{{-- @if(Auth::check()) --}}
						<!-- 信用卡相關選項 -->
						{{-- <tr class="credit_card_options" style="display: none;">
							<td>
								@if(Auth::user()->creditCards()->active()->count() > 0)
									<label for="">　選擇卡片：</label>
									<div class="d-inline-block">
										
										<div class="saved_cards">
											<input id="use_new_card" class="radio" type="radio" name="credit_card_option" value="new" checked>
											<span>使用新信用卡</span>
											@foreach(Auth::user()->creditCards()->active()->orderBy('is_default', 'desc')->get() as $card)
												<div class="mt-1">
													<input id="use_saved_card_{{ $card->id }}" class="radio" type="radio" name="credit_card_option" value="saved">
													<input type="hidden" name="use_saved_card" value="{{ $card->id }}">
													<span>{{ $card->masked_card_number }} ({{ $card->card_alias }})
														@if($card->is_default) <small class="text-success">預設</small> @endif
													</span>
												</div>
											@endforeach
										</div>
									</div>
								@endif
								
							</td>
						</tr>
						<tr class="credit_card_options" style="display: none;">
							<td>
								<label for=""></label>
								<div class="d-inline-block">
									<a href="{{ route('creditCard.index') }}" target="_blank" class="btn btn-sm btn-outline-primary">
										管理我的信用卡
									</a>
								</div>
							</td>
						</tr> --}}
						{{-- <tr class="credit_card_options" style="display: none;">
							<td>
								<label for=""></label>
								<div id="save_card_option" class="d-inline-block">
									<input type="checkbox" id="save_credit_card" name="save_credit_card" value="1">
									<span>儲存此次結帳信用卡資訊，下次結帳更便利</span>
									<div style="font-size: 12px; color: #666; margin-top: 5px;">
										※ 我們僅儲存卡號前六後四碼用於識別，不會儲存完整卡號資訊
									</div>
								</div>
							</td>
						</tr> --}}
						{{-- @endif --}}


					</table>
					
					
					<div style="background: rgba(255,255,255,0.3); height: 1.5pt;"></div>

					<div class="d-flex justify-content-end priceSum mt-2">
						<span style="font-size: 18pt">總額：</span>
						<span style="margin: 0 8px 0 8px;font-size: 18pt" id="total-price-span"></span>
					</div>

					<div class="sure-to-buy-div">
						<div class="d-flex justify-content-end mt-2 pl-2 pr-2 pb-2">
							<div onclick="sureToBuy()" class="sureToBuy btn btn-primary">
								確定購買
							</div>
						</div>
					</div>

					<div class="check-out-form-div" style="display: none">
						<div class="d-flex justify-content-between mt-2 pl-2 pr-2 pb-2">
							{{-- <div class="back-shop btn btn-success mr-2" onclick="location.href='/productCategory/1'">繼續購物</div> --}}
							<div>
								<div onclick="back_kart();" id="back-kart" style="position: absolute; bottom: 0" class="btn btn-primary align-bottom">回購物車</div>
							</div>
							
							<div onclick="checkForm();" id="payBtn" class="ml-3">送出訂單</div>
						</div>
					</div>

				</form>
				

				@if (count($bindedProducts) > 0)
				
				<hr>
				<div class="bind-product-title mt-2 p-2">
					<h5 class="m-0 text-white">目前已滿足加價購條件</h5>
				</div>
				
				<div class="bind-product-outter pt-3 pl-3 pr-3 pb-2">
					
					@foreach ($bindedProducts as $p)
					<div class="mb-2 pl-2 pr-2 pt-2 pb-2 bind-product">
						<div class="d-inline-block align-middle" style="height: 80px; width: 80px;">
							<img src="{{asset('images/productsIMG') . '/' . $p->image}}" alt="">
						</div>
						<div class="d-inline-block align-middle ml-2 w-auto" style="height: 80px;">
							<div class="d-flex flex-column justify-content-between h-100">
								<span class="d-block h-50">{{$p->name}}</span>
								<div class="h-50">
									<span class="text-danger">${{$p->price}}</span>
								</div>
							</div>
						</div>
						<div class="mt-2 mr-2" style="height: 80px; position: absolute; right:0; top: 0">
							<button class="btn btn-warning h-100" style="cursor: pointer" onclick="addToKart({{$p->id}})">
								加購
							</button>
						</div>
					</div>	
					@endforeach	
				</div>
				
				@endif

				@endif
				<div class="alert-field"></div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
{{ Html::script('js/_kart_0820_1.js') }}
{{-- {{ Html::script('js/_family.js') }} --}}
<script src="{{ asset('js/checkout-funnel-tracker.js') }}"></script>

<script>


	// 加購條件
	const relation = {!!json_encode($relation)!!};

	function addToKart(id){
		$.ajax({
			type:'POST',
			url:'{{route('kart.store')}}',
			dataType:'json',
			data: {
				'product_id':id,
			},
			success: function (response) {
                location.reload();
            },
            error: function (error) {
				console.log(error);
            }
		});
	}

	// 信用卡選項控制
	// $(document).ready(function() {
	// 	// 監聽付款方式變更
	// 	$('input[name="ship_pay_by"]').change(function() {
	// 		if ($(this).val() === 'CREDIT') {
	// 			$('.credit_card_options').show();
	// 		} else {
	// 			$('.credit_card_options').hide();
	// 		}
	// 	});

		// 監聽信用卡選擇變更
		// $('input[name="credit_card_option"]').change(function() {
		// 	if ($(this).val() === 'new') {
		// 		$('#save_card_option').show();
		// 		// 清除已選擇的儲存卡片
		// 		$('input[name="use_saved_card"]').prop('checked', false);
		// 	} else if ($(this).val() === 'saved') {
		// 		$('#save_card_option').hide();
		// 		// 設定選中的儲存卡片
		// 		var cardId = $(this).siblings('input[name="use_saved_card"]').val();
		// 		$('input[name="use_saved_card"]').val(cardId);
		// 	}
		// });
	// });
</script>
@endsection