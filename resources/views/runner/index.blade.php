@extends('admin_main')

@section('title','| 跑馬燈管理')

@section('stylesheets')
<style>
.outter{
	width: 100%;
}
.table{
	font-size: 14px;
}
.createBox{
	width: 100%;
	/*height: 40px;*/
}
</style>
@endsection

@section('content')


<div class="outter">
	
	<div class="createBox pt-2 pl-2 pr-2 pb-2">
		<form action="{{route('runner.store')}}" method="post">
		{{ csrf_field() }}
		
		<input style="width: calc(100% - 80px);display: inline-block;" class="form-control" type="text" name="running_text" placeholder="跑馬燈內容...">
		
		<button style="display: inline-block;top: 0;margin: 0;" class="btn btn-success btn-sm ml-2" type="submit">新增</button>
	</form>	
	</div>
	

	<table class="table">
		<thead>
			<tr>
				<th style="width: 20px;" scope="col">#</th>
				<th style="width: 80px;" scope="col">使用</th>
				<th style="width: 80%;" scope="col">跑馬燈內容</th>
				<th style="width: ;" scope="col">-</th>
				<th style="width: ;" scope="col">-</th>
			</tr>
		</thead>
		<?php $i=1 ?>
		@foreach($runners as $runner)
		@if($runner->use == 1)
		<tr style="background: gray">
		@else
		<tr>
		@endif
			<td>{{$i++}}</td>
			<td>
				@if($runner->use == 1)
					使用中
				@else
					<button class="btn btn-sm btn-warning" onclick="useRunner({{$runner->id}})">使用</button>
				@endif
				
			</td>
			<td id="text_{{$runner->id}}"><span class="span_text" id="span_{{$runner->id}}">{{$runner->running_text}}</span></td>
			<td id="td_{{$runner->id}}">
				<button id="update_{{$runner->id}}" class="updateBtn btn btn-sm btn-primary ml-1 mr-1" href="" onclick="updateRunner({{$runner->id}})">修改</button>
			</td>
			<td>
					<div class="btn btn-sm btn-danger ml-1 mr-1" onclick="deleteRunner({{$runner->id}})">刪除</div>
			</td>
		</tr>
		@endforeach
	</table>
	
	
</div>




@endsection

@section('scripts')
<script>
	function deleteRunner(id){
			$.ajaxSetup({
  				headers: {
    				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  				}
			});
			$.ajax({
				type:'POST',
				url:'runner/' + id,
				dataType:'json',
				data:{
					_method:'delete',
				},
				success:function(response){
					location.reload();
				},
				error:function(){
					alert('刪除失敗');
				}
			});
		}
		function useRunner(id){
			$.ajaxSetup({
  				headers: {
    				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  				}
			});
			$.ajax({
				type:'POST',
				url:'runner/use/',
				dataType:'json',
				data:{
					use:id,
				},
				success:function(response){
					
					location.reload();
				},
				error:function(){
					alert('失敗');
				}
			});
		}
		function recovery(){
			$('.updateBtn').css('display','block');
			$('.span_text').css('display','initial');
			$('.appended').remove();
		}
		function updateRunner(id){
			
			$('.updateBtn').css('display','block');
			$('.span_text').css('display','initial');
			$('.appended').remove();

			$('#update_'+id).css('display','none');
			$('#td_'+id).append('<button class="appended btn btn-success btn-sm ml-1 mr-1" type="submit" onclick="update_form();">修改</button>');
			var text = $('#span_'+id).html();
			$('#span_'+id).css('display','none');
			$('#text_'+id).append('<form id="update_form" class="appended" style="display:inline-block;width:95%;" action="/runner/'+id+'" method="POST"><input name="_method" type="hidden" value="PUT">{{csrf_field()}}<input id="updating" style="width: 100%;display: inline-block;" class="form-control" type="text" name="running_text"></form><button style="display:inline-block;" class="appended btn btn-sm btn-secondary ml-2" onclick="recovery();">X</button>');
			$('#updating').val(text);

		}
		function update_form(){
			$('#update_form').submit();
		}
</script>
@endsection