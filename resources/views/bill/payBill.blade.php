@extends('main')

@section('title','| 付款')

@section('stylesheets')
<style>
.outter{
	margin-top: 60px;
	margin-bottom: 60px;
	min-height: 520px;
	/*overflow-y: scroll;*/
	padding-bottom: 100px;
	background-color: rgba(255,255,255,0.5);
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	border-radius: 0.3em;
}
.billTable{
	border:1pt solid rgba(0,0,0,0.3);
	border-radius: 0.3em;
	margin-top: 20px;
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
	bottom: 20px;
	left: 0;
	padding: 10px 0 10px 0;
}
.inner-payBy{
	width: 20%;
	left: 50%;
	transform: translateX(-50%);
	text-align: center;
}
.payByBtn{
	width: 100%;
}
/* tnak you part*/
.thankU{
	/*border:1pt solid #000;*/
	width: 100%;
	height: 260px;
	margin-top: 20px;
}
.U-1{
	/*border:1pt solid #000;*/
	width: 100%;
	height: 33.33333%;
	text-align: center;
	padding: 12px 20px 0 20px;
}
.loader-bg{
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0,0,0,0.5);
	z-index: 9999999998;
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
{{Html::style('css/_process.css')}}
@endsection

@section('content')


<div class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2 outter">

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
						<div class="process-bg processing"></div>
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
				
				<div class="thankU">
					<div class="U-1">
						<font style="font-size: 32pt;
						font-weight: 500;
						letter-spacing: 4px;
						">
						@if($finalBill['pay_by'] == '貨到付款')
						　感謝您的購買~
						@elseif($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] ==1)
						　感謝您的購買~
						@elseif($finalBill['pay_by'] == 'ATM')
						ATM轉帳繳費
						@elseif($finalBill['pay_by'] == 'CREDIT')
						信用卡繳費
						@endif
						</font>
					</div>
					<div class="U-1">
						@if($finalBill['pay_by'] == '貨到付款')
						<img style="height: 70%;" src="{{asset('images/thankYou.png')}}" alt="">
						@elseif($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] ==1)
						<img style="height: 70%;" src="{{asset('images/thankYou.png')}}" alt="">
						@elseif($finalBill['pay_by'] == 'ATM')
						<img style="height: 70%;" src="{{asset('images/atm.png')}}" alt="">
						@elseif($finalBill['pay_by'] == 'CREDIT')
						<img style="height: 70%;" src="{{asset('images/credit.png')}}" alt="">
						@endif
						
					</div>
					<div class="U-1">
						@if($finalBill['pay_by'] == '貨到付款')
						<font>我們衷心感謝您購買我們的產品，您將會收到一封電子確認信，內含您的購買明細。<br>若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。</font>
						@elseif($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] ==1)
						<font>我們衷心感謝您購買我們的產品，您將會收到一封電子確認信，內含您的購買明細。<br>若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。</font>
						@elseif($finalBill['pay_by'] == 'ATM')
						<font>取得繳費帳號後您將收到一封電子確認信，內含您的購買明細及繳款資訊，<br>
						商品會於繳款確認後寄出，若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。</font>
						@elseif($finalBill['pay_by'] == 'CREDIT')
						請確認訂單資訊正確無誤後點擊“前往繳費”<br>若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。
						@endif
					</div>
				</div>

				<div class="billTable">
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
							<th>紅利折扣</th>
							<th>總金額</th>
						</tr>
						<tr>
							<td class="TDdate">{{$finalBill['bill_id']}}</td>
							<td class="TDproduct">
								<table style="width: 100%;">
									
									@foreach($finalBill['itemArray'] as $item)
										<tr>
											<td class="TNT1">{{$item['name']}}</td>
											<td class="TNT2">{{$item['price']}}</td>
											<td class="TNT3">{{$item['quantity']}}</td>
										</tr>
									@endforeach
									
								</table>
							</td>
							<td>{{$finalBill['bonus_use']}}</td>
							<td class="TDtotal">{{$finalBill['price']}}</td>
						</tr>
						<tr><td>　</td><td>　</td><td>　</td></tr>
					</table>
				</div>

			<div class="payBy">
				<div class="inner-payBy">
					@if($finalBill['pay_by'] == 'ATM' && $finalBill['SPToken'] != null)
					<button class="payByBtn btn btn-primary" onclick="checkOut('ATM')">取得繳費帳號</button>
					

					@elseif($finalBill['pay_by'] == '貨到付款')
					<a href="{{url('/')}}" style="color: white;" class="payByBtn btn btn-success">回首頁</a>
					@elseif($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] ==1)
					<a href="{{url('/')}}" style="color: white;" class="payByBtn btn btn-success">回首頁</a>
					@elseif($finalBill['pay_by'] == 'CREDIT')
					<button class="payByBtn btn btn-primary" onclick="creditSubmit();">前往繳費</button>
					@endif
				</div>
			</div>

			</div>
		</div>
	</div>

@if($finalBill['pay_by'] == 'CREDIT')
<form class="creditForm" style="display: none;" method=post action="https://epost.hncb.com.tw/ezpostw/auth/SSLAuthUI.jsp"> 
<INPUT value="6940" name=merID><br>
<INPUT value="金園排骨股份有限公司" name=MerchantName><br>
<INPUT value="008786350353296" name=MerchantID><br>
<INPUT value="77543256" name=TerminalID><br>
<INPUT maxLength=100 size=50 name="AuthResURL" value="http://45.76.104.218/api/creditPaied"><!-- (optional, 亦可不使用本參數)  --><br>
<INPUT value="{{$finalBill['bill_id']}}" name=lidm><br>
<INPUT onclick=chkTxType(); type=radio value=0 name=txType checked><br>
<INPUT type=radio value=0 name=AutoCap CHECKED><br>
<INPUT size=3 value="{{$finalBill['price']}}" name=purchAmt><!-- 金額 --><br>
<input type="text" value="UTF-8" name="encode"><br>
<INPUT NAME=checkValue Value="{{$checkValue}}" > <br>
<INPUT onclick=doSubmit(); type=submit value="Pay by credit card" border=0 name=imageField height="32" width="161">  <br>
</form>
@endif

</div>
@endsection

@section('scripts')

@if($finalBill['pay_by'] == 'ATM' AND $finalBill['SPToken'] != null)
	<script src="https://payment-stage.ecpay.com.tw/Scripts/SP/ECPayPayment_1.0.0.js"
	data-MerchantID="2000132" {{-- test --}}
	{{-- data-MerchantID="1044372" --}} {{-- kingpork --}}
	data-SPToken="{{$finalBill['SPToken']}}"
	data-PaymentType="ATM"
	data-PaymentName="CREDIT"
	data-CustomerBtn="1" >
	</script> 
	<script>
		$(document).ready(function (){
			window.addEventListener('message', function (e) {
				var json = JSON.parse(e.data);
				// alert(e.data);
				// alert(json.MerchantTradeNo);
				if (json.RtnCode == '2') {
					$('body').append('<div class="loader-bg"></div>');
					$('body').append('<div class="loader-box"><div class="loader"></div><strong>請稍候...</strong></div>');
					$.ajaxSetup({
				  		headers: {
				    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
				  		}
					});
					$.ajax({
						type:'POST',
						url:'{{route('bill.sendMail')}}',
						dataType:'json',
						data: {
							'MerchantID':json.MerchantID,
							'MerchantTradeNo':json.MerchantTradeNo,
							'TradeNo':json.TradeNo,
							'TradeAmt':json.TradeAmt,
							'TradeDate':json.TradeDate,
							'BankCode':json.BankCode,
							'vAccount':json.vAccount,
							'ExpireDate':json.ExpireDate,
						},
						success: function (response) {
							if (response == 's') {
								$('.loader').remove();
								$('.loader-box').remove();
								$('.loader-bg').remove();
								$('.payByBtn').remove();
								$('.inner-payBy').append('<a href="/" style="color: white;" class="payByBtn btn btn-success">回首頁</a>');
								setTimeout(function(){
									alert('電子確認信已寄出，內含您的購買明細及繳款資訊');
								},10)
							}
						},
						error: function () {
				            alert('錯誤');
				        },
					});
				}
				else if (json.RtnCode == '10200165') {
					$('.payByBtn').remove();
					$('.inner-payBy').append('已取得繳費代碼，<br>請留意電子信箱。');
				}
				else if (json.RtnCode == '10200164') {
					$('.payByBtn').remove();
					$.ajaxSetup({
				  		headers: {
				    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
				  		}
					});
					$.ajax({
						type:'POST',
						url:'/bill/{{$finalBill['bill_id']}}',
						dataType:'json',
						data: {
							_method: 'delete',
						},
						success: function (response) {
							if (response == '1') {
								$('.inner-payBy').append('取得代碼逾時，<br>此訂單將被刪除。');
								setTimeout(function(){
									window.location.href = '{{route('bill.index')}}';
								},3000);
							}else if (response == 's') {
								$('.inner-payBy').append('已取得繳費代碼，<br>請留意電子信箱。');
							}
							else{
								alert('錯誤');
							}
						},
						error: function () {
				            alert('錯誤');
				        },
					});
				}

			});
		});
	</script>
@endif

@if($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] !=1)
<script>
	function creditSubmit(){
		$('.creditForm').submit();
	}
</script>
@elseif($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] ==1)
<script>
	$.ajaxSetup({
			headers: {
		 		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
			}
		});
	$.ajax({
		type:'POST',
		url:'{{route('bill.sendMailC')}}',
		dataType:'json',
		data: {
			'bill_id':'{{$finalBill['bill_id']}}',
		},
		success: function (response) {
			if (response == 's') {
				alert('電子確認信已寄出，內含您的購買明細。');
			}
		},
		error: function () {
	         alert('錯誤');
	     },
	});
</script>
@endif

@endsection