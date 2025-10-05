@extends('main')

@section('title','| 付款')



@section('stylesheets')
{{Html::style('css/_payBill.css')}}
{{Html::style('css/_process.css')}}
<style>
    .ecpay-pay-list-wrap{
        background: transparent!important;
    }
    .ecpay-pl-content{
        background: #fff!important;
    }
</style>
@endsection

@section('content')


<div class="wrapper">
	<div class="container" style="min-height: 700px">

		<div class="row">
			<div class="col-md-12 mt-3" style="text-align: center">
				<h3>進行付款</h3>            
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div id="ECPayPayment"></div>
			</div>
		</div>

        <div class="row">
            <div class="col-md-12">
                <div id="error-msg-div">
                </div>
            </div>
        </div>

		<div class="row">
			<div class="col-md-12">
				<form action="/bill/{{$bill_id}}/pay" id="PayProcess" method="post"> 
					<div style="text-align: center;">
						{{ csrf_field() }}
						<input id="PaymentType" name="PaymentType" type="hidden" value="" />
						<input id="btnPay" type="button" class="btn single btn-gray-dark" value="確認付款" />
					</div>
					<br/>
					<input id="PayToken" name="PayToken" type="hidden" size="50"  value="" />
				</form>
			</div>
		</div>

	</div>

</div>
@endsection



@section('scripts')
<script src ="https://cdn.jsdelivr.net/npm/node-forge@0.7.0/dist/forge.min.js"></script>
<script src="{{$ecpaySDKUrl}}"></script>
<script src="{{ asset('js/checkout-funnel-tracker.js') }}"></script>
<script>


var _token = "{{$token}}";
var env = "{{(config('app.env') == 'production')?'Prod':'Stage'}}";
$(function(){
    delete $.ajaxSettings.headers["X-CSRF-TOKEN"];
    
    ECPay.initialize(env, 1, function (errMsg) {
        try {
            ECPay.createPayment(_token, ECPay.Language.zhTW, function (errMsg) {
                console.log('Callback Message: ' + errMsg);
                if (errMsg != null){ ErrHandle(errMsg); }
            });
            $('#Language').val(ECPay.Language.zhTW);
        } catch (err) {
            ErrHandle(err);
        }
    });


    //消費者選擇完成付款方式,取得PayToken 
    $('#btnPay').click(function () {
        try {
            ECPay.getPayToken(function (paymentInfo, errMsg) {
                //console.log("response => getPayToken(paymentInfo, errMsg):", paymentInfo, errMsg);
                if (errMsg != null) {
                    ErrHandle(errMsg);
                    return;
                };
                $("#PayToken").val(paymentInfo.PayToken);

                $("#PayProcess").submit();
                return true;
            });
        } catch (err) {
            ErrHandle(err);
        }
    });
    
});


function ErrHandle(strErr) {
    let errorDiv = $('#error-msg-div');
    errorDiv.empty();
    errorDiv.append('<div style="text-align: center;"><label style="color: red;">' + strErr ?? 'Token取得失敗' + '</label></div>');
}

</script>
@endsection

