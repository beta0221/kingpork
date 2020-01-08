@extends('admin_main')

@section('title','| 產品類別')

@section('stylesheets')
<style>
	
</style>
@endsection

	@section('content')

	<div>
		<a href="{{route('productCategory.create')}}" class="btn btn-success btn-sm mt-3 ml-3 mr-3">新增</a>
	</div>
	<hr>
	<div>
		@foreach($productCategorys as $productCategory)
		<a class="btn btn-sm mt-2 ml-3 mr-3 mb-2" href="{{route('productCategory.edit',$productCategory->id)}}">{{$productCategory->name}}</a><br>
		@endforeach
	</div>


@endsection

@section('scripts')

@endsection