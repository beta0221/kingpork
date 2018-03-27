<html>
	<head>
		<title>管理後台｜商品總覽</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		{{Html::style('css/reset.css')}}
		{{Html::style('css/bootstrap/bootstrap.min.css')}}

	</head>
	<style>
		.productsIMG{
			height: 80px;
			width: 80px;
		}
	</style>
	<body>
		<a href="{{route('products.create')}}" class="btn btn-success mt-3 mb-3 ml-4 mr-4" style="color: #fff;cursor: pointer;">新增產品</a>


		
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
</div>

		<table class="table">
			<thead>
				<tr>
					<th scope="col">ID</th>
					<th scope="col">圖片</th>
					<th scope="col">名稱</th>
					<th scope="col">代號</th>
					<th scope="col">類別</th>
					<th scope="col">規格</th>
					<th scope="col">價格</th>
					<th scope="col">紅利</th>
					<th scope="col">內容</th>
					<th scope="col">-</th>
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
					<td>{{$product->slug}}</td>
					<td>{{$product->productCategory->name}}</td>
					<td>{{$product->format}}</td>
					<td>{{$product->price}}</td>
					<td>{{$product->bonus}}</td>
					<td>
						{{substr(strip_tags($product->content),0,50)}}
						{{strlen(strip_tags($product->content)) > 50 ? '...' : ''}}
					</td>
					<td>
						<button onclick="show({{$product->id}})" style="cursor:pointer;" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModal">瀏覽</button>
						{{-- {{ route('products.show' , $product->id ) }} --}}
					</td>
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



	</body>
	{{ Html::script('js/tinymce/tinymce.min.js') }}
	{{ Html::script('js/jquery/jquery-3.2.1.min.js') }}
	{{ Html::script('js/bootstrap/bootstrap.min.js') }}
	<script>
		function show(id){
			$('.modal-body').empty();
			$('.modal-title').empty();
			$.ajaxSetup({
	  			headers: {
	    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
	  			}
			});
			$.ajax({

				type:'GET',
				url:'{{url('products')}}' +'/' + id,
				dataType:'json',
				success: function (response) {
					$('.modal-body').append(response.content);
					$('.modal-title').append(response.name);
				},
				error: function () {
	                alert('錯誤');
	            },
			});	




		};
	</script>
	<script type="text/javascript">
		$('#myModal').on('shown.bs.modal', function () {
		  $('#myInput').trigger('focus');
		})
	</script>
</html>
