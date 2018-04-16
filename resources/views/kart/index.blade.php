@extends('main')

@section('title','| 我的購物車')

@section('stylesheets')
<style>
.contentPage{
    width: 100%;
    height: auto;
}
.outter{
	margin-top: 60px;
	margin-bottom: 60px;
	min-height: 520px;
	/*overflow-y: scroll;*/
	padding-bottom: 80px;
	background-color: rgba(255,255,255,0.5);
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	border-radius: 0.3em;
}
.quantity{
	width: 32px;
	border:1pt solid rgba(0,0,0,0.1);
	border-radius: 4px;
	/*outline: none;*/
}
.delBtn{
	display: inline-block;
	padding: 4px 8px 4px 8px;
	border-radius: 0.3em;
	background: linear-gradient(0deg,rgba(195,28,34,0.5),rgba(195,28,34,1));
	cursor: pointer;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	border:none;
	color: #fff;
	outline: none;
}
#payBtn{
	border:none;
	outline: none;
	cursor: pointer;
	border-radius: 0.3em;
	height: 40px;
	padding-left: 20px;
	padding-right: 20px;
	margin-left: 20px;
	color: #fff;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	background: linear-gradient(0deg,rgba(225,139,31,0.6),rgba(225,139,31,1));
}
.delBtn:hover,#payBtn:hover{
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.5);
}
.kartTable td,.kartTable th{
	height: 80px;
	vertical-align: middle;
	padding: 20px 0 20px 0;
}
.kartTable tr{
	border-bottom: 1pt solid rgba(0,0,0,0.1);
}
.littleIMG{
	height: 100%;
	/*width: auto;*/
	max-width: 100%;
	max-height: 100%;
}
#payBtn,.shipping{
	display: none;
}
/*.sureToBuy,.kartTable{
	display: none;
}*/
.shipping{
	width: 100%;
}
.shipping input{
	display: inline-block;
}
.shipping span{
	margin: 0 4px 0 4px;
}
.shipping td{
	padding: 4px 0 4px 0;
	width: 100%;
}
.shipping label{
	width: 14%;
}
.radio{
	margin:0 4px 0 4px;
}
#arriveDate{
	width: 25%;
	display: none;
}
.ifThree{
	display: none;
	width: 60%;
}
.required{
	color: red;
}
</style>
@endsection

@section('content')

<div class="contentPage">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2 outter">
				
				@if(count($products) == 0)
					<div style="position: absolute;top: 50%;transform: translateY(-50%);width: calc(100% - 30px);text-align: center;">
						<h1 style="">您的購物車中目前沒有商品</h1>	
					</div>
				@else


				<form action="{{route('bill.store')}}" method="POST">
				{{csrf_field()}}
					<table class="kartTable" style="width: 100%">	
						<tr>
							<th></th>
							<th style="padding-left: 20px;">商品名稱</th>
							<th>數量</th>
							<th>價格</th>
							<th></th>
						</tr>
						@foreach($products as $product)
						<tr id="item{{$product->id}}">
							<td style="width: 80px;overflow: hidden;">
								<div style="width: 80px;height: 80px;">
									<img class="littleIMG" src="{{asset('images/productsIMG') . '/' . $product->image}}" alt="">
								</div>
							</td>

							<td style="padding-left: 20px;">
								<span>{{$product->name}}</span>
								<input style="display: none;" type="text" value="{{$product->slug}}" name="item[]">
							</td>

							<td style="width: 56px;">
								<input id="{{$product->slug}}" class="quantity" type="number" value="1" name="quantity[]" price="{{$product->price}}">
							</td>

							<td style="width:56px;">
								<span class="priceTag" id="priceTag{{$product->slug}}">{{$product->price}}</span>
							</td>

							<td style="width: 56px;">
								<div class="delBtn" data-method="delete" onclick="deleteWithAjax({{$product->id}})">刪除</div>
							</td>
						</tr>
						@endforeach
					</table>	
						
					<table class="shipping">
						<tr>
							<td>
								<label for=""><span class="required">*</span>收件人：</label>
								<input name="ship_name" type="text" class="form-control" placeholder="收件人" value="{{Auth::user()->name}}" style="width: 25%;">		
							
								<input class="radio" type="radio" name="ship_gender" value="1" checked><span>先生</span>
  								<input class="radio" type="radio" name="ship_gender" value="2"><span>小姐</span>
							</td>
						</tr>
						<tr>
							<td>
								<label for=""><span class="required">*</span>E-mail：</label>
								<input name="ship_email" type="text" class="form-control" placeholder="E-mail" style="width: 40%;" value="{{Auth::user()->email}}">
							</td>
						</tr>
						<tr>
							<td>
								<label for=""><span class="required">*</span>聯絡電話：</label>
								<input name="ship_phone" type="text" class="form-control" placeholder="聯絡電話" style="width: 25%">		
							</td>
						</tr>
						<tr>
							<td>
								<label for=""><span class="required">*</span>地址：</label>
								<select name="ship_county" class="form-control ship_county" style="width: 12%;display: inline-block;">
									<option value="">縣市</option>
									<option value="基隆市">基隆市</option>
									<option value="台北市">台北市</option>
									<option value="新北市">新北市</option>
									<option value="桃園市">桃園市</option>
									<option value="新竹市">新竹市</option>
									<option value="新竹縣">新竹縣</option>
									<option value="苗栗縣">苗栗縣</option>
									<option value="台中市">台中市</option>
									<option value="彰化縣">彰化縣</option>
									<option value="南投縣">南投縣</option>
									<option value="雲林縣">雲林縣</option>
									<option value="嘉義市">嘉義市</option>
									<option value="嘉義縣">嘉義縣</option>
									<option value="台南市">台南市</option>
									<option value="高雄市">高雄市</option>
									<option value="屏東縣">屏東縣</option>
									<option value="台東縣">台東縣</option>
									<option value="花蓮縣">花蓮縣</option>
									<option value="宜蘭縣">宜蘭縣</option>
								</select>
							
								<select name="ship_district" class="form-control ship_district" style="width: 12%;display: inline-block;">
									<option value="">地區</option>
									
									
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for=""></label>
								<input name="ship_address" type="text" class="form-control" placeholder="地址" style="width: 40%;display: inline-block;">
							</td>
						</tr>
						
						<tr>
							<td>
								<label for="">　希望到貨日:</label>

								<input id="arriveNo" class="radio" type="radio" name="ship_arrive" value="no" checked><span>不指定</span>
								<input id="arriveYes" class="radio" type="radio" name="ship_arrive" value="yes"><span>指定</span>

								<input name="ship_arriveDate" id="arriveDate" type="date" class="form-control">

								<input type="text" class="form-control" style="width: 1%;height: 42px;visibility: hidden;">
							</td>
						</tr>
						<tr>
							<td>
								<label for="">　時間：</label>
								<input name="ship_time" class="radio" type="radio" name="time" value="no" checked><span>不指定</span>
  								<input name="ship_time" class="radio" type="radio" name="time" value="13:00"><span>13:00前</span>
  								<input name="ship_time" class="radio" type="radio" name="time" value="14:00-18:00"><span>14:00-18:00</span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="">　發票：</label>
								<select name="ship_receipt" class="two-three form-control" style="width: 12%; display: inline-block;">
									<option id="two" value="2">二聯</option>
									<option id="three" value="3">三聯</option>
								</select>
								<div class="ifThree">
									<input name="ship_three_name" type="text" class="form-control ship_three" placeholder="購買人" style="width: 30%;">	
									<input name="ship_three_id" type="text" class="form-control ship_three" placeholder="統一編號" style="width: 30%;">
									<input name="ship_three_company" type="text" class="form-control ship_three" placeholder="公司名稱" style="width: 30%;">	
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="" style="vertical-align: top;">　備註：</label>
								<textarea name="ship_memo" class="form-control" style="display: inline-block;width: 40%;height: 56px;max-height: 56px;min-height: 56px;" placeholder="備註"></textarea> 
							</td>
						</tr>
						<tr>
							<td>
								<label for=""><span class="required">*</span>付款方式：</label>
								<div style="display: inline-block;border:1pt solid #000;width: 40%;text-align: center;padding: 10px 0 10px 0;border-radius: 0.3em;margin-top: 4px;">
									<input class="radio" type="radio" name="ship_pay_by" value="credit"><span>信用卡</span>
	  								<input class="radio" type="radio" name="ship_pay_by" value="atm"><span>ATM</span>
	  								<input class="radio" type="radio" name="ship_pay_by" value="cod"><span>貨到付款</span>	
								</div>
							</td>
						</tr>

					</table>
					

					<div class="submitBtn" style="margin-top: 20px;position: absolute;right: 20px;">
						<span style=";margin: 0 8px 0 8px;font-size: 18pt;">總額：</span>
						<span style="font-size: 18pt;" id="sum"></span>
						<div onclick="sureToBuy()" class="sureToBuy btn btn-primary" style="margin:0 0 0 20px;">
							確定購買
						</div>
						<button id="payBtn" type="submit">送出訂單</button>
					</div>
				</form>
					
				@endif
				
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function(){

		var price = 0;
		$('.priceTag').each(function(){
			price =  price + parseInt($(this).html());
		});
		$('#sum').append(price);

		//------- 
		$('.quantity').change(function(){
			var slug = $(this).attr('id');
			var q = $(this).val();
			var pp = q * parseInt($(this).attr('price'));
			$('#priceTag' + slug).empty().append(pp);
			uploadSum();
		});

		$('#arriveYes').click(function(){
			$('#arriveDate').css('display','inline-block');
		});

		$('#arriveNo').click(function(){
			$('#arriveDate').css('display','none');
			$('#arriveDate').val(null);
		});

		$('.two-three').change(function(){
			if ($(this).val() == '3'){
				$('.ifThree').css('display','inline-block');
			}else if($(this).val()=='2'){
				$('.ifThree').css('display','none');
				$('.ship_three').val(null);
			}
		});

		$('.ship_county').change(function(){

			switch($(this).val()){
				case '基隆市':
					$('.ship_district').empty().append('<option value="仁愛區">仁愛區</option><option value="信義區">信義區</option><option value="中正區">中正區</option><option value="中山區">中山區</option><option value="安樂區">安樂區</option><option value="暖暖區">暖暖區</option><option value="七堵區">七堵區</option>');
					break;
				case '台北市':
					$('.ship_district').empty().append('<option value="中正區">中正區</option><option value="大同區">大同區</option><option value="中山區">中山區</option><option value="松山區">松山區</option><option value="大安區">大安區</option><option value="萬華區">萬華區</option><option value="信義區">信義區</option><option value="士林區">士林區</option><option value="北投區">北投區</option><option value="內湖區">內湖區</option><option value="南港區">南港區</option><option value="文山區">文山區</option>');
					break;
				case '新北市':
					$('.ship_district').empty().append('<option value="板橋區">板橋區</option><option value="新莊區">新莊區</option><option value="中和區">中和區</option><option value="永和區">永和區</option><option value="土城區">土城區</option><option value="樹林區">樹林區</option><option value="三峽區">三峽區</option><option value="鶯歌區">鶯歌區</option><option value="三重區">三重區</option><option value="蘆洲區">蘆洲區</option><option value="五股區">五股區</option><option value="泰山區">泰山區</option><option value="林口區">林口區</option><option value="八里區">八里區</option><option value="淡水區">淡水區</option><option value="三芝區">三芝區</option><option value="石門區">石門區</option><option value="金山區">金山區</option><option value="萬里區">萬里區</option><option value="汐止區">汐止區</option><option value="瑞芳區">瑞芳區</option><option value="貢寮區">貢寮區</option><option value="平溪區">平溪區</option><option value="雙溪區">雙溪區</option><option value="新店區">新店區</option><option value="深坑區">深坑區</option><option value="石碇區">石碇區</option><option value="坪林區">坪林區</option><option value="烏來區">烏來區</option>');
					break;
				case '桃園市':
					$('.ship_district').empty().append('<option value="桃園區">桃園區</option><option value="中壢區">中壢區</option><option value="平鎮區">平鎮區</option><option value="八德區">八德區</option><option value="楊梅區">楊梅區</option><option value="蘆竹區">蘆竹區</option><option value="大溪區">大溪區</option><option value="龍潭區">龍潭區</option><option value="龜山區">龜山區</option><option value="大園區">大園區</option><option value="觀音區">觀音區</option><option value="新屋區">新屋區</option><option value="復興區">復興區</option>');
					break;
				case '新竹市':
					$('.ship_district').empty().append('<option value="東區">東區</option><option value="北區">北區</option><option value="香山區">香山區</option>');
					break;
				case '新竹縣':
					$('.ship_district').empty().append('<option value="竹北市">竹北市</option><option value="竹東鎮">竹東鎮</option><option value="新埔鎮">新埔鎮</option><option value="關西鎮">關西鎮</option><option value="湖口鄉">湖口鄉</option><option value="新豐鄉">新豐鄉</option><option value="峨眉鄉">峨眉鄉</option><option value="寶山鄉">寶山鄉</option><option value="北埔鄉">北埔鄉</option><option value="芎林鄉">芎林鄉</option><option value="橫山鄉">橫山鄉</option><option value="尖石鄉">尖石鄉</option><option value="五峰鄉">五峰鄉</option>');
					break;
				case '苗栗縣':
					$('.ship_district').empty().append('<option value="苗栗市">苗栗市</option><option value="頭份市">頭份市</option><option value="竹南鎮">竹南鎮</option><option value="後龍鎮">後龍鎮</option><option value="通霄鎮">通霄鎮</option><option value="苑裡鎮">苑裡鎮</option><option value="卓蘭鎮">卓蘭鎮</option><option value="造橋鄉">造橋鄉</option><option value="西湖鄉">西湖鄉</option><option value="頭屋鄉">頭屋鄉</option><option value="公館鄉">公館鄉</option><option value="銅鑼鄉">銅鑼鄉</option><option value="三義鄉">三義鄉</option><option value="大湖鄉">大湖鄉</option><option value="獅潭鄉">獅潭鄉</option><option value="三灣鄉">三灣鄉</option><option value="南庄鄉">南庄鄉</option><option value="泰安鄉">泰安鄉</option>');
					break;
				case '台中市':
					$('.ship_district').empty().append('<option value="中區">中區</option><option value="東區">東區</option><option value="南區">南區</option><option value="西區">西區</option><option value="北區">北區</option><option value="北屯區">北屯區</option><option value="西屯區">西屯區</option><option value="南屯區">南屯區</option><option value="太平區">太平區</option><option value="大里區">大里區</option><option value="霧峰區">霧峰區</option><option value="烏日區">烏日區</option><option value="豐原區">豐原區</option><option value="后里區">后里區</option><option value="石岡區">石岡區</option><option value="東勢區">東勢區</option><option value="新社區">新社區</option><option value="潭子區">潭子區</option><option value="大雅區">大雅區</option><option value="神岡區">神岡區</option><option value="大肚區">大肚區</option><option value="沙鹿區">沙鹿區</option><option value="龍井區">龍井區</option><option value="梧棲區">梧棲區</option><option value="清水區">清水區</option><option value="大甲區">大甲區</option><option value="外埔區">外埔區</option><option value="大安區">大安區</option><option value="和平區">和平區</option>');
					break;
				case '彰化縣':
					$('.ship_district').empty().append('<option value="彰化市">彰化市</option><option value="員林市">員林市</option><option value="和美鎮">和美鎮</option><option value="鹿港鎮">鹿港鎮</option><option value="溪湖鎮">溪湖鎮</option><option value="二林鎮">二林鎮</option><option value="田中鎮">田中鎮</option><option value="北斗鎮">北斗鎮</option><option value="花壇鄉">花壇鄉</option><option value="芬園鄉">芬園鄉</option><option value="大村鄉">大村鄉</option><option value="永靖鄉">永靖鄉</option><option value="伸港鄉">伸港鄉</option><option value="線西鄉">線西鄉</option><option value="福興鄉">福興鄉</option><option value="秀水鄉">秀水鄉</option><option value="埔心鄉">埔心鄉</option><option value="埔鹽鄉">埔鹽鄉</option><option value="大城鄉">大城鄉</option><option value="芳苑鄉">芳苑鄉</option><option value="竹塘鄉">竹塘鄉</option><option value="社頭鄉">社頭鄉</option><option value="二水鄉">二水鄉</option><option value="田尾鄉">田尾鄉</option><option value="埤頭鄉">埤頭鄉</option><option value="溪州鄉">溪州鄉</option>');
					break;
				case '南投縣':
					$('.ship_district').empty().append('<option value="南投市">南投市</option><option value="埔里鎮">埔里鎮</option><option value="草屯鎮">草屯鎮</option><option value="竹山鎮">竹山鎮</option><option value="集集鎮">集集鎮</option><option value="名間鄉">名間鄉</option><option value="鹿谷鄉">鹿谷鄉</option><option value="中寮鄉">中寮鄉</option><option value="魚池鄉">魚池鄉</option><option value="國姓鄉">國姓鄉</option><option value="水里鄉">水里鄉</option><option value="信義鄉">信義鄉</option><option value="仁愛鄉">仁愛鄉</option>');
					break;
				case '雲林縣':
					$('.ship_district').empty().append('<option value="斗六市">斗六市</option><option value="斗南鎮">斗南鎮</option><option value="虎尾鎮">虎尾鎮</option><option value="西螺鎮">西螺鎮</option><option value="土庫鎮">土庫鎮</option><option value="北港鎮">北港鎮</option><option value="林內鄉">林內鄉</option><option value="古坑鄉">古坑鄉</option><option value="大埤鄉">大埤鄉</option><option value="莿桐鄉">莿桐鄉</option><option value="褒忠鄉">褒忠鄉</option><option value="二崙鄉">二崙鄉</option><option value="崙背鄉">崙背鄉</option><option value="麥寮鄉">麥寮鄉</option><option value="臺西鄉">臺西鄉</option><option value="東勢鄉">東勢鄉</option><option value="元長鄉">元長鄉</option><option value="四湖鄉">四湖鄉</option><option value="口湖鄉">口湖鄉</option><option value="水林鄉">水林鄉</option>');
					break;
				case '嘉義市':
					$('.ship_district').empty().append('<option value="東區">東區</option><option value="西區">西區</option>');
					break;
				case '嘉義縣':
					$('.ship_district').empty().append('<option value="太保市">太保市</option><option value="朴子市">朴子市</option><option value="布袋鎮">布袋鎮</option><option value="大林鎮">大林鎮</option><option value="民雄鄉">民雄鄉</option><option value="溪口鄉">溪口鄉</option><option value="新港鄉">新港鄉</option><option value="六腳鄉">六腳鄉</option><option value="東石鄉">東石鄉</option><option value="義竹鄉">義竹鄉</option><option value="鹿草鄉">鹿草鄉</option><option value="水上鄉">水上鄉</option><option value="中埔鄉">中埔鄉</option><option value="竹崎鄉">竹崎鄉</option><option value="梅山鄉">梅山鄉</option><option value="番路鄉">番路鄉</option><option value="大埔鄉">大埔鄉</option><option value="阿里山鄉">阿里山鄉</option>');
					break;
				case '台南市':
					$('.ship_district').empty().append('<option value="中西區">中西區</option><option value="東區">東區</option><option value="南區">南區</option><option value="北區">北區</option><option value="安平區">安平區</option><option value="安南區">安南區</option><option value="永康區">永康區</option><option value="歸仁區">歸仁區</option><option value="新化區">新化區</option><option value="左鎮區">左鎮區</option><option value="玉井區">玉井區</option><option value="楠西區">楠西區</option><option value="南化區">南化區</option><option value="仁德區">仁德區</option><option value="關廟區">關廟區</option><option value="龍崎區">龍崎區</option><option value="官田區">官田區</option><option value="麻豆區">麻豆區</option><option value="佳里區">佳里區</option><option value="西港區">西港區</option><option value="七股區">七股區</option><option value="將軍區">將軍區</option><option value="學甲區">學甲區</option><option value="北門區">北門區</option><option value="新營區">新營區</option><option value="後壁區">後壁區</option><option value="白河區">白河區</option><option value="東山區">東山區</option><option value="六甲區">六甲區</option><option value="下營區">下營區</option><option value="柳營區">柳營區</option><option value="鹽水區">鹽水區</option><option value="善化區">善化區</option><option value="大內區">大內區</option><option value="山上區">山上區</option><option value="新市區">新市區</option><option value="安定區">安定區</option>');
					break;
				case '高雄市':
					$('.ship_district').empty().append('<option value="楠梓區">楠梓區</option><option value="左營區">左營區</option><option value="鼓山區">鼓山區</option><option value="三民區">三民區</option><option value="鹽埕區">鹽埕區</option><option value="前金區">前金區</option><option value="新興區">新興區</option><option value="苓雅區">苓雅區</option><option value="前鎮區">前鎮區</option><option value="旗津區">旗津區</option><option value="小港區">小港區</option><option value="鳳山區">鳳山區</option><option value="大寮區">大寮區</option><option value="鳥松區">鳥松區</option><option value="林園區">林園區</option><option value="仁武區">仁武區</option><option value="大樹區">大樹區</option><option value="大社區">大社區</option><option value="岡山區">岡山區</option><option value="路竹區">路竹區</option><option value="橋頭區">橋頭區</option><option value="梓官區">梓官區</option><option value="彌陀區">彌陀區</option><option value="永安區">永安區</option><option value="燕巢區">燕巢區</option><option value="田寮區">田寮區</option><option value="阿蓮區">阿蓮區</option><option value="茄萣區">茄萣區</option><option value="湖內區">湖內區</option><option value="旗山區">旗山區</option><option value="美濃區">美濃區</option><option value="內門區">內門區</option><option value="杉林區">杉林區</option><option value="甲仙區">甲仙區</option><option value="六龜區">六龜區</option><option value="茂林區">茂林區</option><option value="桃源區">桃源區</option><option value="那瑪夏區">那瑪夏區</option>');
					break;
				case '屏東縣':
					$('.ship_district').empty().append('<option value="屏東市">屏東市</option><option value="潮州鎮">潮州鎮</option><option value="東港鎮">東港鎮</option><option value="恆春鎮">恆春鎮</option><option value="萬丹鄉">萬丹鄉</option><option value="長治鄉">長治鄉</option><option value="麟洛鄉">麟洛鄉</option><option value="九如鄉">九如鄉</option><option value="里港鄉">里港鄉</option><option value="鹽埔鄉">鹽埔鄉</option><option value="高樹鄉">高樹鄉</option><option value="萬巒鄉">萬巒鄉</option><option value="內埔鄉">內埔鄉</option><option value="竹田鄉">竹田鄉</option><option value="新埤鄉">新埤鄉</option><option value="枋寮鄉">枋寮鄉</option><option value="新園鄉">新園鄉</option><option value="崁頂鄉">崁頂鄉</option><option value="林邊鄉">林邊鄉</option><option value="南州鄉">南州鄉</option><option value="佳冬鄉">佳冬鄉</option><option value="琉球鄉">琉球鄉</option><option value="車城鄉">車城鄉</option><option value="滿州鄉">滿州鄉</option><option value="枋山鄉">枋山鄉</option><option value="霧臺鄉">霧臺鄉</option><option value="瑪家鄉">瑪家鄉</option><option value="泰武鄉">泰武鄉</option><option value="來義鄉">來義鄉</option><option value="春日鄉">春日鄉</option><option value="獅子鄉">獅子鄉</option><option value="牡丹鄉">牡丹鄉</option><option value="三地門鄉">三地門鄉</option>');
					break;
				case '台東縣':
					$('.ship_district').empty().append('<option value="臺東市">臺東市</option><option value="成功鎮">成功鎮</option><option value="關山鎮">關山鎮</option><option value="長濱鄉">長濱鄉</option><option value="池上鄉">池上鄉</option><option value="東河鄉">東河鄉</option><option value="鹿野鄉">鹿野鄉</option><option value="卑南鄉">卑南鄉</option><option value="大武鄉">大武鄉</option><option value="綠島鄉">綠島鄉</option><option value="太麻里鄉">太麻里鄉</option><option value="海端鄉">海端鄉</option><option value="延平鄉">延平鄉</option><option value="金峰鄉">金峰鄉</option><option value="達仁鄉">達仁鄉</option><option value="蘭嶼鄉">蘭嶼鄉</option>');
					break;
				case '花蓮縣':
					$('.ship_district').empty().append('<option value="花蓮市">花蓮市</option><option value="鳳林鎮">鳳林鎮</option><option value="玉里鎮">玉里鎮</option><option value="新城鄉">新城鄉</option><option value="吉安鄉">吉安鄉</option><option value="壽豐鄉">壽豐鄉</option><option value="光復鄉">光復鄉</option><option value="豐濱鄉">豐濱鄉</option><option value="瑞穗鄉">瑞穗鄉</option><option value="富里鄉">富里鄉</option><option value="秀林鄉">秀林鄉</option><option value="萬榮鄉">萬榮鄉</option><option value="卓溪鄉">卓溪鄉</option>');
					break;
				case '宜蘭縣':
					$('.ship_district').empty().append('<option value="宜蘭市">宜蘭市</option><option value="頭城鎮">頭城鎮</option><option value="羅東鎮">羅東鎮</option><option value="蘇澳鎮">蘇澳鎮</option><option value="礁溪鄉">礁溪鄉</option><option value="壯圍鄉">壯圍鄉</option><option value="員山鄉">員山鄉</option><option value="冬山鄉">冬山鄉</option><option value="五結鄉">五結鄉</option><option value="三星鄉">三星鄉</option><option value="大同鄉">大同鄉</option><option value="南澳鄉">南澳鄉</option>');
					break;
			}
		});
	});

	function deleteWithAjax(id){

		$.ajaxSetup({
  			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  			}
		});
		$.ajax({
			type:'POST',
			url:'kart/'+id,
			dataType:'json',
			data: {
				_method: 'delete',
			},
			success: function (response) {
                alert(response.msg);
                $('#item'+id).remove();
                // navbar cart 減一
                var inKart = parseInt($('#inKart').html()) - 1;
                $('#inKart').empty().append(inKart);

                uploadSum();
            },
            error: function () {
                alert('無法從購物車中刪除');
            }
		});
	}

	function uploadSum(){
		var price = 0;
			$('.priceTag').each(function(){
				price =  price + parseInt($(this).html());
			});
			$('#sum').empty().append(price);
			if (price == 0) {
				$('.sureToBuy').css('display','none');
			}else{
				$('.sureToBuy').css('display','inline-block');
			}
	}
	function sureToBuy(){
		$('.kartTable').css('display','none');
		$('.sureToBuy').css('display','none');
		$('.shipping').css('display','table');
		$('#payBtn').css('display','inline-block');
	}

	function arriveDate(){
		alert('hello');
	}

</script>
@endsection