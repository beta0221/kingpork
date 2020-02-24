@extends('admin_main')

@section('title','| 編輯產品類別')

@section('stylesheets')
<style>
	.contentPage{
		width: 100%;
		padding: 40px 0 40px 0;
	}
	input{
		width: 100%;
		display: block;
	}
	textarea{
		height: 400px;
	}
</style>
@endsection

	@section('content')



	<div class="contentPage">
		<div class="container">
			<div class="row">
				<div class="col-md-10 offset-md-1">
					<form action="{{route('productCategory.update',$PC->id)}}" method="POST">
						<input name="_method" type="hidden" value="PUT">
						{{ csrf_field() }}
						
						<label for="name">新增類別：</label>
						<input type="text" name="name" class="form-control" value="{{$PC->name}}">

						<label class="mt-4">嵌入youtube影片範例：</label>
						<div class="mb-4" style="font-size:6px;">
							<pre class="m-0">&lt;div class="youtube-outter"&gt;</pre>
							<pre class="m-0">&lt;div class="youtube-inner"&gt;</pre>
							<pre class="m-0">&lt;iframe width="100%" height="100%" src="{影片網址}" frameborder="0" allow="accelerometer; autoplay; </pre>
							<pre class="m-0">encrypted-media; gyroscope; picture-in-picture" allowfullscreen&gt;&lt;/iframe&gt;</pre>
							<pre class="m-0">&lt;/div&gt;</pre>
							<pre class="m-0">&lt;/div&gt;</pre>
						</div>

						<label for="content">內容：</label>
						<textarea type="text" name="content" class="form-control">{{$PC->content}}</textarea>

						<input type="submit" value="更新" class="btn btn-success btn-block mt-3">

					</form>

				</div>
			</div>
		</div>
	</div>


@endsection

@section('scripts')
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
@endsection