<html>
	<head>
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>banner管理</title>
		<style>
			
		</style>
		{{Html::style('css/reset.css')}}
		{{Html::style('css/bootstrap/bootstrap.min.css')}}
	</head>

	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<a href="{{route('banner.create')}}">新增</a><br>
					<table>
						@foreach($banners as $banner)
						<tr>
							<td><img src="{{asset('images/banner') .'/' . $banner->image}}"></td>
							<td>{{$banner->link}}</td>
							<td>{{$banner->alt}}</td>
							<td><a href="{{route('banner.edit',$banner->id)}}">修改</a></td>
							<td><div onclick="deleteBanner({{$banner->id}})">刪除</div></td>
						</tr>
						@endforeach
					</table>
					
					
				</div>
			</div>
		</div>
		
	</body>

	{{ Html::script('js/jquery/jquery-3.2.1.min.js') }}
	<script>
		function deleteBanner(id){
			$.ajaxSetup({
  				headers: {
    				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  				}
			});
			$.ajax({
				type:'POST',
				url:'banner/' + id,
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
	</script>
</html>