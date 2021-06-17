@extends('admin_main')

@section('title','| 庫存清單')

@section('stylesheets')
<style>
	
</style>
@endsection

	@section('content')

	<div class="p-2">
        <form action="/inventory" method="POST">
            {{ csrf_field() }}
            <input name="name" style="display: inline-block;width:160px" type="text" class="form-control">
            <button style="display: inline-block" href="{{route('inventory.create')}}" class="btn btn-success btn-sm">新增</button>
        </form>
	</div>
	<hr class="m-0">
	<div class="p-2">
		@foreach($inventories as $i => $inventory)
            <span class="btn btn-sm mb-2">{{$i+1}}.{{$inventory->name}}</span><br>
		@endforeach
	</div>


@endsection

@section('scripts')

@endsection