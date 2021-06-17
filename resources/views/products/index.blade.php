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
	.unpublic{
		background-color: gray;
	}
</style>
@endsection

@section('content')

<a href="{{route('products.create')}}" class="btn btn-success btn-sm mt-2 mb-2 ml-3 mr-3" style="color: #fff;cursor: pointer;">新增產品</a>

<form id="catForm" style="display: inline-block;" action="{{URL::current()}}" method="GET">
	<span>類別</span>
	<select name="category" id="categorySelecter">
		<option value="">全部分類</option>
		@foreach($cats as $cat)
		<option {{($request->category==$cat->id)?'selected':''}} value="{{$cat->id}}">{{$cat->name}}</option>
		@endforeach
	</select>

	<span>上下架</span>
	<select name="public" id="publicSelector">
		<option value="">全部</option>
		<option {{($request->public=='1')?'selected':''}} value="1">上架</option>
		<option {{($request->public=='0')?'selected':''}} value="0">下架</option>
	</select>

	<div class="ml-2 btn btn-sm btn-primary" onclick="prev()">上頁</div>
	<input id="page" name="page" style="width:40px" value="{{($request->page)?$request->page:1}}" />
	<div class="btn btn-sm btn-primary" onclick="next()">下頁</div>
	<span>共{{$totalPage}}頁</span>
</form>
		
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
			<th scope="col">縮寫</th>
			<th scope="col">erp</th>
			<th scope="col">小標</th>
			<th scope="col">代號</th>
			<th scope="col">類別</th>
			<th scope="col">每片單價</th>
			<th scope="col">價格</th>
			<th scope="col">紅利</th>
			<th scope="col">-</th>
		</tr>
	</thead>
	<tbody>
	@foreach($products as $product)
		<tr class="{{($product->public == 0)?'unpublic':''}}">
			<td>{{$product->id}}</td>
			<td>
				<img class="productsIMG" src="{{asset('images/productsIMG') . '/' . $product->image}}" alt="">
			</td>
			<td>{{$product->name}}</td>
			<td>{{$product->short}}</td>
			<td>{{$product->erp_id}}</td>
			<td>{{$product->discription}}</td>
			<td>{{$product->slug}}</td>
			<td>{{$product->productCategory->name}}</td>
			<td>{{$product->format}}</td>
			<td>{{$product->price}}</td>
			<td>{{$product->bonus}}</td>
			<td>
				<div onclick="editInventory({{$product->id}})" class="btn btn-sm btn-primary">庫存</div>
				<a class="btn btn-sm btn-warning" href="{{ route('products.edit', $product->id)}}">編輯</a>
				@if($product->public == 1)
					<div class="public-btn btn btn-sm btn-success ml-2" onclick="publicProduct({{$product->id}});">已上架</div>
				@else
					<div class="public-btn btn btn-sm btn-warning ml-2" onclick="publicProduct({{$product->id}});">已下架</div>
				@endif
			</td>
		</tr>
	@endforeach
	</tbody>
</table>


<div class="modal fade" id="inventoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="exampleModalLabel">庫存關聯</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			@foreach ($inventories as $inventory)
				<input id="inventory-check-{{$inventory->id}}" data-inventory-id="{{$inventory->id}}" class="inventory-check" type="checkbox">
				<span>{{$inventory->name}}</span>
				<input id="inventory-quantity-{{$inventory->id}}" class="inventory-quantity" type="number"><br>
			@endforeach
		</div>
		<div class="modal-footer">
			<button onclick="updateInventory();" type="button" class="btn btn-primary">更新</button>
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
		</div>
	  </div>
	</div>
</div>
@endsection



@section('scripts')
<script>
var current_page = parseInt({{($request->page)?$request->page:'1'}});
var total_page = parseInt({{$totalPage}});
$(document).ready(function(){
	$.ajaxSetup({
  		headers: {
    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		}
	});

	$('#categorySelecter').change(function(){
		$('input#page').val(1);
		$('#catForm').submit();
	});
	$('#publicSelector').change(function(){
		$('input#page').val(1);
		$('#catForm').submit();
	});

	@if (isset($_GET['category']))
	$('#categorySelecter').val('{{$_GET['category']}}');
	@endif
});

function publicProduct(id){
	$.ajax({
		type:'POST',
		url:'/products/public/' + id,
		dataType:'json',
		data:{
			_method:'patch',
		},
		success:function(response){
			location.reload();
			// alert(response);
		},
		error:function(){
			// alert('error');
		}
	});
}

function next(){
	if(current_page >= total_page){ return; }
	$('input#page').val(current_page + 1);
	$('#catForm').submit();
}
function prev(){
	if(current_page <= 1){ return; }
	$('input#page').val(current_page - 1);
	$('#catForm').submit();
}
var edit_product_id;
function editInventory(product_id){
	edit_product_id = product_id;
	$('.inventory-check').each(function(){
		$(this).prop('checked',false);
	});
	$('.inventory-quantity').each(function(){
		$(this).val('');
	});
	$.ajax({
		type:'GET',
		url:'/product/'+product_id+'/inventory',
		dataType:'json',
		success:function(res){
			console.log(res);
			Object.keys(res).forEach(function(key){
				$('#inventory-check-'+key).prop('checked',true);
				$('#inventory-quantity-'+key).val(res[key]);
			});
			$('#inventoryModal').modal('show');
		},
		error:function(error){
			console.log(error);
			alert('錯誤');
		}
	});

}

function updateInventory(){

	var inventory = {};
	$('.inventory-check').each(function(){
		if($(this).prop('checked') == true){
			let id = $(this).data('inventory-id');
			let quantity = $('#inventory-quantity-'+id).val();
			if (quantity == ''){ return false; }
			inventory[id] = parseInt(quantity);
		}
	});
	console.log(inventory);
	$.ajax({
		type:'POST',
		url:'/product/'+edit_product_id+'/updateInventory',
		dataType:'json',
		data:{
			_method:'PUT',
			inventory:inventory
		},
		success:function(res){
			console.log(res);
			$('#inventoryModal').modal('hide');
		},
		error:function(error){
			console.log(error);
			alert('錯誤');
		}
	});
}

</script>
@endsection