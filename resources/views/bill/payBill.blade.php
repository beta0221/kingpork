@extends('main')

@section('title','| 付款')

@section('stylesheets')
<style>
</style>
@endsection

@section('content')

@endsection

@section('scripts')
	<script src="https://payment-stage.ecpay.com.tw/Scripts/SP/ECPayPayment_1.0.0.js"
	data-MerchantID="2000132"
	data-SPToken="{{$SPToken}}"
	data-PaymentType="ATM"
	data-PaymentName="信用卡"
	data-CustomerBtn="0" >
	</script> 
@endsection