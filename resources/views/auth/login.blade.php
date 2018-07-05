@extends('main')

@section('title','| 會員登入')

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
    /*z-index: -1;*/
}
.sel{
    /*width: 100%;*/
    top:20px;
    height: 48px;
    margin-top: 15px;
    border-radius: 0.3em;
    background-color: rgba(0,0,0,0.2);
    z-index: 1;
}
.sel::before , .sel.change::before{
    content:"";
    position: absolute;
    background-color: #d9534f;
    height: 100%;
    width: 50%;
    z-index: 0;
    left: 0;
    transition: ease-in-out 0.1s;
    border-radius: 0.3em;
}
.sel.change::before{
    left: 50%;
}
.sel span{
    position:absolute;
    left: 50%;
    transform: translateX(-50%);
    top: 12px;
}
.registerForm{
    display: none;
}
.loginForm,.registerForm{
    position: absolute;
    width: 100%;
    height: auto;
    top: 50%;
    transform: translateY(-50%);
}
.form-group{
    width: calc(100% - 30px);

}
.loginBtn,.regBtn{
    color: #fff;
    background-color: #d9534f;
    display: inline-block;
    font-weight: 400;
    line-height: 1.25;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: .5rem 1rem;
    font-size: 1rem;
    border-radius: .25rem;
    -webkit-transition: all .2s ease-in-out;
    -o-transition: all .2s ease-in-out;
    transition: all .2s ease-in-out;
    cursor: pointer;
}
.forget{
    border-color: transparent;
    background-color: transparent;
    font-weight: 400;
    color: #d9534f;
    border-radius: 0;
    line-height: 1.25;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: .5rem 1rem;
    font-size: 1rem;
}
.forget:hover{
    color: rgba(195,28,34,0.5);
}
.help-block{
    text-align: center;
    width: calc(100% - 30px);
}
</style>

@if(Session::has('regFail'))
    <style>
        .registerForm{
            display: block;
        }
        .loginForm{
            display: none;
        }
    </style>
@endif

@endsection

@section('content')

<div class="contentPage">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 col-12 outter">
                
                <div class="sel col-md-6 offset-md-3">
                    <div class="row">
                        <div id="selLog" onclick="selLog()" style="cursor: pointer;height: 48px;color:#fff;" class="col-md-6 col-6">
                            <span>登入</span>
                        </div>
                        <div id="selReg" onclick="selReg()" style="cursor: pointer;height: 48px;color:#fff;" class="col-md-6 col-6">
                            <span>註冊</span>
                        </div>
                    </div>
                </div>


                {{-- login part start --}}
                
                <form class="loginForm form-horizontal" method="POST" action="{{ route('login') }}">
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
                            <input type="text" name="reg_buy" style="display: none;" value="0">
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
                            <button style="left: 50%;transform: translateX(-50%);" type="submit" class="loginBtn">
                                登入
                            </button>

                            <a style="display: block;" class="forget" href="{{ route('password.request') }}">
                                忘記密碼
                            </a>
                        </div>
                    </div>


                    @if ($errors->has('email'))
                         <p class="help-block">
                             <strong>{{ $errors->first('email') }}</strong>
                         </p>
                     @endif
                </form>
                {{-- login part end --}}




                {{-- register part start --}}
                
                    <form class="registerForm form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div style="margin-top: 3rem" class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            

                            <div class="col-md-6 offset-md-3">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="姓名" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            

                            <div class="col-md-6 offset-md-3">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="E-Mail" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            

                            <div class="col-md-6 offset-md-3">
                                <input id="password" type="password" class="form-control" name="password" placeholder="密碼" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            

                            <div class="col-md-6 offset-md-3">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="確認密碼" required>
                            </div>
                        </div>
                        
                        {{-- custom part --}}
                        <div class="form-group">
                            <div class="col-md-6 offset-md-3">
                                <input type="text" class="form-control" name="phone" placeholder="電話" required>
                                <input type="text" name="reg_buy" style="display: none;" value="0">
                            </div>
                        </div>
                        {{-- custom part --}}
                        
                        <div class="form-group">
                            <div class="col-md-6 offset-md-3">
                                <button style="left: 50%;transform: translateX(-50%);" type="submit" class="regBtn">
                                    註冊
                                </button>
                            </div>
                            <font style="display: block;text-align: center;margin: 4px 0 0 0;color: #d9534f">！！馬上獲得 5 0 0 0 點紅利！！</font>
                        </div>
                    </form>


                {{-- register part end --}}


            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

@if(Session::has('regFail'))
    <script>
        $(document).ready(function(){
            $('.sel').addClass('change');
        });
    </script>
@endif
    <script>
        function selLog(){
            $('.help-block').empty();
            $('.loginForm').css('display','block');
            $('.registerForm').css('display','none');
            $('.sel').removeClass('change');
        };
        function selReg(){
            $('.help-block').empty();
            $('.loginForm').css('display','none');
            $('.registerForm').css('display','block');
            $('.sel').addClass('change');
        };
    </script>
@endsection
