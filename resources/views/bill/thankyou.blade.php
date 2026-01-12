@extends('main')

@section('title','| 購買成功')


@section('stylesheets')
{{Html::style('css/_payBill.css')}}
{{Html::style('css/_process.css')}}
@endsection

@section('content')


<div class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 offset-lg-2 col-12 outter">

				<ul class="process">
					<li class="process-4">
						<div class="process-bg process-1"></div>
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
						<div class="process-bg processing"></div>
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
				
				<div style="text-align: center;">
					<div style="margin-top:24px;">
						<h1>感謝您的購買~</h1>
					</div>
					
                    <div style="height: 56px">
                        <img style="height: 70%;" src="{{asset('images/thankYou.png')}}">
                    </div>
					<div>
						<font>我們衷心感謝您購買我們的產品。<br>若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。</font>
					</div>
				</div>

				<div class="billTable">
					<table style="width: 100%">	

						<tr class="product-title-TR">
							<th>
								<table style="width: 100%;">
									<tr>
										<td class="TNT1">產品</td>
										<td class="TNT2">價格</td>
										<td class="TNT3">數量</td>
									</tr>
								</table>
							</th>
							<th>紅利折扣</th>
							@if ($bill->promo_discount_amount > 0)
                            <th>優惠折扣</th>
                            @endif
							<th>總金額</th>
						</tr>
						<tr>
							<td class="TDproduct">
								<table style="width: 100%;">
									
									@foreach($products as $product)
										<tr class="product-TR">
											<td class="TNT1">{{$product->name}}</td>
											<td class="TNT2">{{$product->price}}</td>
											<td class="TNT3">{{$product->quantity}}</td>
										</tr>
									@endforeach
									
								</table>
							</td>
							<td>{{$bill->bonus_use}}</td>
							@if ($bill->promo_discount_amount > 0)
                            <td>{{$bill->promo_discount_amount}}</td>
                            @endif
							<td class="TDtotal">{{$bill->price}}</td>
						</tr>
					</table>
				</div>

			<div class="payBy">
				<div class="inner-payBy">
					<a href="/bill" style="color: white;" class="payByBtn btn btn-success">我的訂單</a>
				</div>
			</div>

			</div>
		</div>
	</div>

</div>
@endsection



@section('scripts')
@if($gaData && config('app.env') === 'production' && config('app.ga_id'))
<script>
// GA4 事件追蹤 - 根據付款方式區分處理
if (typeof gtag !== 'undefined') {
    gtag('event', 'purchase', {
        transaction_id: '{{ $gaData['ecommerce']['transaction_id'] }}',
        value: {{ $gaData['ecommerce']['value'] }},
        currency: '{{ $gaData['ecommerce']['currency'] }}',
        items: json_encode($gaData['ecommerce']['items'])
    });
}
</script>
@endif
@endsection
