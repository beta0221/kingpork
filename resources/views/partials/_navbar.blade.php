<div class="navCostume">

  <a class="navbar-logo" href="{{url('/')}}">
  	<img src="{{asset('images/logo.png')}}" alt="金園排骨">
  </a>


	    <ul class="navbar-ul navbar-ul-left">
	    	{{-- <li id="line-href" class="navbar-li">
	    		<a href="{{url('about-line')}}"><img src="{{asset('images/line.png')}}" alt=""></a>
	    	</li>
	    	<li id="facebook-href" class="navbar-li">
	    		<a href="https://www.facebook.com/KINGPORK/" target="_blank"><img src="{{asset('images/facebook.png')}}" alt=""></a>
	    	</li> --}}
	    	<li class="navbar-li">
	    		<a class="{{Request::is('/')?'now-page':''}}" href="{{url('/')}}">首頁</a>
	    	</li>
	    	<li class="navbar-li">
	    		<a class="{{Request::is('productCategory/*')?'now-page':''}}" href="{{route('productCategory.show',1)}}">購物趣</a>
	    	</li>
	    	<li class="navbar-li">
	    		<a href="/kingblog/">美食廚房</a>
	    	</li>
			{{-- <li class="navbar-li">
	    		<a class="{{Request::is('')?'now-page':''}}" href="#">最新消息</a>
	    	</li> --}}
	    	<li class="navbar-li">
	    		<a class="{{Request::is('guide')?'now-page':''}}" href="{{route('guide')}}">訂購相關</a>
	    	</li>
	    	{{-- <li class="navbar-li">
	    		<a class="{{Request::is('')?'now-page':''}}" href="#">食在安心</a>
	    	</li> --}}
	    	<li class="navbar-li">
	    		<a class="{{Request::is('contact')?'now-page':''}}" href="{{route('contact')}}">聯絡我們</a>
	    	</li>
	    	<li id="navbar-ul-left-close" class="navbar-li">
	    		<button onclick="burgerDown();">X</button>
	    	</li>
	    </ul>
	    	

	    <ul class="navbar-ul navbar-ul-right">
	    	@if (Auth::guest())
	    	<li class="navbar-li">
		        <a href="{{route('login')}}">登入/註冊</a>
			</li>
			<li class="navbar-li">
		        <a href="#" data-toggle="modal" data-target="#exampleModal" onclick="event.preventDefault();ajaxShowKart();"><img src="{{asset('images/cart.png')}}"></a>
			</li>
			<li class="navbar-li">
		        <span id="inKart"></span>
			</li>
			<li class="navbar-li">
		        <button class="btnCostume btn btn-danger" onclick="location.href='{{route('kart.index')}}'">結帳</button>
			</li>

			@else

			<li class="navbar-li">
		        <span>{{ Auth::user()->name }}</span>
			</li>
			<li class="navbar-li">
		        <span>紅利:{{ Auth::user()->bonus }}</span>
			</li>
			<li class="navbar-li">
		        <a href="{{route('bill.index')}}">我的訂單</a>
			</li>
			<li class="navbar-li">
		        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">登出</a>
			</li>
			<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
				{{ csrf_field() }}
	        </form>
	        <li class="navbar-li">
		        <a href="#" data-toggle="modal" data-target="#exampleModal" onclick="event.preventDefault();ajaxShowKart();"><img src="{{asset('images/cart.png')}}"></a>
			</li>
			<li class="navbar-li">
		        <span id="inKart"></span>
			</li>
			<li class="navbar-li">
		        <button class="btnCostume" onclick="location.href='{{route('kart.index')}}'">結帳</button>
			</li>
			@endif
	    </ul>

  
</div>

@if(!Request::is('/'))
<div class="marquee">
	<span class="runner"></span>
</div>
<div class="topSpace"></div>
@else
<div class="topSpace-landing"></div>
@endif


<div class="main-bg"></div>
<div onclick="burgerUp();" class="burger"><img src="{{asset('images/burger.png')}}"></div>

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
