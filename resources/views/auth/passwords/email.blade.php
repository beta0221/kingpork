{{-- @extends('layouts.app') --}}
@extends('main')

@section('title','| 忘記密碼')

@section('stylesheets')
<style>
.outter{
    min-height: 60vh;
    margin: 60px auto 60px auto;
    background-color: rgba(255,255,255,0.5);
    box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
    border-radius: 0.3em;
}    
</style>
@endsection

@section('content')


    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 outter">
                <div class="panel panel-default">
                    <div class="panel-heading"><h5 style="margin-top: 10px;">忘記密碼</h5></div>
                    <hr>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">電子信箱（E-Mail）</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        傳送認證
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
