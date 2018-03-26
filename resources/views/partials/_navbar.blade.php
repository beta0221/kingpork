<div class="navbar">
	<div class="container">
		<div class="row">
			<div class="navLogo col-md-3">
				<a href="{{url('/')}}"><img src="http://localhost:8000/images/productsIMG/logo.jpg" alt=""></a>
			</div>
			<div class="navLeft col-md-4 ">
				<div class="itemBox">
					<ul class="goto">
						<li class="navItem"><a href="{{route('productCategory.show',3)}}">產品</a></li>
						<li class="navItem"><a href="">烹煮教學</a></li>
						<li class="navItem"><a href="{{route('contact')}}">聯絡我們</a></li>
					</ul>
				</div>	
			</div>
			<div class="navRight col-md-5">
				<div style="width: 100px;height: 100%;padding: 0 8px 0 8px;position: absolute;right: 0;">
					<div class="fbLink"><img src="http://localhost:8000/images/facebook.png" alt=""></div>
					<div class="lineLink"><img src="http://localhost:8000/images/line.png" alt=""></div>
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
						<img src="http://localhost:8000/images/cart.png" alt="">
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
					</ul>
				</div>
				
				<div class="kart">
					
					<a href="{{route('kart.index')}}"><img src="http://localhost:8000/images/cart.png" alt=""></a>
				</div>
				
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
				@endif
				</div>
				
			</div>	
		</div>
	</div>
</div>
<div class="topSpace"></div>
{{-- main-bg start--}}
<div class="main-bg"></div>
{{-- main-bg end--}}