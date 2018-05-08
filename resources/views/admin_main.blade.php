<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>管理後台 @yield('title')</title>
{{Html::style('css/reset.css')}}
{{Html::style('css/bootstrap/bootstrap.min.css')}}

<style>
  .topBar{
    position: fixed;
    width: 100%;
    height: 40px;
    top: 0;
    left: 0;
    background:#222;
    z-index: 1;
  }
  .topBar ul{
    position: absolute;
    right: 80px;
    top: 0;
  }
  .topBar li{
    position: relative;
    float: left;
    line-height: 40px;
    text-align: center;
    margin: 0 12px;
    color:white;
  }
  .logoutBtn a{
    display: block;
    line-height: 40px;
    color: white;
    text-decoration: none;
    background: steelblue;
    width: 50px;
  }
  .sideBar{
    position: fixed;
    z-index: 1;
    width: 150px;
    height: 100%;
    background:#222;
    top: 40px;
    left: 0;
  }
  .content{
    width: calc(100% - 150px);
    position: absolute;
    top: 40px;
    left: 150px;
  }
  .sideBar_item{
    width: 100%;
    height: 40px;
    cursor: pointer;
    /*padding-top: 6.5px;*/
    position: relative;
  }
  .sideBar_item:hover{
    background: #3a3a3a;
  }
  .sideBar_now{
    background:steelblue;
  }
  .sideBar_now:hover{
    background:steelblue;
  }
  .sideBar a{
    display: block;
    line-height: 40px;
    color: white;
    text-decoration: none;
    margin-left: 45px;
  }
  .sideBar img{
    position: absolute;
    height: 20px;
    left: 12px;
    top: 9px;
  }
</style>
@yield('stylesheets')
</head>
<body>


<div class="topBar">
  <ul>
    <li>{{Auth::user()->name}}</li>
    <li>{{Auth::user()->job_title}}</li>
    <li class="logoutBtn"><a href="{{route('admin.logout')}}">登出</a></li>
  </ul>
</div>
<ul class="sideBar">
  <li class="sideBar_item {{Request::is('products') ? 'sideBar_now' : ''}}">
    <a href="{{route('products.index')}}"><img src="{{asset('images/admin_product.png')}}" alt="">產品管理</a>
  </li>
  <li class="sideBar_item {{Request::is('banner') ? 'sideBar_now' : ''}}">
    <a href="{{route('banner.index')}}"><img src="{{asset('images/admin_wall.png')}}" alt="">轉撥牆管理</a>
  </li>
  <li class="sideBar_item {{Request::is('order') ? 'sideBar_now' : ''}}">
    <a href="{{route('order.index')}}"><img src="{{asset('images/admin_delivery.png')}}" alt="">訂單管理</a>
  </li>
</ul>
<div class="content">
@yield('content')
</div>






</body>


{{ Html::script('js/jquery/jquery-3.2.1.min.js') }}
{{ Html::script('js/bootstrap/bootstrap.min.js') }}
@yield('scripts')
</html>