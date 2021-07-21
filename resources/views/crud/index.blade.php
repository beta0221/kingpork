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

				@if ($name=="selector")
					@foreach ($columns['selector'] as $_name => $options)
						<select name="{{$_name}}" class="form-control d-inline-block" style="width: 160px">
							@foreach ($options as $option)
								<option value="{{$option}}">{{$option}}</option>
							@endforeach
						</select>	
					@endforeach
					
				@else
				<input name="{{$name}}" style="display: inline-block;width:160px" type="text" class="form-control" placeholder="{{$title}}">
				@endif

			
			@endforeach
            <button style="display: inline-block" class="btn btn-success btn-sm">新增</button>
        </form>
	</div>
	<hr class="m-0">
	<div class="p-2">

		
		@if (isset($group))
			
			@foreach($group as $item)
				<h5>{{$item}}</h5>
				<?php if(!isset($dataList[$item])){ continue; } ?>

				@include('crud.dataList',[
					'dataList'=>$dataList[$item],
					'columns'=>$columns,
				])

			@endforeach

		@else

			@include('crud.dataList',[
				'dataList'=>$dataList,
				'columns'=>$columns
			])

		@endif
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
			$('.select-row-'+id).show();
			$('.span-row-'+id).hide();
			$('.input-row-'+id).show();
			$('.button-cancel-'+id).show();
			$('.button-submit-'+id).show();
		});

		$(".button-cancel").on('click',function(){
			init();
			let id = $(this).data('id');
			$(this).hide();
			$('.select-row-'+id).hide();
			$('.input-row-'+id).hide();
			$('.span-row-'+id).show();
			$('.button-edit-'+id).show();
			$('.button-submit-'+id).hide();
		});

		$(".button-submit").on('click',function(){
			let id = $(this).data('id');
			$('#form-update-data').prop('action',window.location.pathname+'/'+id);
			$( ".input-row-"+id).clone().appendTo("#form-update-data");
			$( ".select-row-"+id).each(function(index){
				let value = $(this).val();
				let name = $(this).prop('name');
				$("#form-update-data").append(`<input name='${name}' value='${value}'/>`)
			});
			$('#form-update-data').submit();
		});

	});

	function init(){
		$('.select-row').hide();
		$('.span-row').show();
		$('.input-row').hide();
		$('.button-edit').show();
		$('.button-cancel').hide();
		$('.button-submit').hide();
	}


</script>
@endsection