@extends('admin_main')

@section('title','| 子產品')

@section('stylesheets')
<style>
	.package-item-outter-div th, .package-item-outter-div td {
        width: 80px;
        text-align: center;
    }
</style>
@endsection

@section('content')
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div>
    {{ Form::open(['url' => '/packageItem']) }}

    {{Form::label('name','名稱:')}}
    {{ Form::text('name', null, ['placeholder' => '名稱']) }}
    
    {{Form::label('short','物流縮寫:')}}
    {{ Form::text('short', null, ['placeholder' => '物流縮寫']) }}
    
    {{Form::label('erp_key','ERP代號:')}}
    {{ Form::text('erp_key', null, ['placeholder' => 'ERP代號']) }}
    
    {{ Form::submit('新增') }}
    
    {{ Form::close() }}
</div>

{{$packageItems->links()}}
<div class="package-item-outter-div">
    <div style="display: inline-block">
        <table style="width: 100%">
            <tr>
                <th>#</th>
                <th>商品</th>
                <th>物流縮寫</th>
                <th>ERP代號</th>
            </tr>
            @foreach ($packageItems as $i => $item)
            <tr>
                <td>{{$i + 1}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->short}}</td>
                <td>{{$item->erp_key}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
{{$packageItems->links()}}






@endsection

@section('scripts')


@endsection