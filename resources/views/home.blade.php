@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4>會員功能</h4>
                    <div class="list-group">
                        <a href="{{ route('bill.index') }}" class="list-group-item">
                            <i class="fa fa-shopping-bag"></i> 訂單管理
                        </a>
                        <a href="{{ route('creditCard.index') }}" class="list-group-item">
                            <i class="fa fa-credit-card"></i> 信用卡管理
                        </a>
                        <a href="{{ route('kart.index') }}" class="list-group-item">
                            <i class="fa fa-shopping-cart"></i> 購物車
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
