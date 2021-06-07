@extends('main')

@section('title','| 我的訂單')

@section('stylesheets')
{{Html::style('css/_bill.css')}}
<style>
	ul.pagination a,ul.pagination span{
		position: relative;
		float: left;
		padding: 6px 12px;
		margin-left: -1px;
		line-height: 1.42857143;
		text-decoration: none;
		background-color: #fff;
		border: 1px solid #ddd;
	}
	ul.pagination a{
		color: #337ab7;
	}
	ul.pagination span{
		color: gray;
	}
	ul.pagination li.active span{
		z-index: 3;
		color: #fff;
		cursor: default;
		background-color: #337ab7;
		border-color: #337ab7;
	}
</style>
@endsection


@section('content')

<div class="the-wrapper">
	<h1 style="text-align: center;" id="contactUs">我的訂單</h1>
	<div class="container">
		<div class="row">
			
			<div class="col-12 outter">
				
				
				
				<table style="width: 100%">	

					<tr>
						<th>日期</th>
						<th>訂單編號</th>
						<th>紅利折扣</th>
						<th>總價</th>
						<th>付款方式</th>
						<th>付款狀態</th>
						<th>出貨狀態</th>
						<th>-</th>
					</tr>

					@if($bills)

						@foreach($bills as $bill)

							<tr class="bill-tr">

								<td class="TDdate">{{$bill->created_at}}</td>
								<td class="TDbill_id">
									<a href="{{route('billDetail',['bill_id'=>$bill->bill_id])}}">{{$bill->bill_id}}</a>
									
								</td>
								
								<td><font color="red">{{$bill->bonus_use}}</font></td>
								<td class="TDtotal"><font color="#0275d8">{{$bill->price}}</font></td>
								<td>
									
									@if($bill->pay_by == 'CREDIT')
										信用卡
									@elseif($bill->pay_by == 'ATM')
										ATM轉帳
									@elseif($bill->pay_by == 'FAMILY')
										超商付款
									@else
										{{$bill->pay_by}}
									@endif

									
								</td>
								<td>
									@if($bill->status == 1)
										<font color="#5cb85c">已付款</font>
									@elseif($bill->pay_by == '貨到付款')
										<font color="gray">-</font>
									@else
										<font color="gray">未付款</font>
									@endif
								</td>
								<td>
									@if($bill->shipment==0)
										<font color="gray">-</font>
									@elseif($bill->shipment==1)
										<font color="#eb9316">準備中</font>
									@elseif($bill->shipment==2)
										<font color="#5cb85c">已出貨</font>
									@elseif($bill->shipment==3)
										<font color="gray">已取消</font>
									@endif
									
								</td>
								<td>
									@if($bill->status != 1 AND $bill['shipment']==0)
										<font style="cursor: pointer;" color="gray" onclick="cancelBill({{$bill->bill_id}})">取消訂單</font>
									@else
										<font color="gray">-</font>
									@endif
								</td>
							</tr>
							

						@endforeach
					@else
					<tr>
						<td colspan="11">
							<h3 class="mt-4 mb-4">無訂單</h3>
						</td>
					</tr>
						
					@endif

				
				</table>
				<div style="margin-top:12px;">
					{{$bills->links()}}
				</div>
			</div>
		</div>
	</div>
</div>



@endsection


@section('scripts')
<script>

	function cancelBill(id){
		var r = confirm('是否確定取消訂單？');
		if (r==true) {
			$.ajax({
				type:'POST',
				url:'/bill/cancel/'+id,
				dataType:'json',
				data: {
					_method: 'delete',
				},
				success: function (response) {
					window.location.href = '/bill';
				},
				error: function () {
			        alert('錯誤');
			    },
			});
		}
	}


</script>
@endsection