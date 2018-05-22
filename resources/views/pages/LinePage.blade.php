@extends('main')

@section('title','| LINE線上客服')

@section('stylesheets')
<style>
.outter{
	text-align: center;
	padding-top: 80px;
	padding-bottom: 80px;
	min-height: 80vh;
}
.outter img{
	width: 100%;
	margin: 20px 0 20px 0;
}
</style>
@endsection

@section('content')

<div class="outter">
	<div class="container">
		<div class="row">
			<div class="col-md-4 offset-md-4">
				<h1 id="contactUs">LINE 線上客服</h1>
				<img src="{{asset('images/productsIMG/both.jpg')}}" alt="">
				<h4>LINE ID：kingpork</h4>
			</div>
		</div>
	</div>
</div>










@endsection

@section('scripts')

@endsection