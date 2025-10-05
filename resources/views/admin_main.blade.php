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
  .topBar img{
    height: 28px;
    margin: -4px 8px 0 4px;
  }
  .topBar a{
    color: white;
    text-decoration: none;
    line-height: 40px;
    display: block;
  }
  .topBar_right{
    position: absolute;
    right: 80px;
    top: 0;
  }
  .topBar_right li{
    position: relative;
    float: left;
    line-height: 40px;
    text-align: center;
    margin: 0 12px;
    color:white;
  }
  .topBar_left{
    position: absolute;
    left:180px;
    top: 0;
  }
  .topBar_left li{
    position: relative;
    float: left;
    line-height: 40px;
    text-align: center;
    margin: 0 12px;
    color:white;
    padding: 0 12px;
  }
  .topBar_left li:hover{
    background: #3a3a3a;
  }
  .logoutBtn a{
    display: block;
    background: steelblue;
    width: 80px;
    transition: 0.1s ease-in-out;
  }
  .logoutBtn a:hover{
    background:#0275d8;
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
    height: calc(100% - 40px);
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

  .sideBar_item .badge{
    position: absolute;
    left: 6px;
    top: 4px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background-color: red;
    text-align: center;
    font-size: 2pt;
    line-height: 18px;
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
  ul.pagination a,ul.pagination span{
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    line-height: 1.42857143;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd;
  }
  ul.pagination a{
    color: #337ab7;
  }
  ul.pagination span{
    color: gray;
  }
  ul.pagination li.active span{
    z-index: 3;
    color: #fff;
    cursor: default;
    background-color: #337ab7;
    border-color: #337ab7;
  }
</style>
@yield('stylesheets')
</head>
<body>


<div class="topBar">
  <ul class="topBar_left">
    <li><a href="/" target="_blank"><img src="{{asset('images/admin_haveLook.png')}}">瀏覽網頁</a></li>

    <li><a href="https://www.kingpork.com.tw/kingblog/wp-admin/" target="_blank"><img src="{{asset('images/admin_wordpress.png')}}">廚房後台</a></li>
    
    <li><a href="https://vendor.ecpay.com.tw/" target="_blank"><img src="{{asset('images/admin_ecpay.png')}}"></a></li>

    <li><a href="https://analytics.google.com/analytics/web/?hl=zh-TW&pli=1#/realtime/rt-overview/a121883818w179867443p178085533" target="_blank"><img src="{{asset('images/admin_GA.png')}}"></a></li>

    <li><a href="https://my.vultr.com/subs/?SUBID=14512165" target="_blank"><img src="{{asset('images/admin_server.png')}}"></a></li>




    
  </ul>
  <ul class="topBar_right">
    <li>{{Auth::user()->job_title}}</li>
    <li>{{Auth::user()->name}}</li>
    <li class="logoutBtn"><a href="{{route('admin.logout')}}">登出</a></li>
  </ul>
</div>
<ul class="sideBar">
  <li class="sideBar_item {{Request::is('admin') ? 'sideBar_now' : ''}}">
    <a href="/admin"><img src="{{asset('images/admin_dashboard.png')}}" alt="">儀表板</a>
  </li>
  <li class="sideBar_item {{Request::is('admin/funnel-analytics*') ? 'sideBar_now' : ''}}">
    <a href="{{route('admin.funnel.index')}}"><img src="{{asset('images/admin_dashboard.png')}}" alt="">轉換率監控</a>
  </li>
  <li class="sideBar_item {{Request::is('inventory*') ? 'sideBar_now' : ''}}">
    <a href="{{route('inventory.index')}}"><img src="{{asset('images/admin_category.png')}}" alt="">庫存清單</a>
  </li>
  <li class="sideBar_item {{Request::is('psi*') ? 'sideBar_now' : ''}}">
    <a href="{{route('psi.index')}}"><img src="{{asset('images/admin_category.png')}}" alt="">進銷存</a>
  </li>
  <li class="sideBar_item {{Request::is('retailer*') ? 'sideBar_now' : ''}}">
    <a href="{{route('retailer.index')}}"><img src="{{asset('images/admin_category.png')}}" alt="">通路清單</a>
  </li>
  <li class="sideBar_item {{Request::is('productCategory*') ? 'sideBar_now' : ''}}">
    <a href="{{route('productCategory.index')}}"><img src="{{asset('images/admin_category.png')}}" alt="">產品類別</a>
  </li>
  <li class="sideBar_item {{Request::is('products*') ? 'sideBar_now' : ''}}">
    <a href="{{route('products.index')}}"><img src="{{asset('images/admin_product.png')}}" alt="">產品管理</a>
  </li>
  <li class="sideBar_item {{Request::is('banner*') ? 'sideBar_now' : ''}}">
    <a href="{{route('banner.index')}}"><img src="{{asset('images/admin_wall.png')}}" alt="">轉撥牆管理</a>
  </li>
  <li class="sideBar_item {{Request::is('runner*') ? 'sideBar_now' : ''}}">
    <a href="{{route('runner.index')}}"><img src="{{asset('images/admin_marquee.png')}}" alt="">跑馬燈管理</a>
  </li>
  <li class="sideBar_item {{Request::is('contact*') ? 'sideBar_now' : ''}}">
    <a href="{{route('contactManage.index')}}"><img src="{{asset('images/admin_mail.png')}}" alt="">客服管理</a>
    <div class="badge contact-badge"></div>
  </li>
  <li class="sideBar_item {{Request::is('order*') ? 'sideBar_now' : ''}}">
    <a href="{{route('order.index')}}"><img src="{{asset('images/admin_delivery.png')}}" alt="">訂單管理</a>
  </li>
  <li class="sideBar_item {{Request::is('admin-kingblog') ? 'sideBar_now' : ''}}">
    <a href="/admin-kingblog"><img src="{{asset('images/admin_wordpress.png')}}" alt="">美食廚房</a>
  </li>
  
</ul>
<div class="content">
@yield('content')
</div>






</body>


{{ Html::script('js/jquery/jquery-3.2.1.min.js') }}
{{ Html::script('js/bootstrap/bootstrap.min.js') }}
@yield('scripts')
<script>
  $.ajax({
		type:'GET',
		url:'/sumPendingContact',
		dataType:'json',
		success:function(res){
      $('.contact-badge').html(res.sum);
      if(res.sum == 0){
        $('.contact-badge').html('');
      }
		},
		error:function(){
			alert('錯誤');
		}
	});
</script>
</html>