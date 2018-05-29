@extends('admin_main')

@section('title','| 新增商品')

@section('stylesheets')
<style>
	.contentPage{
		width: 100%;
		padding: 80px 0 80px 0;
	}
	/*.createForm > input{
		width: 100%;
		display: block;
	}
	.createForm > textarea{
		height: 400px;
	}*/
</style>
@endsection

@section('content')
<div class="contentPage">
			<div class="container">
				<div class="row">
					<div class="col-md-8 offset-md-2">
						<form class="createForm" method="POST" action="{{route('products.store')}}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<label for="name">品名：(必填)</label>
							<input class="form-control" type="text" name="name">

							<label for="slug">代號：(必填｜大於5字元｜不可重複)</label>
							<input class="form-control" type="text" name="slug">

							<label for="category_id">類別：(必填)</label><br>
							<select class="form-control" name="category_id" id="">
							@foreach($productCategorys as $productCategory)
								<option value="{{ $productCategory->id }}">{{ $productCategory->name}}</option>
							@endforeach
							</select><br>

							<label for="format">每片單價：(必填)</label>
							<input class="form-control" type="text" name="format">

							<label for="price">價格：(必填｜數字)</label>
							<input class="form-control" type="number" name="price">

							<label for="bonus">紅利：(必填｜數字)</label>
							<input class="form-control" type="number" name="bonus">

							<label for="image">圖片：(必填｜圖片)</label>
							<input class="form-control" type="file" name="image">

							{{-- <label for="content">內容：</label>
							<textarea type="text" name="content"></textarea> --}}

							<button style="float: right;cursor: pointer;" class="btn btn-success btn-block mt-4" type="submit">
								發布
							</button>
						</form>
		                 

					</div>
				</div>
			</div>
		</div>
@endsection

@section('scripts')
{{-- {{ Html::script('js/tinymce/tinymce.min.js') }}
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
</script> --}}
@endsection				