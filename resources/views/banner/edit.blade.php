@extends('admin_main')

@section('title','| 修改BANNER')

@section('stylesheets')
<style>
	
</style>
@endsection

@section('content')
{!! Form::model($banner,['route'=>['banner.update',$banner->id],'method'=>'PUT','files'=>'true']) !!}
	{{Form::file('image',['class'=>'form-control ml-3 mt-3','style'=>'width: 20%;'])}}
	{{Form::text('link',null,['class'=>'form-control ml-3 mt-3','style'=>'width: 60%;','placeholder'=>'超連結'])}}
	{{Form::text('alt',null,['class'=>'form-control ml-3 mt-3','style'=>'width: 60%;','placeholder'=>'關鍵字'])}}
	{{Form::submit('更新',['class'=>'btn btn-success ml-3 mt-3'])}}
{!! Form::close() !!}
@endsection

@section('scripts')

@endsection
