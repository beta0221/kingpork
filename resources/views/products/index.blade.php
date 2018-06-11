@extends('admin_main')

@section('title','| 商品總覽')

@section('stylesheets')
<style>
	.productsIMG{
		height: 80px;
		width: 80px;
	}
	.table{
		font-size: 14px;
	}
</style>
@endsection

@section('content')

<a href="{{route('products.create')}}" class="btn btn-success btn-sm mt-2 mb-2 ml-3 mr-3" style="color: #fff;cursor: pointer;">新增產品</a>
		
<!-- Modal -->
{{-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
      </div>
    </div>
  </div>
</div> --}}
{{-- Modal --}}



<table class="table">
	<thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">圖片</th>
			<th scope="col">名稱</th>
			<th scope="col">小標</th>
			<th scope="col">代號</th>
			<th scope="col">類別</th>
			<th scope="col">每片單價</th>
			<th scope="col">價格</th>
			<th scope="col">紅利</th>
			{{-- <th scope="col">內容</th> --}}
			{{-- <th scope="col">-</th> --}}
			<th scope="col">-</th>
			<th scope="col">-</th>
		</tr>
	</thead>
	<tbody>
	@foreach($products as $product)
		<tr>
			<td>{{$product->id}}</td>
			<td>
				<img class="productsIMG" src="{{asset('images/productsIMG') . '/' . $product->image}}" alt="">
				{{-- {{$product->image}} --}}
			</td>
			<td>{{$product->name}}</td>
			<td>{{$product->discription}}</td>
			<td>{{$product->slug}}</td>
			<td>{{$product->productCategory->name}}</td>
			<td>{{$product->format}}</td>
			<td>{{$product->price}}</td>
			<td>{{$product->bonus}}</td>
			{{-- <td>
				{{substr(strip_tags($product->content),0,50)}}
				{{strlen(strip_tags($product->content)) > 50 ? '...' : ''}}
			</td> --}}
			{{-- <td>
				<button onclick="show({{$product->id}})" style="cursor:pointer;" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModal">瀏覽</button>
			</td> --}}
			<td>
				<a class="btn btn-sm btn-warning" href="{{ route('products.edit', $product->id)}}">編輯</a>
			</td>
			<td>
				{!!Form::open(['route'=> ['products.destroy',$product->id],'method'=>'DELETE'])!!}
					{!!Form::submit('刪除',['class'=>'btn btn-danger btn-sm','style'=>'cursor:pointer;'])!!}
				{!!Form::close()!!}
			</td>
		</tr>
	@endforeach
	</tbody>
</table>

@endsection




@section('scripts')
<script>
	// function show(id){
	// 	$('.modal-body').empty();
	// 	$('.modal-title').empty();
	// 	$.ajaxSetup({
 //  			headers: {
 //    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
 //  			}
	// 	});
	// 	$.ajax({

	// 		type:'GET',
	// 		url:'/products/' + id,
	// 		dataType:'json',
	// 		success: function (response) {
	// 			$('.modal-body').append(response.content);
	// 			$('.modal-title').append(response.name);
	// 		},
	// 		error: function () {
 //                alert('錯誤');
 //            },
	// 	});	
	// };
</script>
	{{-- <script type="text/javascript">
		$('#myModal').on('shown.bs.modal', function () {
		  $('#myInput').trigger('focus');
		})
	</script> --}}
@endsection