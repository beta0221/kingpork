@extends('admin_main')

@section('title','| 轉撥牆管理')

@section('stylesheets')
<style>
.outter{
	width: 100%;
}
.table{
	font-size: 14px;
}
.switch-box{
	height: 80px;
	width: 40px;
}
.switch{
	height: 50%;
	width: 100%;
	background-color: #000;
	color: #fff;
	text-align: center;
	line-height: 40px;
	margin: 4px 0;
}
.switch-up{
	transform: rotate(180deg);
}
.public-btn{
	cursor:pointer;
}
.unpublic{
	background-color: gray;
}
</style>
@endsection

@section('content')



<div class="outter">
	<a class="btn btn-success mt-2 mb-2 ml-2 mr-2" href="{{route('banner.create')}}">新增</a><br>
	<table class="table">
		<thead>
			<tr>
				<th>#</th>
				<th style="width: 100px" scope="col">排序</th>
				<th style="width: 30%;" scope="col">圖片</th>
				<th style="width: 25%;" scope="col">超連結</th>
				<th style="width: 25%;" scope="col">關鍵字</th>
				<th scope="col">-</th>
				<th scope="col">-</th>
				<th scope="col">-</th>
			</tr>
		</thead>
		@foreach($banners as $index => $banner)
		<tr class="{{($banner->public == 0)?'unpublic':''}}">
			<td>
				<div class="switch-box">
					{{$index + 1}}
				</div>
			</td>
			<td style="width: 100px">
				<input type="number" value="{{$banner->sort}}" class="form-control banner-sort-input" data-id="{{$banner->id}}">
			</td>
			<td><img style="width: 100%;" src="{{asset('images/banner') .'/' . $banner->image}}"></td>
			<td>{{$banner->link}}</td>
			<td>{{$banner->alt}}</td>
			<td><a class="btn btn-sm btn-primary ml-1 mr-1" href="{{route('banner.edit',$banner->id)}}">修改</a></td>
			<td>
				@if($banner->public == 1)
					<div class="public-btn btn btn-sm btn-success ml-1 mr-1" onclick="publicBanner({{$banner->id}});">已發布</div>
				@else
					<div class="public-btn btn btn-sm btn-warning ml-1 mr-1" onclick="publicBanner({{$banner->id}});">已停止</div>
				@endif
			</td>
			<td>
				<div class="btn btn-sm btn-danger ml-1 mr-1" onclick="deleteBanner({{$banner->id}})">刪除</div>
			</td>
		</tr>
		@endforeach
	</table>
	
	
</div>



@endsection

@section('scripts')
<script>
		$(document).ready(function(){
			$.ajaxSetup({
  				headers: {
    				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  				}
			});

			$('.banner-sort-input').on('change',function(){

				let banner_id = $(this).data('id');
				let sort = $(this).val();

				$.ajax({
					type:'POST',
					url:'/banner/sort/' + banner_id,
					dataType:'json',
					data:{
						_method:'patch',
						sort:sort,
					},
					success:function(response){
						location.reload();
					},
					error:function(){
						alert('錯誤');
					}
				});
				
			});
		});
		function deleteBanner(id){
			
			$.ajax({
				type:'POST',
				url:'/banner/' + id,
				dataType:'json',
				data:{
					_method:'delete',
				},
				success:function(response){
					location.reload();
				},
				error:function(){
					alert('刪除失敗');
				}
			});
		}


		function publicBanner(id){

			$.ajax({
				type:'POST',
				url:'/banner/public/' + id,
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
	</script>
@endsection
