<nav class="navCostume navbar navbar-toggleable-sm">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <a class="navbar-brand" href="{{url('/')}}"><img src="{{asset('images/logo.png')}}" height="100%" alt=""></a>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">

    <ul class="navbar-nav mr-auto">
    	<li class="nav-item">
			<a class="nav-link pr-0" href="#"><img src="{{asset('images/line.png')}}" alt=""></a>
		</li>
		<li class="nav-item">
			<a class="nav-link pl-0" href="#"><img src="{{asset('images/facebook.png')}}" alt=""></a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{Request::is('/')?'now-page':''}}" href="{{url('/')}}">首頁</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{Request::is('productCategory/*')?'now-page':''}}" href="{{route('productCategory.show',1)}}">購物趣</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{Request::is('')?'now-page':''}}" href="#">美食廚房</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{Request::is('guide')?'now-page':''}}" href="{{route('guide')}}">訂購相關</a>
		</li>
		<li class="nav-item">
	        <a class="nav-link {{Request::is('contact')?'now-page':''}}" href="{{route('contact')}}">聯絡我們</a>
		</li>
    </ul>
    <ul class="navbar-nav mr-auto navbar-toggler-right">
    	@if (Auth::guest())
    	<li class="nav-item">
	        <a class="nav-link" href="{{route('login')}}">登入/註冊</a>
		</li>
		<li class="nav-item">
	        <a class="nav-link" href="#" data-toggle="modal" data-target="#exampleModal" onclick="event.preventDefault();ajaxShowKart();"><img src="{{asset('images/cart.png')}}" alt=""></a>
		</li>
		<li class="nav-item">
	        <span class="navbar-text" id="inKart"></span>
		</li>
		<li class="nav-item">
	        <button class="btnCostume btn btn-danger" onclick="location.href='{{route('kart.index')}}'">結帳</button>
		</li>
		@else



		<li class="nav-item">
	        <span class="navbar-text">{{ Auth::user()->name }}</span>
		</li>
		<li class="nav-item">
	        <span class="navbar-text">紅利:{{ Auth::user()->bonus }}</span>
		</li>
		<li class="nav-item">
	        <a class="nav-link" href="{{route('bill.index')}}">我的訂單</a>
		</li>
		<li class="nav-item">
	        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">登出</a>
		</li>
		<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
			{{ csrf_field() }}
        </form>
        <li class="nav-item">
	        <a class="nav-link" href="#" data-toggle="modal" data-target="#exampleModal" onclick="event.preventDefault();ajaxShowKart();"><img src="{{asset('images/cart.png')}}" alt=""></a>
		</li>
		<li class="nav-item">
	        <span class="navbar-text" id="inKart"></span>
		</li>
		<li class="nav-item">
	        <button class="btnCostume" onclick="location.href='{{route('kart.index')}}'">結帳</button>
		</li>
		@endif
    </ul>

  </div>
</nav>

<div class="marquee">
	<span class="runner"></span>
</div>

<div class="topSpace"></div>
<div class="main-bg"></div>
<div class="main-bg-filter"></div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">我的購物車</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="modal-body-table" style="width: 100%;">
        	<tr style="border-bottom: 1px solid rgba(0,0,0,0.1);">
	        	<th style="min-width: 20%">　</th>
				<th style="min-width: 50%;padding-bottom: 10px;">商品名稱</th>
				<th style="min-width: 15%">價格</th>
				<th style="min-width: 15%">　</th>
			</tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
      </div>
    </div>
  </div>
</div>
{{-- Modal --}}
