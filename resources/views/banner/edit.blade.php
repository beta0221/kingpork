<html>
	<head>
		<title>修改banner</title>
		<style>
			
		</style>
	</head>

	<body>
		{!! Form::model($banner,['route'=>['banner.update',$banner->id],'method'=>'PUT','files'=>'true']) !!}
			
			{{Form::file('image')}}
			{{Form::text('link',null)}}
			{{Form::text('alt',null)}}
			{{Form::submit('更新')}}
		{!! Form::close() !!}
	</body>
</html>