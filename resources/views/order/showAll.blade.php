<html>
	<head>
		<style>
			table{
				width: 100%;
				border-collapse: collapse;
			}
			td{
				padding-left: 4px;
			}
			.bill-table td{
				/*border:1pt solid #000;*/
				border: 1pt solid #000;
				word-break: break-all;
			}
			.item-table{
				/*border:none;*/
				width: calc(100% + 4px);
				margin-left: -4px;
			}
			.item-col-td{
				width: 25%;
			}
			.outter{
				position: absolute;
				left: 50%;
				transform: translateX(-50%);
				width: 50%;
			}
			.bill-table{

				border: 1pt solid #000;
				margin: 20px 0;
			}
			.head{
				background-color: rgba(0,0,0,0.3);
			}
			.head-title{
				background-color: steelblue;
				color: #fff;
			}
			.head-status{
				text-align: center;
				padding: 10px 0;
				color: #fff;
				@if(($bill->pay_by == '貨到付款' AND $bill->shipment == 0) OR ($bill->status == 1 AND $bill->shipment == 0))
				background-color: #d9534f;
				@elseif($bill->status == 1 AND $bill->shipment == 1)
				background-color: #eb9316;
				@elseif($bill->shipment==2)
				background-color: #5cb85c;
				@elseif($bill->shipment==3)
				background-color: green;
				@else
				background-color: #000;
				@endif
			}
		</style>
	</head>
	<body>
		
		<div class="outter">
			<h2>No. <font color="steelblue">{{$bill->bill_id}}</font></h2>
			<hr>
			@if($user)
			<table class="bill-table">
				<tr>
					<td class="head">姓名</td>
					<td class="head">信箱</td>
					<td class="head">電話</td>
					<td class="head">累計紅利</td>
				</tr>
				<tr>
					<td>
						<a href="/order/history/{{$user->id}}" target="_blank">{{$user->name}}</a>
					</td>
					<td>{{$user->email}}</td>
					<td>{{$user->phone}}</td>
					<td>{{$user->bonus}}</td>
				</tr>
			</table>
			@endif



			<table class="bill-table">
				<tr>
					<td>
						<table class="item-table">
							<tr>
								<td class="head">產品</td>
								<td class="head">優惠</td>
								<td class="head">價格</td>
								<td class="head">數量</td>
							</tr>
						</table>
					</td>
					<td class="head">紅利折扣</td>
					<td class="head">總金額</td>
					<td class="head">回饋紅利</td>
				</tr>
				<tr>
					<td>
						<table class="item-table">
							@foreach($items as $item)
							<tr>
								<td class="item-col-td">{{$item->name}}</td>
								<td class="item-col-td">{{$item->description}}{{$item->discription}}</td>	
								<td class="item-col-td">{{$item->price}}</td>
								<td class="item-col-td">{{$item->quantity}}</td>
							</tr>
							@endforeach
						</table>
					</td>
					<td>{{$bill->bonus_use}}</td>
					<td>{{$bill->price}}</td>
					<td>{{$bill->get_bonus}}</td>
				</tr>
			</table>



			<table class="bill-table">
				<tr>
					<td class="head-status" colspan="4">
						@if(($bill->pay_by == '貨到付款' AND $bill->shipment == 0) OR ($bill->status == 1 AND $bill->shipment == 0))
						可準備
						@elseif($bill->status == 1 AND $bill->shipment == 1)
						準備中
						@elseif($bill->shipment==2)
						已出貨
						@elseif($bill->shipment==3)
						＊結案＊
						@else
						未成立
						@endif	
					</td>
				</tr>
				<tr>
					<td class="head head-title" colspan="4">收貨人</td>
				</tr>
				<tr>
					<td class="head">姓名</td>
					<td>{{$bill->ship_name}}</td>
					<td class="head">性別</td>
					<td>{{$bill->ship_gender}}</td>
				</tr>
				<tr>
					<td class="head">電話</td>
					<td>{{$bill->ship_phone}}</td>
					<td class="head">信箱</td>
					<td>{{$bill->ship_email}}</td>
				</tr>
				<tr>
					<td class="head">物流方式</td>
					<td colspan="3">{{$carrierDict[$bill->carrier_id]}}</td>
				</tr>
				@if (!empty($storeInfo))
                <tr>
					<td class="head">門市</td>
					<td colspan="3">門市代碼:{{$storeInfo->number}};門市名稱:{{$storeInfo->name}};門市地址:{{$storeInfo->address}};</td>
                </tr>
                @endif

				@if ($bill->carrier_id == 0)
				<tr>
					<td class="head">寄送地址</td>
					<td colspan="3">
						@if ($bill->ship_time == '*')
                        
                            <?php $addresses = json_decode($bill->ship_address, true); ?>
                            @foreach ($addresses as $a)
                            姓名:{{$a['name']}} 地址:{{$a['address']}} 電話:{{$a['phone']}} 數量:{{$a['quantity']}}<br>
                            @endforeach
						@else
							{{$bill->ship_county}}{{$bill->ship_district}}{{$bill->ship_address}}
						@endif
					</td>
				</tr>	
				@endif
				
				<tr>
					<td class="head head-title" colspan="4">付款資訊</td>
				</tr>
				<tr>
					<td class="head">繳費方式</td>
					<td>
						@if ($bill->pay_by == "FAMILY")
                        超商付款
                        @else
                        {{$bill->pay_by}}
                        @endif

						@if ($bill->pay_by == 'CREDIT')
						-授權碼:{{$bill->auth_code}}{{(isset($cardInfo->AuthCode))?$cardInfo->AuthCode:''}}	
						@endif
						
					</td>
					<td class="head">繳費狀態</td>
					<td>
						@if($bill->status==1)
							<font color="green">已繳費</font>
						@else
							<font color="red">未繳費</font>
						@endif
					</td>
				</tr>
				<tr>
					<td class="head">出貨時間</td>
					<td>{{$bill->ship_arriveDate}}{{$bill->ship_time}}</td>
					<td class="head">發票</td>
					<td>
						@if($bill->ship_receipt == 2)
						二聯：{{$bill->ship_name}}
						@else
						三聯：{{$bill->ship_three_id}}/{{$bill->ship_three_company}}
						@endif
						
					</td>
				</tr>
				<tr>
					<td class="head">備註</td>
					<td colspan="3">{{$bill->ship_memo}}</td>
				</tr>
				
			</table>

			@if ($bill->shipment != 3)
				
			<form action="/order/void/{{$bill->bill_id}}" method="POST">
				{{ csrf_field() }}
				<button style="background:red;color:#fff;margin-top:80px">作廢</button>
			</form>
			@endif
			
		</div>

		
		
	</body>
</html>