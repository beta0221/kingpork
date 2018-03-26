@extends('main')

@section('title','| 類別編輯')

@section('stylesheets')

@endsection

@section('content')

<form action="{{route('productCategory.store')}}" method="POST">
	{{ csrf_field() }}
新增類別：
<input type="text" name="name">

<input type="submit" value="新增">

</form>


@foreach($productCategorys as $productCategory)

<a href="{{route('productCategory.show',$productCategory->id)}}">{{$productCategory->name}}</a><br>



@endforeach

@endsection


@section('scripts')

@endsection
