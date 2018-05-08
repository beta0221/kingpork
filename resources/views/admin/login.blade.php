{{-- @extends('layouts.app') --}}
@extends('main')

@section('title','| 管理員登入')

@section('stylesheets')
<style>
.contentPage{
    width: 100%;
    /*height: calc(100vh - 160px);*/
    height: auto;
}
.outter{
    margin: 80px auto 80px auto;
    height: 520px;
    background-color: rgba(255,255,255,0.5);
    box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
    border-radius: 0.3em;
    text-align: center;
}
.loginForm{
    position: absolute;
    width: 100%;
    height: auto;
    top: 50%;
    transform: translateY(-50%);
}
.form-group{
    width: calc(100% - 30px);

}
</style>

@endsection

@section('content')

<div class="contentPage">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 outter">
                
                
                <font style="font-size: 32pt;top: 60px;">管理後台登入</font>

                {{-- login part start --}}
                
                <form class="loginForm form-horizontal" method="POST" action="{{ route('admin.login.submit') }}">
                    {{ csrf_field() }}

                    <div style="margin-top: 3rem" class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        

                        <div class="col-md-6 offset-md-3">
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="email" required autofocus>

                            {{-- @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif --}}
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        

                        <div class="col-md-6 offset-md-3">
                            <input id="password" type="password" class="form-control" name="password" placeholder="密碼" required>

                            {{-- @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif --}}
                        </div>
                    </div>

                    {{-- <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> 記住我
                                </label>
                            </div>
                        </div>
                    </div> --}}

                    <div class="form-group">
                        <div class="col-md-6 offset-md-3">
                            <button type="submit" class="btn btn-primary">
                                登入
                            </button>

                            <a style="display: block;" class="btn btn-link" href="{{ route('admin.password.request') }}">
                                忘記密碼
                            </a>
                        </div>
                    </div>


                    @if ($errors->has('email'))
                         <span style="" class="help-block">
                             <strong>{{ $errors->first('email') }}</strong>
                         </span>
                     @endif
                </form>
                {{-- login part end --}}




            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    
@endsection
