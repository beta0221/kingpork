@extends('admin_main')

@section('title','| 產品編輯')

@section('stylesheets')
<style>
	.contentPage{
		width: 100%;
		padding: 80px 0 80px 0;
	}
</style>
@endsection

@section('content')

<div class="contentPage">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2">

				

				{!! Form::model($product,['route'=>['products.update',$product->id],'method'=>'PUT','files'=>'true']) !!}
				
				<input name="previous_url" type="hidden" value="{{url()->previous()}}">
				{{Form::label('name','品名：(必填)')}}
				{{ Form::text('name',null,['class'=>'form-control']) }}<br>

				{{Form::label('','物流限制：')}}<br>
				<div class="mb-4">
					@foreach ($carriers as $id => $name)
						<span><input name="carrier_id[]" value="{{$id}}" type="checkbox" {{ (in_array($id,$carrierRestriction))?'checked':'' }}>{{$name}}</span>
					@endforeach
				</div>
				

				{{Form::label('short','物流用縮寫：(必填)')}}
				{{ Form::text('short',null,['class'=>'form-control']) }}<br>

				{{Form::label('erp_id','ERP代號：(可忽略)')}}
				{{ Form::text('erp_id',null,['class'=>'form-control']) }}<br>

				{{Form::label('discription','小標題：(可忽略)')}}
				{{ Form::text('discription',null,['class'=>'form-control']) }}<br>
				
				
				{{Form::label('category_id','類別：(必填)')}}
				{{Form::select('category_id',$productCategorys,null,['class'=>'form-control'])}}<br>
				
				{{Form::label('format','每片單價：(可忽略)')}}
				{{Form::text('format',null,['class'=>'form-control'])}}<br>
				
				{{Form::label('price','價格：(必填｜數字)')}}
				{{Form::text('price',null,['class'=>'form-control'])}}<br>
				
				{{Form::label('bonus','紅利：(必填｜數字)')}}
				{{Form::text('bonus',null,['class'=>'form-control'])}}<br>
				
				{{Form::label('image','圖片：(必填｜圖片)')}}
				{{Form::file('image',['class'=>'form-control'])}}
				<br>
				<div class="mb-2">
					<h5 id="image-size"></h5>
				</div>
				<div class="mb-2">
					<img style="max-width:100%" src="/images/productsIMG/{{$product->image}}" alt="">
				</div>
				
				{{-- {{Form::label('content','內容：')}}
				{{Form::textarea('content',null,['class'=>'form-control'])}} --}}
				<br>
				{{Form::submit('更新',['class'=>'btn btn-success btn-block','style'=>'float: right;cursor: pointer;'])}}
				{!! Form::close() !!}
			</div>			
		</div>
	</div>
</div>



@endsection

@section('scripts')
<script>
	var img = new Image();
	img.onload = function() {
		$('#image-size').html(this.width + '*' + this.height);
	}
	img.src = '/images/productsIMG/{{$product->image}}';
</script>


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
