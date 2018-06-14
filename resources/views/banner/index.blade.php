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
</style>
@endsection

@section('content')



<div class="outter">
	<a class="btn btn-success mt-2 mb-2 ml-2 mr-2" href="{{route('banner.create')}}">新增</a><br>
	<table class="table">
		<thead>
			<tr>
				<th>-</th>
				<th style="width: 40%;" scope="col">圖片</th>
				<th style="width: 25%;" scope="col">超連結</th>
				<th style="width: 25%;" scope="col">關鍵字</th>
				<th style="width: ;" scope="col">-</th>
				<th style="width: ;" scope="col">-</th>
			</tr>
		</thead>
		@foreach($banners as $banner)
		<tr>
			<td>
				<div class="switch-box">
					<div onclick="switchIt({{$banner->id}},0);" class="switch switch-up">v</div>
					<div onclick="switchIt({{$banner->id}},1);" class="switch switch-down">v</div>
				</div>
			</td>
			<td><img style="width: 100%;" src="{{asset('images/banner') .'/' . $banner->image}}"></td>
			<td>{{$banner->link}}</td>
			<td>{{$banner->alt}}</td>
			<td><a class="btn btn-sm btn-primary ml-1 mr-1" href="{{route('banner.edit',$banner->id)}}">修改</a></td>
			<td><div class="btn btn-sm btn-danger ml-1 mr-1" onclick="deleteBanner({{$banner->id}})">刪除</div></td>
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

		function switchIt(id,go){


			$.ajax({
				type:'POST',
				url:'/banner/switch',
				dataType:'json',
				data:{
					_method:'POST',
					switch:go,
					banner:id,
				},
				success:function(response){
					location.reload();

				},
				error:function(){

				}
			});


		}
	</script>
@endsection
