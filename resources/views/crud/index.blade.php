@extends('admin_main')

@section('title','| '.$title)

@section('stylesheets')
<style>
	
</style>
@endsection

	@section('content')

	<div class="p-2">
		<h3>{{$title}}</h3>
        <form action="{{$createRoute}}" method="POST">
            {{ csrf_field() }}
			@foreach ($columns as $name=>$title)
			<input name="{{$name}}" style="display: inline-block;width:160px" type="text" class="form-control" placeholder="{{$title}}">	
			@endforeach
            <button style="display: inline-block" class="btn btn-success btn-sm">新增</button>
        </form>
	</div>
	<hr class="m-0">
	<div class="p-2">
		@foreach($dataList as $i => $data)
			@foreach ($columns as $name=>$title)
			<span class="span-row span-row-{{$data->id}} btn btn-sm mb-2">{{$data->$name}}</span>
			<input class="input-row input-row-{{$data->id}}" type="text" value="{{$data->$name}}" name="{{$name}}" style="display:none;">
			
			@endforeach
			<button data-id="{{$data->id}}" class="button-edit button-edit-{{$data->id}} btn btn-sm btn-warning">編輯</button>
			<button data-id="{{$data->id}}" class="button-cancel button-cancel-{{$data->id}} btn btn-sm btn-dark" style="display: none">取消</button>
			<button data-id="{{$data->id}}" class="button-submit button-submit-{{$data->id}} btn btn-sm btn-success" style="display: none">送出</button>
            <br>
		@endforeach
	</div>

	<form id="form-update-data" action="" method="POST" style="display: none">
		<input type="hidden" name="_method" value="PUT">
		{{ csrf_field() }}
	</form>

@endsection

@section('scripts')
<script>
	$(document).ready(function(){
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$(".button-edit").on('click',function(){
			init();
			let id = $(this).data('id');
			$(this).hide();
			$('.span-row-'+id).hide();
			$('.input-row-'+id).show();
			$('.button-cancel-'+id).show();
			$('.button-submit-'+id).show();
		});

		$(".button-cancel").on('click',function(){
			init();
			let id = $(this).data('id');
			$(this).hide();
			$('.input-row-'+id).hide();
			$('.span-row-'+id).show();
			$('.button-edit-'+id).show();
			$('.button-submit-'+id).hide();
		});

		$(".button-submit").on('click',function(){
			let id = $(this).data('id');
			$('#form-update-data').prop('action',window.location.pathname+'/'+id);
			$( ".input-row-"+id).clone().appendTo("#form-update-data");
			$('#form-update-data').submit();
		});

	});

	function init(){
		$('.span-row').show();
		$('.input-row').hide();
		$('.button-edit').show();
		$('.button-cancel').hide();
		$('.button-submit').hide();
	}


</script>
@endsection