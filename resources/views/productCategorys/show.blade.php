@extends('main')

@section('title','| 購物趣')

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
	height: 56px;
	line-height: 56px;
	color: #000;
	padding: 0px 92px 0 12px;
	font-size: 22px;
	cursor: pointer;
}
.productItem:hover{
	background-color: rgba(0,0,0,0.1);
}
.nowItem{
	background-color: rgba(0,0,0,0.1);
}
.productPrice{
	float: right;
	color: #c80013;
}
.productPrice_avg{
	color:#000;
	font-size: 13px;
}
.titleRow{
	text-align: center;
	height: 100px;
	background: linear-gradient(0deg,rgba(217,83,79,0.9),rgba(217,83,79,1));
	border-radius: 0.3em 0.3em 0 0;
}
.titleRow h1{
	line-height: 100px;
	letter-spacing: 2px;
	color: white;
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
.addToKartBtn{
	position: absolute;
	color: white;
	top: 8px;
	right: 8px;
	padding: 0;
	height: 40px;
	width: 76px;
	font-size: 18px;
	cursor: pointer;
	line-height: 40px;
	border:none;
	border-radius: .3em;
	background:#f0ad4e;
	{{Auth::user()?'display: none;':''}}
}
.addToKartBtn img{
	height: 20px;
}
.deleteKartBtn{
	background:rgba(0,0,0,0.3);
}
.goToKartBtn{
	position: absolute;
	bottom: 12px;
	right: 28px;
	padding: 0 32px;
	height: 80px;
    font-size: 40px;
    border-radius: 0.3rem;
    color: #fff;
    background-color: #d9534f;
    font-weight: 500;
    letter-spacing: 2px;
    line-height: 80px;
    text-align: center;
    border:none;
    cursor: pointer;
    transition: all .1s ease-in-out;
}
.goToKartBtn img{
	height: 40px;
	transform: translate(8px,-5px);
}
.goToKartBtn:hover{
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.5);
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
	height: 80px;
	width: 40%;
	border-radius: 0.3rem;
	box-shadow: 4px 4px 16px 2px rgba(0, 0, 0, 0.3);
	position: absolute;
	bottom: 12px;
	left: 28px;
	padding: 16px 24px;
	color: #fff;
	display: none;
	transition: ease-in-out 1s;
}
.flash{
	background-color: green;
}
.flashRed{
	background-color: red;
}
</style>
@endsection

@section('content')

<div class="content">
	<div class="container">
		<div class="row productsBar">

			<div class="col-md-4">
				<div id="{{Request::is('productCategory/1') ? 'currentCat' : ''}}" class="catImg P-pork">
					<a href="{{route('productCategory.show',1)}}">
						<img src="{{asset('images/productsIMG/both.jpg')}}" alt="">	
					</a>
				</div>
				
			</div>
			<div class="col-md-4">
				<div id="{{Request::is('productCategory/3') ? 'currentCat' : ''}}" class="catImg P-both">
					<a href="{{route('productCategory.show',3)}}">
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
					<img id="productIMG" src="{{asset('images/productsIMG') . '/'}}{{Request::is('productCategory/1') ? 'pork.jpg' : ''}}{{Request::is('productCategory/3') ? 'both.jpg' : ''}}{{Request::is('productCategory/2') ? 'chicken.jpg' : ''}}" alt="">
				</div>
			</div>
			<div class="col-md-6">
				<div class="menuBoard"></div>
				<div class="titleRow">
					<h1>
						{{Request::is('productCategory/1') ? '厚切手打豬排' : ''}}
						{{Request::is('productCategory/3') ? '幸福雙享組合' : ''}}
						{{Request::is('productCategory/2') ? '無骨嫩雞腿排' : ''}}		
					</h1>
				</div>

				<hr style="margin: 0;width: 95%;margin: 0 auto;">
				
				@if(Auth::user())

					@foreach($productCategory->products as $product)
						<div onclick="showProduct({{$product->id}});" class="productItem">
							<span>{{$product->name}}</span>
							<span class="productPrice">${{$product->price}}元</span>
							<span class="productPrice productPrice_avg">（均價${{$product->format}}）</span>
							<button id="add_{{$product->id}}" class="addToKartBtn" onclick="addToKart({{$product->id}})" product_id="{{$product->id}}">
								加入<img src="{{asset('images/cart.png')}}">
							</button>
						</div>
					@endforeach

				@else

					@foreach($productCategory->products as $product)

						@if(Session::has('item'))
							<div onclick="showProduct({{$product->id}});" class="productItem">
								<span>{{$product->name}}</span>
								<span class="productPrice">${{$product->price}}元</span>
								<span class="productPrice productPrice_avg">（均價${{$product->format}}）</span>

								<button id="add_{{$product->id}}" class="addToKartBtn {{in_array($product->id,Session::get('item'))?'deleteKartBtn':''}}" 
									onclick="location.href='/{{in_array($product->id,Session::get('item'))?'deleteFromSes':'addToSes'}}/{{$product->id}}'" 
									>
									{{in_array($product->id,Session::get('item'))?'取消':'加入'}}
									<img src="{{asset('images/cart.png')}}">
								</button>
							</div>
						@else
							<div onclick="showProduct({{$product->id}});" class="productItem">
								<span>{{$product->name}}</span>
								<span class="productPrice">${{$product->price}}元</span>
								<span class="productPrice productPrice_avg">（均價${{$product->format}}）</span>

								<button id="add_{{$product->id}}" class="addToKartBtn" 
									onclick="location.href='/addToSes/{{$product->id}}'" 
									>
									加入
									<img src="{{asset('images/cart.png')}}">
								</button>
							</div>
						@endif
						
					@endforeach

				@endif
				

				<div class="flash">
					<span></span>
				</div>
				<div class="flashRed">
					<span></span>
				</div>
					
				<button id="goToKartBtn" class="goToKartBtn" onclick="location.href='{{route('kart.index')}}'">前往結帳<img src="{{asset('images/point.png')}}" alt=""></button>
					
			</div>
		</div>
		<hr class="hr">
		<div class="row">
			<div class="col-md-10 offset-md-1 aboutContent">
				@if(Request::url() == config('app.url').'/productCategory/1')
				<p>小簡介：上等里肌肉 &rarr; 手工抓捏、拍打&rarr; 再真空按摩五十分鐘 &rarr; 造就出獨持口感</p>
				<p><strong>【特級厚切手打豬排（調理生排骨肉）】</strong></p>
				<p>◆重量／容量：200g&plusmn;5%／包</p>
				<p>◆內容物：豬排肉、地瓜粉、水、黑胡椒、醬油、 砂糖、L-麩酸鈉 (味精)</p>
				<p>◆產地：台灣</p>
				<p>◆有效期限：180天</p>
				<p>◆食用方式：無須退凍，泡水5分鐘即可退凍（視季節當時水溫而論）平底鍋，中火煎雙面各煎1~2分鐘</p>
				<p>◆保存方式：冷凍</p>
				<p>◆包裝方式：單片真空包裝</p>
				<p><img src="/images/articleIMG/123-350.jpg" /></p>
				<p>---------------------------------------------------------------------------------</p>
				<p>貼心小教學</p>
				<p>---------------------------------------------------------------------------------◎本產品造型、顏色以實物為主</p>
				<p>◎商品圖片僅供實物參考。內容物組成以實物及商品說明為主</p>
				<p>◎注意事項：本為食品特殊類別，ㄧ經拆封或食物、包裝碰撞變形或保存不良導致變質</p>
				<p 、非運送過程失溫導致食品變質者，恕無法退換貨，敬請見諒與配合。</p>
				<p>◎退貨事項：除商品本身有瑕疵可辦理退貨，商品一經使用或損毀即不可退貨 退貨必須保 留紙箱及商品</p>
				<p>--------------------------------------------------------------------------------</p>
				<p>金園廚房</p>
				<p>【一】排骨肉片切絲先入鍋拌炒至8分熟即可加入配料成為湯麵或鮮肉湯。<br /><br />【二】排骨肉片切絲當一般肉絲用，可炒韭黃、高麗菜等新鮮蔬果。<br /><br />【三】排骨肉片切丁炒飯，風味更佳。<strong><br /><br /></strong>【四】中秋佳節烤肉的最佳美味肉品(不需醃製，可以直接烤喔!)。</p>
				@endif
				@if(Request::url() == config('app.url').'/productCategory/2')
				<div class="keyProductImg">
				<div id="wrap">
				<div><img src="/images/articleIMG/雞腿1.jpg" /></div>
				<div class="mousetrap"><span style="line-height: 1.3;"></span><span style="line-height: 1.3;">CAS本土肉雞腿&rarr;去骨醃製&rarr;真空按摩五十分鐘&rarr;成就嫩滑頂級滋味，吃一次就愛上它</span></div>
				<div class="mousetrap"><span style="line-height: 1.3;">---------------------------------------------------------------</span></div>
				<div class="mousetrap">
				<p><span style="color: #993300; font-size: 14pt;"><strong>嚴選無骨嫩雞腿排（調理無骨生雞腿）</strong></span></p>
				<p>重量/容量：調理生雞腿排220g &plusmn; 5% /包</p>
				<p>產地：台灣</p>
				<p>有效期限：180天<br /><br />保存方式： -18℃ 冷凍<br /><br />包裝方式：單片真空包裝</p>
				<p><img src="/images/articleIMG/真空g.jpg" width="238" height="255" /></p>
				<p>◎本產品造型、顏色以實物為主。</p>
				<p>◎商品圖片僅供實物參考。內容物組成以實物及商品說明為主。</p>
				<p>◎注意事項：本為食品特殊類別，ㄧ經拆封或食物、包裝碰撞變形或保存不良導</p>
				<p> 致變質、非運送過程失溫導致食品變質者，恕無法退換貨，敬請見諒與配合。</p>
				<p>◎退貨事項：除商品本身有瑕疵可辦理退貨，商品一經使用或損毀即不可退貨，<br /> 退貨必須保留紙箱及商品</p>
				<p></p>
				<p><span style="font-size: 14pt;"><strong>貼心小教學雞腿料理</strong></span></p>
				<p><strong>．方便迅速</strong></p>
				<p><img src="/images/articleIMG/G退凍拷貝.jpg" width="264" height="198" /><br /><br />不需要事先解凍喔﹗可直接將真空包放入冷水中約5分鐘即可 (視季節當時水溫而論)。<br /><br /><br /><strong>．</strong><strong>少油多健康</strong><strong></strong>(四種方式可參考)</p>
				<p><br /><strong>A．小火煎10～12分鐘。</strong><br /><br /><br /><img src="http://www.kingpork.com.tw/ckfinder/userfiles/images/20140129141347_87335.jpg" alt="雞腿料理、酥脆大雞腿、好吃的炸雞腿、桃園多汁炸雞腿" width="500" height="250" /></p>
				<div><hr /></div>
				<p><br /><strong>B．氣炸鍋</strong><br /><br />  (飛牌)150&deg;約15分即可，無需翻面(視雞腿大小)<br /><br /> (他牌)單面180&deg; 約10分，翻面再180&deg; 約8分即可(視雞腿大小)<br /><br /><img src="http://www.kingpork.com.tw/ckfinder/userfiles/images/20140129141418_66949.jpg" alt="雞腿料理、酥脆大雞腿、好吃的炸雞腿、桃園多汁炸雞腿" width="500" height="250" /></p>
				<p></p>
				<div><hr /></div>
				<p><br /><br /><strong>C．陽春型烤箱烤12～15分(視雞腿大小)</strong><br /><br /><img src="http://www.kingpork.com.tw/ckfinder/userfiles/images/20140129141437_32898.jpg" alt="雞腿料理、酥脆大雞腿、好吃的炸雞腿、桃園多汁炸雞腿" width="500" height="250" /></p>
				<div><br /></div>
				<p>D. 微波5分鐘至半熟，再以中小火雙面各煎4-5分鐘，最後大火收油</p>
				<p> 20秒即可<br /></p>
				<hr />
				<p><br /><strong>．美味上桌</strong><br /><br />可依個人喜好加入些許胡椒鹽或檸檬汁，不出門也能輕鬆享受美味!<br /></p>
				<p><img src="http://www.kingpork.com.tw/ckfinder/userfiles/images/20140129121355_16085.jpg" alt="雞腿料理、酥脆大雞腿、好吃的炸雞腿、桃園多汁炸雞腿" width="435" height="290" /><br /></p>
				<div><hr /></div>
				<p><br /><strong>金園廚房</strong></p>
				<p><span style="background-color: #800000;"><span style="color: #ffffff;">香酥炸雞</span></span></p>
				<p>隔水解凍後可裹上炸雞粉或直接油炸都可。</p>
				<p><span style="background-color: #800000;"><span style="color: #ffffff;">咖哩雞肉</span></span></p>
				<p>雞腿肉、洋蔥、馬鈴薯、紅蘿蔔切成小塊全部拌炒一下，再加水淹過<br />食材燉煮到自己喜歡熟度再加入咖哩塊融化即可，(依個人喜好再自行<br />調整)。<br /></p>
				<p><span style="color: #ffffff; background-color: #800000;">鹽酥雞米花</span></p>
				<p>去骨雞腿肉切成小塊，裹上炸雞粉或直接油炸，起鍋後再依個人喜好灑<br />上胡椒粉。</p>
				</div>
				</div>
				</div>
				@endif
				@if(Request::url() == config('app.url').'/productCategory/3')
				<p><strong>金園精選大幸福雙饗組合</strong></p>
				<p>重量/容量：調理生豬排200g &plusmn; 5% /包<br />調理生雞腿排220g &plusmn; 5% /包</p>
				<p>內容物 雞腿肉、地瓜粉、醬油、黑胡椒、砂糖、L-麩酸鈉(味精)</p>
				<p>內容物：CAS、TFP認證調理生排骨肉、調理生雞腿肉</p>
				<p>產地：台灣</p>
				<p>有效期限：180天</p>
				<p>保存方式： -18℃ 冷凍</p>
				<p>包裝方式：單片真空包裝</p>
				<p></p>
				<p>◎金園肉品皆為每天手工方式新鮮現做</p>
				<p>每日產量有限，請先預約到貨日，如需等候敬請見諒！</p>
				<p></p>
				<p>◎本產品造型、顏色以實物為主。</p>
				<p>◎商品圖片僅供實物參考。內容物組成以實物及商品說明為主。</p>
				<p>◎注意事項：本為食品特殊類別，ㄧ經拆封或食物、包裝碰撞變形或保存不良導</p>
				<p>致變質、非運送過程失溫導致食品變質者，恕無法退換貨，敬請見諒與配合。</p>
				<p>◎退貨事項：除商品本身有瑕疵可辦理退貨，商品一經使用或損毀即不可退貨，<br />退貨必須保留紙箱及商品<br /></p>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>

@if (Auth::user())

	$(document).ready(function(){

		$('.productItem').click(function(){			//點擊後加入nowItem Class
			$('.nowItem').removeClass('nowItem');
			$(this).addClass('nowItem');
		});

		$('.addToKartBtn').each(function(){
			var id = $(this).attr('product_id');
			
			$.ajax({
				type:'GET',
				url:'/checkIfKart/'+$(this).attr('product_id'),
				dataType:'json',
				success: function (response) {
					if (response.msg == true) {
						$('#add_'+id).empty().append('取消<img src="{{asset('images/cart.png')}}">');
						$('#add_'+id).addClass('deleteKartBtn');
						$('#add_'+id).attr('onclick','deleteFromKart('+id+')');
					}
	            },
	            error: function () {
	                // alert('錯誤');
	            },
			});
		});
		setTimeout(function(){
			$('.addToKartBtn').css('display','block');
		},500);

	});


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
                
                $('#add_'+id).empty().append('取消<img src="{{asset('images/cart.png')}}">');
                $('#add_'+id).addClass('deleteKartBtn');
                $('#add_'+id).attr('onclick','deleteFromKart('+id+')');
                // navbar cart 加一
                var inKart = parseInt($('#inKart').html()) + 1;
                $('#inKart').empty().append(inKart);
                
            },
            error: function (data) {
                alert(response);
                $('.flashRed>span').empty().append('無法加入購物車，請先登入會員');
                $('.flashRed').css('display','block');
                setTimeout(function(){
                	$('.flashRed>span').empty();
					$('.flashRed').css({'display':'none'});                	
                },2000);
            }
		});
	}

	function deleteFromKart(id){
		$.ajaxSetup({
  			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  			}
		});
		$.ajax({
			type:'POST',
			url:'/kart/'+id,
			dataType:'json',
			data: {
				_method: 'delete',
			},
			success: function (response) {
                if (response.status == 1) {
                	 $('#add_'+id).empty().append('加入<img src="{{asset('images/cart.png')}}">');
	                $('#add_'+id).removeClass('deleteKartBtn');
	                $('#add_'+id).attr('onclick','addToKart('+id+')');
                	// navbar cart 減一
	                var inKart = parseInt($('#inKart').html()) - 1;
	                $('#inKart').empty().append(inKart);
	                
                }
            },
            error: function () {
                alert('無法從購物車中刪除');
            }
		});
	}

@endif

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

                $('.aboutContent').empty().append(response.content);
                $('#productIMG').attr('src','{{asset('images/productsIMG') . '/'}}'+response.image);

            },
            error: function () {
                // alert('錯誤');
            },
		});

	};
</script>
@endsection