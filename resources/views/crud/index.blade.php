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
			<span class="btn btn-sm mb-2">{{$data->$name}}</span>
			@endforeach
            <br>
		@endforeach
	</div>


@endsection

@section('scripts')

@endsection