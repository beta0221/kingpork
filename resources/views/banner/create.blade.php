@extends('admin_main')

@section('title','| 新增BANNER')

@section('stylesheets')
<style>

</style>
@endsection

@section('content')
<form action="{{route('banner.store')}}" method="post" enctype="multipart/form-data">
	{{ csrf_field() }}
	<input style="width: 20%;" class="form-control ml-3 mt-3" type="file" name="image"><br>
	<input style="width: 60%;" class="form-control ml-3" type="text" name="link" placeholder="超連結"><br>
	<input style="width: 60%;" class="form-control ml-3" type="text" name="alt" placeholder="關鍵字"><br>
	<button class="btn btn-success ml-3" type="submit">確定</button>
</form>
@endsection

@section('scripts')

@endsection
