<html>
	<head>
		<title>管理後台｜類別編輯</title>
	</head>
	<body>
		

		<form action="{{route('productCategory.store')}}" method="POST">
			{{ csrf_field() }}
		新增類別：
		<input type="text" name="name">

		<input type="submit" value="新增">

		</form>


		@foreach($productCategorys as $productCategory)

		<a href="{{route('productCategory.show',$productCategory->id)}}">{{$productCategory->name}}</a><br>



		@endforeach


		
	</body>
</html>







