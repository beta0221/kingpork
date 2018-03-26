@extends('main')

@section('title','| 類別商品')

@section('stylesheets')
<style>
.content{
	margin:60px 0 80px 0;
}
/*-----------------------------------------------*/
.productIMG{
	/*border:1pt solid #000;*/
	width: 100%;
	padding-top:100%;
	border-radius: 0.3em;
	box-shadow: 4px 4px 16px 2px rgba(0, 0, 0, 0.15);
	overflow: hidden;
}
.productItem{
	display: inline-block;
	background: linear-gradient(0deg,rgba(195,28,34,0.5),rgba(195,28,34,1));
	color: #fff;
	padding: 4px 8px;
	margin: 0 4px 8px 4px;
	border-radius: 0.3em;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	cursor: pointer;
}
.productItem:hover{
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.5);
}
.nowItem{
	background-color: rgba(0,0,0,0.1);
}
.productRow{
	/*border:1pt solid #000;*/
	height: 112px;
	padding:8px 0 0 4px;
}
/*-------------------------------------------------*/
.productIMG>img{
	height: 100%;
	width: 100%;
	position: absolute;
	top: 0;
}
.hr{
	margin: 40px 0 40px 0;
}
.addBar{
	/*border:1pt solid #000;*/
	height: 56px;
	width: calc(100% - 30px);
	position: absolute;
	bottom: 0;
	left: 15px;
	padding:8px 4px 0 4px;
}
#addToKartBtn,#goToKartBtn,.place{
	float: right;
	margin: 0 4px 0 4px;
}
#addToKartBtn,#goToKartBtn{
	border-radius: 0.3em;
	display: none;
	padding: 4px 8px;
	cursor: pointer;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	border:none;
	color: #fff;
	outline: none;
}
#addToKartBtn:hover,#goToKartBtn:hover{
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.5);
}
#addToKartBtn{
	background: linear-gradient(0deg,rgba(225,139,31,0.6),rgba(225,139,31,1));
}
#goToKartBtn{
	background: linear-gradient(0deg,rgba(195,28,34,0.5),rgba(195,28,34,1));
}
.place{
	display: inline-block;
	padding: 7px 8px;
}
/*---------------------------------------------------*/
.menuBoard{
	background-color: rgba(255,255,255,0.5);
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	position: absolute;
	top: 0;
	left: 15px;
	height: 100%;
	width: calc(100% - 30px);
	border-radius: 0.3em;
}
.data{
	/*border:1pt solid #000;*/
	margin: 8px 0 8px 0;
	height: 56px;
	padding:8px 0 0 16px;
}
.data>span{
	font-size: 18pt;
}
.data>h3{
	display: inline-block;
}
/*---------------------------------------------------*/
.productsBar{
	height: 160px;
	/*border:1pt solid #000;*/
	margin-bottom: 60px;
}
.catImg{
	overflow: hidden;
	height: 100%;
	border-radius: 0.3em;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	filter: opacity(50%);
}
.catImg:hover{
	filter:opacity(100%);
}
.catImg>img{
	max-width: 100%;
	top: -50%;
}
#currentCat{
	filter:opacity(100%);
	border:4pt solid rgba(195,28,34,1);;
}
.P-pork::before{
	content: "排骨";
	color: #fff;
	font-size: 22pt;
	left: 16px;
	top: 8px;
	position: absolute;
	z-index: 1;
}
.P-both::before{
	content: "幸福雙響";
	color: #fff;
	font-size: 22pt;
	left: 16px;
	top: 8px;
	position: absolute;
	z-index: 1;
}
.P-chicken::before{
	content: "雞腿";
	color: #fff;
	font-size: 22pt;
	left: 16px;
	top: 8px;
	position: absolute;
	z-index: 1;
}
.flash,.flashRed{
	height: 56px;
	width: calc(100% - 30px);
	border-radius: 0.3rem;
	
	box-shadow: 4px 4px 16px 2px rgba(0, 0, 0, 0.3);
	position: absolute;
	bottom: 56px;
	padding: 16px 24px;
	color: #fff;
	display: none;
	transition: ease-in-out 1s;
}
.flash{
	background-color: rgba(41,112,245,0.8);
}
.flashRed{
	background-color: rgba(201,44,63,0.8);
}
</style>
@endsection

@section('content')



<div class="content">
	<div class="container">
		<div class="row productsBar">

			<div class="col-md-4">
				<div id="{{Request::is('productCategory/3') ? 'currentCat' : ''}}" class="catImg P-pork">
					<a href="{{route('productCategory.show',3)}}">
						<img src="{{asset('images/productsIMG/both.jpg')}}" alt="">	
					</a>
				</div>
				
			</div>
			<div class="col-md-4">
				<div id="{{Request::is('productCategory/4') ? 'currentCat' : ''}}" class="catImg P-both">
					<a href="{{route('productCategory.show',4)}}">
						<img src="{{asset('images/productsIMG/both.jpg')}}" alt="">	
					</a>
				</div>
				
			</div>
			<div class="col-md-4">
				<div id="{{Request::is('productCategory/2') ? 'currentCat' : ''}}" class="catImg P-chicken">
					<a href="{{route('productCategory.show',2)}}">
						<img src="{{asset('images/productsIMG/chicken.jpg')}}" alt="">
					</a>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="productIMG">
					<img id="productIMG" src="{{asset('images/productsIMG') . '/'}}{{Request::is('productCategory/3') ? 'pork.jpg' : ''}}{{Request::is('productCategory/4') ? 'both.jpg' : ''}}{{Request::is('productCategory/2') ? 'chicken.jpg' : ''}}" alt="">
				</div>
			</div>
			<div class="col-md-6">
				<div class="menuBoard"></div>
				<div class="productRow">
					@foreach($productCategory->products as $product)
						<div onclick="showProduct({{$product->id}});" class="productItem"><span>{{$product->name}}</span></div>
					@endforeach
				</div>

				<hr style="margin: 0;width: 95%;margin: 0 auto;">
				
				<div class="data name">
					<h2>
						{{Request::is('productCategory/3') ? '厚切手打豬排' : ''}}
						{{Request::is('productCategory/4') ? '幸福雙享組合' : ''}}
						{{Request::is('productCategory/2') ? '無骨嫩雞腿排' : ''}}		
					</h2>
				</div>
				<hr style="margin: 0;width: 95%;margin: 0 auto;">
				<div class="data format"><span>規格：</span><h3></h3></div>
				<hr style="margin: 0;width: 95%;margin: 0 auto;">
				<div class="data price"><span>售價：</span><h3></h3></div>
				<hr style="margin: 0;width: 95%;margin: 0 auto;">
				<div class="data bonus"><span>紅利：</span><h3></h3></div>
				
				<div class="flash">
					<span></span>
				</div>
				<div class="flashRed">
					<span></span>
				</div>

				<div class="addBar">
					
					<button id="goToKartBtn" onclick="location.href='{{route('kart.index')}}'">前往結帳</button>
					<button id="addToKartBtn" onclick="">加入購物車</button>
					<div class="place"><span></span></div>
					
				</div>
			</div>
		</div>
		<hr class="hr">
		<div class="row">
			<div class="col-md-10 offset-md-1 aboutContent">
				<p>
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Natus labore, consequatur, quas eius velit consequuntur, nesciunt dolores earum tempore, praesentium enim itaque nisi officiis. Molestiae, ut! Iure obcaecati culpa laboriosam eum voluptatum reiciendis voluptatibus esse, cumque voluptate similique provident quas recusandae minus debitis molestias cum veniam porro, dolore ea eos, ab assumenda sunt odio est possimus? Voluptatibus vel ab odit necessitatibus quas delectus tempore id asperiores velit culpa? Illum pariatur quod eaque, aperiam quos quae vero et voluptates tempora voluptas perferendis illo, iure veniam, dicta itaque alias eius explicabo natus aut. Dolorum quaerat qui nulla perferendis ut, totam? Iste, dolorem?
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptate ipsam, quisquam voluptatibus laudantium aut repellendus a ullam modi adipisci numquam sint, dignissimos officiis alias earum maiores animi. Atque asperiores nulla pariatur fuga, recusandae nisi! Harum at sequi natus aliquid qui vel fugit excepturi, officiis ipsum nemo iusto quis, deleniti fugiat quos accusantium similique nihil aspernatur 

				<br>
				facilis asperiores! Dolore, commodi! Sapiente iure illum nisi sit, cupiditate, pariatur odit ratione ipsam reiciendis nam explicabo ducimus doloremque eligendi tempora velit a molestias! A asperiores facere fugit perspiciatis cumque ut cupiditate iste quia iure. Rerum dolores perspiciatis iste atque aliquam fuga id placeat voluptate, eaque fugiat sed doloribus qui provident repellat quos officia enim in magnam 

				<br>
				suscipit, hic eum, odio quam deleniti. Similique at, laborum rerum neque unde omnis tempore beatae debitis molestiae asperiores eius doloremque quibusdam, delectus minus mollitia inventore, eveniet, ut laudantium aut aperiam iusto deleniti cumque. Nulla fuga possimus, quibusdam magnam incidunt dicta. Voluptates repellat nostrum pariatur inventore commodi. Voluptatem sapiente voluptatibus quo maiores nisi odit quis quisquam veritatis similique possimus illum nam, earum eaque, molestiae perspiciatis nihil cumque debitis veniam corporis itaque, atque suscipit? Non perspiciatis ea fuga fugit quia quisquam atque iste ab magnam eaque delectus rerum, nesciunt facilis.
				</p>
			</div>
		</div>
	</div>
</div>


@endsection


@section('scripts')
<script>
	function showProduct(id){

		$.ajaxSetup({
  			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
  			}
		});
		$.ajax({

			type:'GET',
			url:'{{url('products')}}' +'/' + id,
			dataType:'json',
			success: function (response) {

				$('.name>h2').empty().append(response.name);
                $('.format>h3').empty().append(response.format);
                $('.price>h3').empty().append(response.price);
                $('.bonus>h3').empty().append(response.bonus);
                $('.aboutContent').empty().append(response.content);
                $('#productIMG').attr('src','{{asset('images/productsIMG') . '/'}}'+response.image);

                if (response.add == true) {
                	$('#addToKartBtn').css('display','none');
                	$('.place>span').empty().append('已加入購物車');
                }else{
                	$('#addToKartBtn').css('display','inline-block');
                	$('.place>span').empty();
                	$('#addToKartBtn').attr('onclick','addToKart('+ response.id +');');	
                }
                $('#goToKartBtn').css('display','inline-block');
            },
            error: function () {
                alert('錯誤');
            },
		});

	};

	function addToKart(id){
		$.ajaxSetup({
  			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  			}
		});
		$.ajax({
			type:'POST',
			url:'{{route('kart.store')}}',
			dataType:'json',
			data: {
				'product_id':id,
			},
			success: function (response) {
                // alert(response.msg);
                $('.flash>span').empty().append(response.msg);
                $('.flash').css('display','block');

                setTimeout(function(){
                	$('.flash>span').empty();
					$('.flash').css({'display':'none'});                	
                },2000);
                $('#addToKartBtn').css('display','none');
                $('.place>span').empty().append('已加入購物車');
                // navbar cart 加一
                var inKart = parseInt($('#inKart').html()) + 1;
                $('#inKart').empty().append(inKart);
            },
            error: function (data) {
                
                $('.flashRed>span').empty().append('無法加入購物車，請先登入會員');
                $('.flashRed').css('display','block');
                setTimeout(function(){
                	$('.flashRed>span').empty();
					$('.flashRed').css({'display':'none'});                	
                },2000);
            }
		});
	}

$(document).ready(function(){
	$('.productItem').click(function(){
		$('.nowItem').removeClass('nowItem');
		$(this).addClass('nowItem');
	});
});
</script>
@endsection