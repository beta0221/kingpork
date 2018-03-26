<html>
	<head>
		<title>新增banner</title>
		<style>
			
		</style>
	</head>

	<body>
		<form action="{{route('banner.store')}}" method="post" enctype="multipart/form-data">
			{{ csrf_field() }}
			<input type="file" name="image"><br>
			<input type="text" name="link"><br>
			<input type="text" name="alt"><br>
			<button type="submit">確定</button>
		</form>
	</body>
</html>