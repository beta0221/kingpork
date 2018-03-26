@extends('main')

@section('title','| 單一商品')

@section('stylesheets')

@endsection

@section('content')

{{$product->id}}
{{$product->name}}
{{$product->productCategory->name}}
{{$product->format}}
{{$product->price}}
{{$product->bonus}}
{{$product->content}}

<p style="color:red;">{{ Auth::user()->id }} - {{ Auth::user()->name }}</p>




@if($kart == null)



{{-- <form action="{{ route('kart.store')}}" method="POST">
	{{ csrf_field() }}
	<input style="display: none" type="text" name="product_id" value="{{$product->id}}">
	<button type="submit">
		放入購物車
	</button>
</form> --}}

<div class="place">
<button id="addToKartBtn" onclick="addToKart({{$product->id}});">放入購物車</button>
</div>


@else
已加入購物車
@endif








@endsection


@section('scripts')
<script>
	function addToKart(id){
		$.ajaxSetup({
  			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  			}
		});
		$.ajax({
			type:'POST',
			url:'{{route('kart.store')}}',
			dataType:'json',
			data: {
				'product_id':id,
			},
			success: function (response) {
                alert(response.msg);
                $('#addToKartBtn').remove();
				$('.place').append('已加入購物車');
            },
            error: function (data) {
                alert('無法加入購物車');
            }
		});
	

	}
</script>
@endsection