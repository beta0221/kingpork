<html>
	<head>
		<title>管理後台｜新增商品</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		{{Html::style('css/reset.css')}}
		{{Html::style('css/bootstrap/bootstrap.min.css')}}
		<style>
			.contentPage{
		    	width: 100%;
		    	padding: 80px 0 80px 0;
			}
			.createForm{

			}
			.createForm > input{
				width: 100%;
				display: block;
			}
			.createForm > textarea{
				height: 400px;
			}
		</style>
	</head>
	
	<body>
		<div class="contentPage">
			<div class="container">
				<div class="row">
					<div class="col-md-8 offset-md-2">
						<form class="createForm" method="POST" action="{{route('products.store')}}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<label for="name">品名：</label>
							<input class="form-control" type="text" name="name">

							<label for="slug">代號：</label>
							<input class="form-control" type="text" name="slug">

							<label for="category_id">類別</label><br>
							<select class="form-control" name="category_id" id="">
							@foreach($productCategorys as $productCategory)
								<option value="{{ $productCategory->id }}">{{ $productCategory->name}}</option>
							@endforeach
							</select><br>

							<label for="format">規格：</label>
							<input class="form-control" type="text" name="format">

							<label for="price">價格：</label>
							<input class="form-control" type="number" name="price">

							<label for="bonus">紅利：</label>
							<input class="form-control" type="number" name="bonus">

							<label for="image">圖片：</label>
							<input class="form-control" type="file" name="image">

							<label for="content">內容：</label>
							<textarea type="text" name="content"></textarea>

							<button style="float: right;cursor: pointer;" class="btn btn-primary mt-2" type="submit">
								發布
							</button>
						</form>
		                 

					</div>
				</div>
			</div>
		</div>
	</body>




{{ Html::script('js/tinymce/tinymce.min.js') }}

<script>
	tinymce.init({ 
		selector:'textarea',
		plugins: "image imagetools",
  		// menubar: "insert",
  		language : "zh_TW" ,
  		plugins: [
    		"advlist autolink lists link image charmap print preview anchor",
    		"searchreplace visualblocks code fullscreen",
    		"insertdatetime media table contextmenu paste jbimages"
  		],
  		toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
  		relative_urls: false

  		
	});
</script>
</html>