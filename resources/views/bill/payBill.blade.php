@extends('main')

@section('title','| 付款')

@section('stylesheets')
<style>
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
.TDdate{
	width: 220px;
}
.TDtotal{
	width: 56px;
}
.TDproduct{
	width: calc(100% - 276px);
}
.TNT2,.TNT3{
	width: 56px;
}
.TNT1{
	width: calc(100% - 112px);
	text-align: left;
}
th{
	padding-top: 12px;
	padding-bottom: 12px;
}
td,th{
	text-align: center;
	vertical-align: middle;
}
.payBy{
	position: absolute;
	width: 100%;
	bottom: 0;
	left: 0;
	padding: 10px 0 10px 0;
}
.payByATM{
	left: 50%;
	transform: translateX(-50%);
}
</style>
@endsection

@section('content')


<div class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2 outter">
				
				{{-- @if(Session::has('success'))
					{{Session::get('success')}}<br>
				@endif --}}

				<table style="width: 100%">	

					<tr>
						<th>訂單編號</th>	
						<th>
							<table style="width: 100%;">
								<tr>
									<td class="TNT1">產品</td>
									<td class="TNT2">價格</td>
									<td class="TNT3">數量</td>
								</tr>
							</table>
						</th>
						<th>總價</th>
					</tr>

					

						<tr>

							<td class="TDdate">{{$bill->bill_id}}</td>
							<td class="TDproduct">
								<table style="width: 100%;">
									
									@foreach($payItems as $payItem)
										<tr>
											<td class="TNT1">{{$payItem['name']}}</td>
											<td class="TNT2">{{$payItem['price']}}</td>
											<td class="TNT3">{{$payItem['quantity']}}</td>
										</tr>
									@endforeach
									
								</table>
							</td>
							<td class="TDtotal">{{$bill->price}}</td>
						</tr>
						<tr><td>　</td></tr>


				</table>

			<div class="payBy">
				<button class="payByATM btn btn-primary" onclick="checkOut('CREDIT')">ATM付款</button> 
			</div>

			</div>
		</div>
	</div>
</div>





@endsection

@section('scripts')
	<script src="https://payment-stage.ecpay.com.tw/Scripts/SP/ECPayPayment_1.0.0.js"
	data-MerchantID="2000132" {{-- test --}}
	{{-- data-MerchantID="1044372" --}} {{-- kingpork --}}
	data-SPToken="{{$SPToken}}"
	data-PaymentType="CREDIT"
	data-PaymentName="CREDIT"
	data-CustomerBtn="1" >
	</script> 

	<script type="text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js" >
	    $(function () {
	        window.addEventListener('message', function (e) {
	            alert("訂單結果資訊：" + e.data);
	                    //自行撰寫接收交易結果後續程式         
	            });     
	        });    
	</script> 



@endsection