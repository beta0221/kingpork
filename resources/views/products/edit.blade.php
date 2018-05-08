<html>
	<head>
		<title>管理後台｜產品編輯</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		{{Html::style('css/reset.css')}}
		{{Html::style('css/bootstrap/bootstrap.min.css')}}
		<style>
			.contentPage{
		    	width: 100%;
		    	padding: 80px 0 80px 0;
			}
		</style>
	</head>
	<body>
		<div class="contentPage">
			<div class="container">
				<div class="row">
					<div class="col-md-8 offset-md-2">
						{!! Form::model($product,['route'=>['products.update',$product->id],'method'=>'PUT','files'=>'true']) !!}
						
						
						{{Form::label('name','品名：')}}
						{{ Form::text('name',null,['class'=>'form-control']) }}<br>

						
						{{Form::label('slug','代號：')}}
						{{ Form::text('slug',null,['class'=>'form-control'])}}<br>
						
						{{Form::label('category_id','類別：')}}
						{{Form::select('category_id',$productCategorys,null,['class'=>'form-control'])}}<br>
						
						{{Form::label('format','規格：')}}
						{{Form::text('format',null,['class'=>'form-control'])}}<br>
						
						{{Form::label('price','價格：')}}
						{{Form::text('price',null,['class'=>'form-control'])}}<br>
						
						{{Form::label('bonus','紅利：')}}
						{{Form::text('bonus',null,['class'=>'form-control'])}}<br>
						
						{{Form::label('image','圖片：')}}
						{{Form::file('image',['class'=>'form-control'])}}
						<br>
						
						
						{{Form::label('content','內容：')}}
						{{Form::textarea('content',null,['class'=>'form-control'])}}
						<br>
						{{Form::submit('更新',['class'=>'btn btn-primary','style'=>'float: right;cursor: pointer;'])}}
						{!! Form::close() !!}
					</div>			
				</div>
			</div>
		</div>
	</body>

{{ Html::script('js/tinymce/tinymce.min.js') }}

<script>
	tinymce.init({ 
		selector:'textarea',
  		// menubar: "insert",
  		language : "zh_TW" ,
  		plugins: [
    		"advlist autolink lists link image charmap print preview anchor",
    		"searchreplace visualblocks code fullscreen",
    		"insertdatetime media table contextmenu paste jbimages",
    		"image imagetools",
    		"textcolor colorpicker",
  		],
  		toolbar: "insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages | forecolor backcolor | fontsizeselect | fontselect",
  		relative_urls: false,
  		font_formats: 'Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;AkrutiKndPadmini=Akpdmi-n'
	});
</script>
</html>
