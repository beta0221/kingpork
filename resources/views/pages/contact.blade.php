@extends('main')

@section('title','| 聯絡我們')

@section('stylesheets')
<style>
.contactUs{
	min-height: 60vh;
	text-align: center;
	margin-top: 80px;
	margin-bottom: 80px;
}
.contactUs form{
	/*border:1pt solid #000;*/
	top: 20px;
}
.contactUs span{
	margin: 0 16px 0 16px;
}
.contactUs input{
	width: 100%;
	border-radius: 0.3em;
	height:40px;
	padding-left: 6px;
	border: none;
	/*background-color: rgba(213,185,148,0.1);*/
}
.contactUs input:focus{
	outline-color: rgba(195,28,34,0.1);
}
.contactUs textarea{
	width: 100%;
	height: 200px;
	min-height: 200px;
	max-height: 200px;
	max-width: 100%;
	min-width: 100%;
	border-radius: 0.3em;
	padding-left: 6px;
	border: none;
}
.contactUs textarea:focus{
	outline-color: rgba(195,28,34,0.1);
}
.contactUs p{
	margin: 4px 0 0 0;
}


@media(max-width: 450px){
	.contactUs{
		margin-top: 60px;
		margin-bottom: 60px;
	}
	.contactUs h1{
		font-size: 26px;
	}
}

</style>
@endsection


@section('content')
	
	<div class="contactUs">
		  <div class="container">
		    <div class="row">
		      <div class="col-xs-12 col-md-8 offset-md-2">
				<h1 id="contactUs">聯絡我們<span>/</span>CONTACT US </h1>
				@if(Session::has('success'))
				<div class="form-control btn-success mt-4">{{Session('success')}}</div>
				@endif
		        <form class="form" id="form1" action="/contactUs" method="post" enctype="text">
		        	{{csrf_field()}}
		            <p class="name">
		              <input name="name" type="text" class="feedback-input" placeholder="姓名" id="name" />
		            </p>
		            <p class="email">
		              <input name="email" type="text" class="feedback-input" id="email" placeholder="Email" />
		            </p>
		            <p class="title">
		              <input name="title" type="text" class="feedback-input" placeholder="主旨" id="title" />
		            </p>
		            <div class="g-recaptcha" data-sitekey="6LfOZnoUAAAAANBhKzIm2Clc64yH5cYujcq6X_Iv"></div>
		            <p class="text">
		              <textarea name="text" class="feedback-input" id="comment" placeholder="訊息..."></textarea>
		            </p>

		            <input id="submitBtn" type="submit" value="送 出" class="btn btn-danger btn-block" />
		        </form>
		        <p></p>
		      </div>
		    </div>
		    
		  </div>
	</div>

@endsection

@section('scripts')
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
	$(document).ready(function(){
		$('#submitBtn').click(function(){
			$('#submitBtn').css('display','none');
		});
	});
</script>
@endsection