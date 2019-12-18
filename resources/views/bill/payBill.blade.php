@extends('main')

@section('title','| 付款')

@section('dataLayer')
	<script>
		var d = {!!$dataLayer!!};
		window.dataLayer = window.dataLayer || [];
        window.dataLayer.push(d);
	</script>
	
@endsection

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
						<div class="process-bg {{($finalBill['status']==0 && $finalBill['pay_by'] != '貨到付款')?'processing':''}}"></div>
						<img src="{{asset('images/step-1-3.png')}}">
					</li>
					<li class="process-g">
						<img src="{{asset('images/arrow-right.png')}}">
					</li>
					<li class="process-4">
						<div class="process-bg {{($finalBill['status']==1 || $finalBill['pay_by'] == '貨到付款') ? 'processing' : ''}}"></div>
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
					<div class="U-1 U-title">
						<font>
						@if($finalBill['pay_by'] == '貨到付款')
						　感謝您的購買~
						@elseif($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] ==1)
						　感謝您的購買~
						@elseif($finalBill['pay_by'] == 'ATM')
						ATM轉帳繳費
						@elseif($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status']!=1)
						信用卡繳費
						@endif
						</font>
					</div>
					<div class="U-1 U-img">
						@if($finalBill['pay_by'] == '貨到付款')
						<img style="height: 70%;" src="{{asset('images/thankYou.png')}}">
						@elseif($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] ==1)
						<img style="height: 70%;" src="{{asset('images/thankYou.png')}}">
						@elseif($finalBill['pay_by'] == 'ATM')
						<img style="height: 70%;" src="{{asset('images/atm.png')}}">
						@elseif($finalBill['pay_by'] == 'CREDIT'AND$finalBill['status']!=1)
						<img style="height: 70%;" src="{{asset('images/credit.png')}}">
						@endif
						
					</div>
					<div class="U-1 U-text">
						@if($finalBill['pay_by'] == '貨到付款')
						<font>我們衷心感謝您購買我們的產品，您將會收到一封電子確認信，內含您的購買明細。<br>若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。</font>
						@elseif($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] ==1)
						<font>我們衷心感謝您購買我們的產品，您將會收到一封電子確認信，內含您的購買明細。<br>若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。</font>
						@elseif($finalBill['pay_by'] == 'ATM')
						<font>取得繳費帳號後您將收到一封電子確認信，內含您的購買明細及繳款資訊，<br>
						商品會於繳款確認後寄出，若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。</font>
						@elseif($finalBill['pay_by']=='CREDIT'AND$finalBill['status']!=1)
						請確認訂單資訊正確無誤後點擊“前往繳費”<br>若您對此次交易有任何問題，請隨時<a href="{{route('contact')}}">寫信給我們</a>。
						@endif
					</div>
				</div>

				<div class="billTable">
					<table style="width: 100%">	

						<tr class="product-title-TR">
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
										<tr class="product-TR">
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
					</table>
				</div>

			<div class="payBy">
				<div class="inner-payBy">
					@if($finalBill['pay_by'] == 'ATM' && $finalBill['SPToken'] != null)

						<button class="payByBtn btn btn-primary" onclick="checkOut('ATM')">取得繳費帳號</button>

					@elseif($finalBill['pay_by'] == '貨到付款')

						<a href="{{url('/bill')}}" style="color: white;" class="payByBtn btn btn-success">我的訂單</a>

					@elseif($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] ==1)

						<a href="{{url('/bill')}}" style="color: white;" class="payByBtn btn btn-success">我的訂單</a>

					@elseif($finalBill['pay_by'] == 'CREDIT'AND$finalBill['status']!=1)

						<button class="payByBtn btn btn-primary" onclick="checkOut('CREDIT')">前往繳費</button>

					@endif
				</div>
			</div>

			</div>
		</div>
	</div>
{{-- @if($finalBill['pay_by'] == 'CREDIT')
<form class="creditForm" style="display: none;" method=post action="https://epost.hncb.com.tw/ezpostw/auth/SSLAuthUI.jsp"> 
<INPUT value="6940" name=merID><br>
<INPUT value="金園排骨股份有限公司" name=MerchantName><br>
<INPUT value="008786350353296" name=MerchantID><br>
<INPUT value="77543256" name=TerminalID><br>
<INPUT maxLength=100 size=50 name="AuthResURL" value="http://45.76.104.218/api/creditPaied"><br>
<INPUT value="{{$finalBill['bill_id']}}" name=lidm><br>
<INPUT onclick=chkTxType(); type=radio value=0 name=txType checked><br>
<INPUT type=radio value=0 name=AutoCap CHECKED><br>
<INPUT size=3 value="{{$finalBill['price']}}" name=purchAmt><br>
<input type="text" value="UTF-8" name="encode"><br>
<INPUT NAME=checkValue Value="{{$checkValue}}" > <br>
<INPUT onclick=doSubmit(); type=submit value="Pay by credit card" border=0 name=imageField height="32" width="161">  <br>
</form>
@endif --}}
</div>
@endsection



@section('scripts')


@if(($finalBill['pay_by']=='ATM'AND$finalBill['SPToken']!=null AND$finalBill['status']!=1)OR($finalBill['pay_by']=='CREDIT'AND$finalBill['SPToken']!=null AND$finalBill['status']!=1))

	{{-- <script src="https://payment-stage.ecpay.com.tw/Scripts/SP/ECPayPayment_1.0.0.js" --}} 		{{-- test --}}
	{{-- <script src="https://payment.ecpay.com.tw/Scripts/SP/ECPayPayment_1.0.0.js" --}}     {{-- production --}}
	<script
	{{-- data-MerchantID="2000132" --}} {{-- test --}}
	data-MerchantID="1044372" {{-- kingpork --}}
	data-SPToken="{{$finalBill['SPToken']}}"
	data-PaymentType="{{$finalBill['pay_by']}}"
	data-PaymentName="{{$finalBill['pay_by']}}"
	data-CustomerBtn="1" >
function PayBtn(ECPay) 
{
    var button = document.createElement("button");
    button.innerHTML = ECPay.dataPaymentName + "付款";
    button.type = "button";
    button.id = "Btn_Pay";
    button.setAttribute("style", "text-decoration: none;color: #ffffff;min-width: 150px;display: inline-block;padding: 10px 20px;border-radius: 5px;letter-spacing: 2px;margin: 15px 0;background-color: #3f3f3f;background-image: -webkit-gradient(linear, left top, left bottom, from(#3f3f3f), to(#000000));background-image: -webkit-linear-gradient(top, #3f3f3f, #000000);background-image:-moz-linear-gradient(top, #3f3f3f, #000000);background-image:-ms-linear-gradient(top, #3f3f3f, #000000);background-image:-o-linear-gradient(top, #3f3f3f, #000000);background-image:linear-gradient(top bottom, #3f3f3f, #000000);");
    button.setAttribute("onclick", "checkOut('" + ECPay.dataPaymentType + "');");
    ECPay.div.appendChild(button);
}
function BtnDecorator(ECPay) {
    var DivECPay = document.createElement("div");
    DivECPay.setAttribute("style", "z-index: 2147483646; display: none; background: rgba(0, 0, 0, 0.5);width: 100%; height: 100%; left:0%;top:0%; border: 0px none transparent; overflow-x: hidden; overflow-y: auto; visibility: visible;padding: 0px; -webkit-tap-highlight-color: transparent; position: fixed;");
    DivECPay.setAttribute("id", "DivECPay_" + ECPay.dataPaymentType);
    ECPay.div.appendChild(DivECPay);
    var iframebutton = document.createElement("button");
    iframebutton.setAttribute("id", "iframeECPayClose_" + ECPay.dataPaymentType);
    iframebutton.innerHTML = "X";
    iframebutton.type = "button";
    iframebutton.setAttribute("style", "z-index: 2147483647;display: none; position: fixed; right: 24%; top: 1%; margin-left:0px; margin-top:0px; background-color: #fff;  color: #6d6d6d;  width: 25px;  height: 25px;  border-radius: 5px;  font-size: 16px;  cursor: pointer;  box-shadow: 0 2px 0 0 black;  font-weight: bold;  -webkit-transition: 0.3s;  -moz-transition: 0.3s;  -o-transition: 0.3s;  -ms-transition: 0.3s;  transition: 0.3s;");
    iframebutton.setAttribute("onclick", "CloseIframe('" + ECPay.dataPaymentType + "')");
    ECPay.div.appendChild(iframebutton);
}
var
    version = "1.0.0",
    description = "綠界科技(ECPay)_ECPayPayment",
    //domain = "https://payment-stage.ecpay.com.tw";  //test
    domain = "https://payment.ecpay.com.tw";   //production
ECPay = {
    //### 初始化
    init: function () {
        this.getIsMobileAgent();
        this.getContainer();
        this.createPayButton();
        this.createModal();
    },
    getIsMobileAgent: function () {
        this.IsMobileAgent = false;
        var userAgent = navigator.userAgent;
        var CheckMobile = new RegExp("android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino");
        var CheckMobile2 = new RegExp("mobile|mobi|nokia|samsung|sonyericsson|mot|blackberry|lg|htc|j2me|ucweb|opera mini|mobi|android|iphone");
        if (CheckMobile.test(userAgent) || CheckMobile2.test(userAgent.toLowerCase())) {
            // this.IsMobileAgent = true;
            this.IsMobileAgent = false;
        }
    },
    //### 設定參數值
    getContainer: function () {
        var script = document.getElementsByTagName("script");
        script = script[script.length - 1];
        this.dataMerchantId = script.getAttribute("data-MerchantId");
        this.dataSPToken = script.getAttribute("data-SPToken");
        this.dataPaymentName = script.getAttribute("data-PaymentName");
        this.dataPaymentType = script.getAttribute("data-PaymentType");
        this.dataCustomerBtn = script.getAttribute("data-CustomerBtn");
        this.div = script.parentElement;
    },
    //### 建立Button按鈕
    createPayButton: function () {
        //使用客製化按鈕 不用幫忙創建按鈕
        if (ECPay.dataCustomerBtn == 1) {
            BtnDecorator(ECPay);
        }
        else if (ECPay.IsMobileAgent) {
            PayBtn(ECPay);
        }
        else {
            BtnDecorator(ECPay);
            PayBtn(ECPay);
        }
    },
    //### 初始化Iframe設定值
    createModal: function () {
        //如果是Pc才需創建Iframe
        if (!ECPay.IsMobileAgent) {
            var iframe = document.createElement("iframe");
            iframe.setAttribute("id", "iframeECPay_" + ECPay.dataPaymentType);
            iframe.frameborder = 0;
            iframe.allowtransparency = true;
            // iframe.setAttribute("style", "z-index: 2147483646; display: none; background: rgba(0, 0, 0, 0.00392157); border: 0px none transparent; overflow-x: hidden; overflow-y: auto; visibility: visible;padding: 0px; -webkit-tap-highlight-color: transparent; position: fixed; left: 0%; top: 10%; width: 100%; height: 80%;margin-left:0px;margin-top:0px;");
            iframe.setAttribute("style", "z-index: 2147483646; display: none; background: rgba(0, 0, 0, 0.00392157); border: 0px none transparent; overflow-x: hidden; overflow-y: auto; visibility: visible;padding: 0px; -webkit-tap-highlight-color: transparent; position: fixed; left: 0%; top: -10%; width: 100%; height: 120%;margin-left:0px;margin-top:0px;");


            //iframe.src = domain + "/SP/SPCheckOut?MerchantID=" + ECPay.dataMerchantId + "&SPToken=" + ECPay.dataSPToken + "&PaymentType=" + ECPay.dataPaymentType;			//!* test *!

            iframe.src = domain + "/SP/SPCheckOut?MerchantID=" + ECPay.dataMerchantId + "&SPToken=" + ECPay.dataSPToken + "&PaymentType=" + ECPay.dataPaymentType + "&ts=" + Date.now();			//!* production *!

            this.div.appendChild(iframe);
            this.modalBody = iframe;
            return;
        }
    }
}
function checkOut(Data) {
    if (ECPay.IsMobileAgent) {

        //var url = domain + "/SP/SPCheckOut?MerchantID=" + ECPay.dataMerchantId + "&SPToken=" + ECPay.dataSPToken + "&PaymentType=" + Data;				//!* test *!

        var url = domain + "/SP/SPCheckOut?MerchantID=" + ECPay.dataMerchantId + "&SPToken=" + ECPay.dataSPToken + "&PaymentType=" + Data + "&ts=" + Date.now();			//!* production *!

        window.open(url);
        return;
    }
    document.getElementById("iframeECPay_" + Data).style.display = "block";
    document.getElementById("iframeECPayClose_" + Data).style.display = "block";
    document.getElementById("DivECPay_" + Data).style.display = "block";   
}
function CloseIframe(Data) {
    document.getElementById("iframeECPay_" + Data).style.display = "none";
    document.getElementById("iframeECPayClose_" + Data).style.display = "none";
    document.getElementById("DivECPay_" + Data).style.display = "none";
}
//ECPay.init();							//!* test *!
window.onload = ECPay.init();		//!* production *!
	</script> 

{{-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  --}}

	<script>
		$(document).ready(function (){
			window.addEventListener('message', function (e) {
				var json = JSON.parse(e.data);
				// alert(e.data);
				
				
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
							'pay_by':'ATM',
						},
						success: function (response) {
							if (response == 's') {
								$('.loader').remove();
								$('.loader-box').remove();
								$('.loader-bg').remove();
								$('.payByBtn').remove();
								$('.inner-payBy').append('<a href="/bill" style="color: white;" class="payByBtn btn btn-success">我的訂單</a>');
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
				else if (json.RtnCode == '1') {
					$('body').append('<div class="loader-bg"></div>');
					$('body').append('<div class="loader-box"><div class="loader"></div><strong>請稍候...</strong></div>');
					$.ajaxSetup({
				  		headers: {
				    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
				  		}
					});
					$.ajax({
						type:'POST',
						url:'/bill/sendMail',
						dataType:'json',
						data: {
							'MerchantID':json.MerchantID,
							'MerchantTradeNo':json.MerchantTradeNo,
							'TradeNo':json.TradeNo,
							'TradeAmt':json.TradeAmt,
							'TradeDate':json.TradeDate,
							'pay_by':'CREDIT',
						},
						success: function (response) {
							if (response == 's') {
								$('.loader').remove();
								$('.loader-box').remove();
								$('.loader-bg').remove();
								$('.payByBtn').remove();
								$('.inner-payBy').append('<a href="/bill" style="color: white;" class="payByBtn btn btn-success">我的訂單</a>');
								$('.U-title').html('<font>感謝您的購買~</font>');
								$('.U-img').html("<img style='height: 70%;'' src='{{asset('images/thankYou.png')}}'>");
								$('.U-text').html("<font>我們衷心感謝您購買我們的產品，您將會收到一封電子確認信，內含您的購買明細。<br>若您對此次交易有任何問題，請隨時<a href='{{route('contact')}}'>寫信給我們</a>。</font>");
								setTimeout(function(){
									alert('電子確認信已寄出，內含您的購買明細及繳款資訊');
								},20)
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
				else if (json.RtnCode == '10200164') { //超時
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

{{-- @if($finalBill['pay_by'] == 'CREDIT' AND $finalBill['status'] !=1)
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
				setTimeout(function(){
					alert('電子確認信已寄出，內含您的購買明細。');
				},10);
			}
		},
		error: function () {
	         alert('錯誤');
	     },
	});
</script>
@endif --}}

@endsection

@section('fbq')
	<script>
			var content_ids = [];
			var content_name = '';
			var category_name = '';
			var contents = [];
			
			d.ecommerce.purchase.products.forEach(item => {
				content_ids.push(item.id);
				let c = {};
				c['id'] = item.id;
				c['quantity'] = item.quantity;
				contents.push(c);
				if(content_name){
					content_name = content_name + ',' + item.name;
				}else{
					content_name = item.name;
				}
				if(category_name){
					category_name = category_name + ',' + item.category;
				}else{
					category_name = item.category;
				}
			});
			
			var fbqObject = {
				content_ids:content_ids,
				content_name:content_name,
				category_name:category_name,
				value:d.ecommerce.purchase.actionField.revenue,
				currency:'TWD',
				contents:contents,
				content_type:'product',
			};
		function waitForFbq(callback){
			if(typeof fbq !== 'undefined'){
				callback()
			} else {
				setTimeout(function () {
					waitForFbq(callback)
				}, 500)
			}
		}
		waitForFbq(function () {
			fbq('track','Purchase',fbqObject);
		})
		
	</script>
@endsection