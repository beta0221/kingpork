<h2>
	{{$user_name}}
	@if($ship_gender == 1)
	先生
	@else
	小姐
	@endif
	您好，非常感謝您的購買。
</h2>
<h3>以下為您的購買明細：</h3>
<br>


<h3>訂單編號：{{$bill_id}}</h3>
<h4>訂單成立時間：{{$TradeDate}}</h4>



<div style="border:1pt solid rgba(0,0,0,0.3);border-radius: 0.3em;">
	<table style="width: 100%;">
	<tr>
		<td style="width: 70%;border-bottom: 1pt solid gray;">
			<table style="width: 100%;">
				<tr>
					<td style="width: 60%;">產品</td>
					<td style="width: 20%;">價格</td>
					<td style="width: 20%;">數量</td>
				</tr>
			</table>
		</td>
		<td style="width: 15%;border-bottom: 1pt solid gray;">紅利折扣</td>
		<td style="width: 15%;border-bottom: 1pt solid gray;">總金額</td>
	</tr>
	<tr>
		<td style="width: 70%;">
			<table style="width: 100%;">
				@foreach($items as $item)
				<tr>
					<td style="width: 60%;">{{$item['name']}}</td>
					<td style="width: 20%;">{{$item['price']}}</td>
					<td style="width: 20%;">{{$item['quantity']}}</td>
				</tr>
				@endforeach
			</table>
		</td>
		<td style="width: 15%;">{{$bonus_use}}</td>
		<td style="width: 15%;">{{$price}}</td>
	</tr>
	</table>
</div>



<h4>收件人　：{{$ship_name}}</h4>
<h4>聯絡電話：{{$ship_phone}}</h4>
<h4>寄送地址：{{$ship_county}}-{{$ship_district}}-{{$ship_address}}</h4>
<h4>付款方式：{{$pay_by}}</h4>
-
<p>若您對此次交易有任何問題，請隨時寫信給我們。</p>
