@extends('main')

@section('title','| 新增商品')

@section('stylesheets')
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
@endsection

{{--  --}}

@section('content')
<div class="contentPage">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<form class="createForm" method="POST" action="{{route('products.store')}}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<label for="name">品名：</label>
					<input type="text" name="name">

					<label for="slug">代號：</label>
					<input type="text" name="slug">

					<label for="category_id">類別</label><br>
					<select name="category_id" id="">
					@foreach($productCategorys as $productCategory)
						<option value="{{ $productCategory->id }}">{{ $productCategory->name}}</option>
					@endforeach
					</select><br>

					<label for="format">規格：</label>
					<input type="text" name="format">

					<label for="price">價格：</label>
					<input type="number" name="price">

					<label for="bonus">紅利：</label>
					<input type="number" name="bonus">

					<label for="image">圖片：</label>
					<input type="file" name="image">

					<label for="content">內容：</label>
					<textarea type="text" name="content"></textarea>

					<button type="submit">
						發布
					</button>
				</form>
                 

			</div>
		</div>
	</div>
</div>
@endsection

{{--  --}}

@section('scripts')

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
@endsection