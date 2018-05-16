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
				<div class="col-md-8 offset-md-2">
					<form action="{{route('productCategory.update',$PC->id)}}" method="POST">
						<input name="_method" type="hidden" value="PUT">
						{{ csrf_field() }}
						
						<label for="name">新增類別：</label>
						<input type="text" name="name" class="form-control" value="{{$PC->name}}">


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