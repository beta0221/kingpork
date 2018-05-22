@extends('main')

@section('title','| 我的訂單')

@section('stylesheets')
<style>
.wrapper h1{
	margin-top: 60px;
	margin-bottom: 20px;
}
.outter{
	/*margin-top: 60px;*/
	margin-bottom: 60px;
	min-height: 520px;
	/*overflow-y: scroll;*/
	padding-bottom: 40px;
	background-color: rgba(255,255,255,0.5);
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.3);
	border-radius: 0.3em;
}
.TDtotal{
	width: 56px;
}
.TDproduct{
	width: calc(100% - 276px);
}
.TNT2,.TNT3{
	width: 56px;
}
.TNT1{
	width: calc(100% - 112px);
	text-align: left;
	padding-left: 16px;
}
th{
	padding-top: 12px;
	padding-bottom: 12px;
}
td,th{
	text-align: center;
	vertical-align: middle;
}
.bill-tr{
	border-top: 1px solid rgba(0,0,0,0.1)
}
.item-tr{
	height: 48px;
}
</style>
@endsection



@section('content')





<div class="wrapper">
	<h1 style="text-align: center;" id="contactUs">我的訂單</h1>
	<div class="container">
		<div class="row">
			
			<div class="col-md-12 outter">
				
				
				
				<table style="width: 100%">	

					<tr>
						<th>日期</th>
						<th>訂單編號</th>
						<th>
							<table style="width: 100%;">
								<tr>
									<td class="TNT1">產品</td>
									<td class="TNT2">價格</td>
									<td class="TNT3">數量</td>
								</tr>
							</table>
						</th>
						<th>紅利折扣</th>
						<th>總價</th>
						<th>付款方式</th>
						<th>付款狀態</th>
						<th>出貨狀態</th>
						<th>-</th>
					</tr>

					@foreach(array_reverse($finalBills) as $billX)

						<tr class="bill-tr">

							<td class="TDdate">{{$billX[0]['created_at']}}</td>
							<td>{{$billX[0]['bill_id']}}</td>
							<td class="TDproduct">
								<table style="width: 100%;">
									@foreach($billX as $billY)
										
										<tr class="item-tr">
											<td class="TNT1">{{$billY['name']}}</td>
											<td class="TNT2">{{$billY['price']}}</td>
											<td class="TNT3">{{$billY['quantity']}}</td>
										</tr>

									@endforeach
								</table>
							</td>
							<td><font color="red">{{$billX[0]['bonus_use']}}</font></td>
							<td class="TDtotal"><font color="#0275d8">{{$billX[0]['total']}}</font></td>
							<td>
								
								@if($billX[0]['pay_by'] == 'CREDIT')
									信用卡
								@elseif($billX[0]['pay_by'] == 'ATM')
									ATM轉帳
								@else
									{{$billX[0]['pay_by']}}
								@endif

								
							</td>
							<td>
								@if($billX[0]['status'] == 1)
									<font color="#5cb85c">已付款</font>
								@elseif($billX[0]['pay_by'] == '貨到付款')
									<font color="gray">-</font>
								@else
									<font color="gray">未付款</font>
								@endif
							</td>
							<td>
								@if($billX[0]['shipment']=='已出貨')
									<font color="#5cb85c">{{$billX[0]['shipment']}}</font>
								@else
									<font color="gray">{{$billX[0]['shipment']}}</font>
								@endif
								
							</td>
							<td>
								@if($billX[0]['status'] == 1 OR $billX[0]['status'] == 's' OR $billX[0]['pay_by'] == '貨到付款')
									<font color="gray">-</font>
								@else
									<a href="{{route('bill.show', $billX[0]['bill_id'])}}">付款</a>
								@endif
								
							</td>
						</tr>
						

					@endforeach
				

				</table>
			
			</div>
		</div>
	</div>
</div>



@endsection


@section('scripts')

@endsection