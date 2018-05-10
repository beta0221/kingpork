{{-- <div class="navbar">
	<div class="container">
		<div class="row">
			<div class="navLogo col-md-3">
				<a href="{{url('/')}}"><img src="{{asset('images/productsIMG/logo.jpg')}}" alt=""></a>
			</div>
			<div class="navLeft col-md-4 ">
				<div class="itemBox">
					<ul class="goto">
						<li class="navItem"><a href="{{route('productCategory.show',1)}}">產品總覽</a></li>
						<li class="navItem"><a href="">烹煮教學</a></li>
						<li class="navItem"><a href="{{route('contact')}}">聯絡我們</a></li>
					</ul>
				</div>	
			</div>
			<div class="navRight col-md-5">
				<div style="width: 100px;height: 100%;padding: 0 8px 0 8px;position: absolute;right: 0;">
					<div class="fbLink"><img src="{{asset('images/facebook.png')}}" alt=""></div>
					<div class="lineLink"><img src="{{asset('images/line.png')}}" alt=""></div>
				</div>	

				<div style="width: calc(100% - 100px);height: 100%;padding: 0 8px 0 8px;position: absolute;left: 0;">
				
				@if (Auth::guest())
				
				<div class="login">
					<ul class="goto">
						<li id="inKart" class="navLog">
							
						</li>
						<li class="navItem">
							<a href="{{route('login')}}">登入/註冊</a>
						</li>
					</ul>
					
				</div>

				<div class="kart">
					<a href="{{route('kart.index')}}">
						<img src="{{asset('images/cart.png')}}" alt="">
					</a>
				</div>
				
				@else
				
				<div class="logout">
					<ul class="goto">
						<li class="navLog">
							<a href="{{ route('logout') }}"
		                    onclick="event.preventDefault();
		                             document.getElementById('logout-form').submit();">
		                    登出
		                	</a>
						</li>
					</ul>
				</div>
				
				<div class="user">
					<ul class="goto">

						<li id="inKart" class="navLog">
							
						</li>

						<li class="navLog">
							{{ Auth::user()->name }}
						</li>

						<li class="navLog">
							<a href="{{route('bill.index')}}">我的訂單</a>
						</li>

					</ul>
				</div>
				
				<div class="kart">
					<a href="{{route('kart.index')}}"><img src="{{asset('images/cart.png')}}" alt=""></a>
				</div>
				
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
				@endif
				</div>
				
			</div>	
		</div>
	</div>
</div> --}}

<nav class="navCostume navbar navbar-toggleable-sm">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <a class="navbar-brand" href="{{url('/')}}"><img src="{{asset('images/productsIMG/logo.jpg')}}" height="100%" alt=""></a>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">

    <ul class="navbar-nav mr-auto">
    	<li class="nav-item">
			<a class="nav-link pr-0" href="#"><img src="{{asset('images/line.png')}}" alt=""></a>
		</li>
		<li class="nav-item">
			<a class="nav-link pl-0" href="#"><img src="{{asset('images/facebook.png')}}" alt=""></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{route('productCategory.show',1)}}">購物趣</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#">美食廚房</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#">訂購相關</a>
		</li>
		<li class="nav-item">
	        <a class="nav-link" href="{{route('contact')}}">聯絡我們</a>
		</li>
    </ul>
    <ul class="navbar-nav mr-auto navbar-toggler-right">
    	@if (Auth::guest())
    	<li class="nav-item">
	        <a class="nav-link" href="{{route('login')}}">登入/註冊</a>
		</li>
		<li class="nav-item">
	        <a class="nav-link" href="{{route('kart.index')}}"><span id="inKart"></span><img src="{{asset('images/cart.png')}}" alt=""></a>
		</li>
		<li class="nav-item">
	        <a class="nav-link" href="{{route('kart.index')}}">結帳</a>
		</li>
		@else
		<li class="nav-item">
	        <span class="navbar-text">{{ Auth::user()->name }}</span>
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
	        <span class="navbar-text"><img src="{{asset('images/cart.png')}}" alt=""></span>
		</li>
		<li class="nav-item">
	        <span class="navbar-text" id="inKart"></span>
		</li>
		<li class="nav-item">
	        <button class="btnCostume btn btn-danger" onclick="location.href='{{route('kart.index')}}'">結帳</button>
		</li>
		@endif
    </ul>

  </div>
</nav>

<div class="topSpace"></div>

<div class="main-bg"></div>
