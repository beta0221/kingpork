<html>
	<head>
		<title>管理後台｜訂單管理</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		{{Html::style('css/reset.css')}}
		{{Html::style('css/bootstrap/bootstrap.min.css')}}
	<style>
	.nav{
		width: 150%;
		height: 56px;
		border:1pt solid #000;
	}
	.input{
		height: 28px;
		width: auto;
		display: inline-block;
	}
	</style>
	</head>
	<body>
		<div class="nav">
			<form id="searchForm" action="{{route('order.search')}}" method="POST">
				{{csrf_field()}}
				<input id="bill_id" name="bill_id" type="text" class="input form-control" placeholder="訂單編號" value="{{Session::get('bill_id')}}">
				
				<input id="date1" name="date1" type="date" class="input form-control" value="{{Session::get('date1')}}">~
				<input id="date2" name="date2" type="date" class="input form-control" value="{{Session::get('date2')}}">
				
				<select id="select_county" name="ship_county">
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
				ATM:<input name="pay_by_ATM" type="checkbox" value="ATM" @if(Session::has('pay_by_ATM')) checked @endif>
				貨到付款:<input name="pay_by_cod" type="checkbox" value="貨到付款" @if(Session::has('pay_by_cod')) checked @endif>
				

				<button style="display: inline-block;" type="submit">搜尋</button>

			</form>

			
		</div>
		<div style="width: 150%;">
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>訂單時間</th>
					<th>訂單編號</th>
					<th>訂購人</th>
					<th>訂購商品</th>
					<th>總價</th>
					<th>收貨人</th>
					<th>性別</th>
					<th>電話</th>
					<th>收貨地址</th>
					<th>付款方式</th>
					<th>付款狀態</th>
					<th>指定到貨日</th>
					<th>時間</th>
					<th>發票</th>
					<th>備註</th>
				</tr>
			</thead>
			<tbody>
				
				<?php $i=1 ?>
				@foreach(array_reverse($orders) as $order)

				<tr>
					<td>{{$i++}}</td>
					<td>{{$order['created_at']}}</td>
					<td>{{$order['bill_id']}}</td>
					<td>{{$order['user_name']}}</td>
					<td>
						@foreach($order['item'] as $item)
						{{$item['name']}}*{{$item['quantity']}}<br>
						@endforeach
					</td>
					<td>{{$order['price']}}</td>
					<td>{{$order['ship_name']}}</td>
					<td>
						@if($order['ship_gender']==1)先生@else 小姐@endif
					</td>
					<td>{{$order['ship_phone']}}</td>
					<td>{{$order['ship_county']}}-{{$order['ship_district']}}-{{$order['ship_address']}}</td>
					<td>{{$order['pay_by']}}</td>
					<td>{{$order['status']}}</td>
					<td>
						@if($order['ship_arriveDate'] == null)無@else{{$order['ship_arriveDate']}}@endif
					</td>
					<td>
						@if($order['ship_time'] == 'no')無@else{{$order['ship_time']}}@endif
					</td>
					<td>
						@if($order['ship_receipt'] == '2')二連@else{{$order['ship_three_name']}}{{$order['ship_three_id']}}{{$order['ship_three_company']}}@endif
					</td>
					<td>{{$order['ship_memo']}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		</div>

	</body>
	{{ Html::script('js/jquery/jquery-3.2.1.min.js') }}
	<script>
	$(document).ready(function(){

		@if (Session::has('ship_county'))
		$('#select_county').val('{{Session::get('ship_county')}}');
		@endif

		$('#date1').change(function(){
			$('#date2').val($('#date1').val());
		});

		$('#bill_id').keypress(function(e){
			if (e.which == 13) {
				$('#searchForm').submit();
			}
		});
	});
	</script>
</html>