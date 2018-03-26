@extends('main')

@section('title','| 商品總覽')

@section('stylesheets')

@endsection

@section('content')

@foreach($products as $product)
<a href="{{ route('products.show' , $product->id ) }}">瀏覽</a>
<a href="{{ route('products.edit', $product->id)}}">編輯</a>


{!!Form::open(['route'=> ['products.destroy',$product->id],'method'=>'DELETE'])!!}
	{!!Form::submit('刪除')!!}
{!!Form::close()!!}

{{$product->id}}
{{$product->name}}
{{$product->productCategory->name}}
{{$product->format}}
{{$product->price}}
{{$product->bonus}}
{{$product->content}}

<br>



@endforeach


@endsection


@section('scripts')

@endsection