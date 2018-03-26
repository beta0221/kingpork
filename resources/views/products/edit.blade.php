@extends('main')

@section('title','| 編輯商品')

@section('stylesheets')
<style>
	.contentPage{
    	width: 100%;
    	padding: 80px 0 80px 0;
	}
</style>
@endsection

@section('content')
<div class="contentPage">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2">
				{!! Form::model($product,['route'=>['products.update',$product->id],'method'=>'PUT','files'=>'true']) !!}
				
				品名：
				{{ Form::text('name',null) }}<br>

				代號：
				{{ Form::text('slug',null)}}<br>
				類別：
				{{Form::select('category_id',$productCategorys,null)}}<br>
				規格：
				{{Form::text('format',null)}}<br>
				價格：
				{{Form::text('price',null)}}<br>
				紅利：
				{{Form::text('bonus',null)}}<br>
				圖片：
				{{Form::file('image')}}
				<br>
				
				內容：
				{{Form::textarea('content',null)}}
				<br>
				{{Form::submit('更新')}}
				{!! Form::close() !!}
			</div>			
		</div>
	</div>
</div>
@endsection


@section('scripts')

@endsection