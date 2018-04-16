@extends('main')

@section('title','| 我的訂單')

@section('stylesheets')
<style>
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
.TDdate{
	width: 140px;
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
}
th{
	padding-top: 12px;
	padding-bottom: 12px;
}
td,th{
	text-align: center;
	vertical-align: middle;
}
</style>
@endsection



@section('content')





<div class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-md-10 offset-md-1 outter">
				
				@if(Session::has('success'))
					{{Session::get('success')}}<br>
				@endif

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
						<th>總價</th>
						<th>付款方式</th>
						<th>付款狀態</th>
						<th>-</th>
					</tr>

					@foreach(array_reverse($finalBills) as $billX)

						<tr>

							<td class="TDdate">{{$billX[0]['created_at']}}</td>
							<td>{{$billX[0]['bill_id']}}</td>
							<td class="TDproduct">
								<table style="width: 100%;">
									@foreach($billX as $billY)
										
										<tr>
											<td class="TNT1">{{$billY['name']}}</td>
											<td class="TNT2">{{$billY['price']}}</td>
											<td class="TNT3">{{$billY['quantity']}}</td>
										</tr>

									@endforeach
								</table>
							</td>
							<td class="TDtotal">{{$billX[0]['total']}}</td>
							<td>
								
								@if($billX[0]['pay_by'] == '0')
									-
								@else
									{{$billX[0]['pay_by']}}
								@endif

								
							</td>
							<td>
								@if($billX[0]['status'] == 1)
									已付款
								@else
									未付款
								@endif
							</td>
							<td>
								@if($billX[0]['status'] == 1 OR $billX[0]['pay_by'] == '貨到付款')
									-
								@else
									<a href="{{route('bill.show', $billX[0]['bill_id'])}}">付款</a>
								@endif

							</td>
						</tr>
						<tr>
							<td>-</td>
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