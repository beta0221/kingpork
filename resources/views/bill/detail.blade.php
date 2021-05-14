@extends('main')

@section('title','| 訂單詳情')



@section('stylesheets')
{{Html::style('css/_payBill.css')}}
{{Html::style('css/_process.css')}}
@endsection

@section('content')


<div class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 offset-lg-2 col-12 outter">

				
				<div class="mt-4">
                    <h3>訂單編號：{{$bill->bill_id}}</h3>
                </div>

                <div class="mt-2">
                    <h5>
                        狀態：
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
                    </h5>
                </div>

                @if (!empty($atmInfo))
                @include('bill.atmInfo',['atmInfo'=>$atmInfo])    
                @endif
                


                <div>
                    <h5>收貨人：</h5>
                    <ul>
                        <li>姓名：{{$bill->ship_name}}</li>
                        {{-- <li>性別：{{$bill->ship_gender}}</li> --}}
                        <li>電話：{{$bill->ship_phone}}</li>
                        <li>信箱：{{$bill->ship_email}}</li>
                        <li>寄送地址：{{$bill->ship_county}}{{$bill->ship_district}}{{$bill->ship_address}}</li>
                    </ul>
                </div>

                <div>
                    <h5>付款資訊：</h5>
                    <ul>
                        <li>繳費方式：{{$bill->pay_by}}</li>
                        <li>
                            繳費狀態：
                            @if($bill->status==1)
                                    <font color="green">已繳費</font>
                            @else
                                    <font color="red">未繳費</font>
                            @endif
                        </li>
                        <li>指定到貨時間：{{$bill->ship_arriveDate}}{{$bill->ship_time}}</li>
                        <li>
                            發票：
                            @if($bill->ship_receipt == 2)
                            二聯：{{$bill->ship_name}}
                            @else
                            三聯：{{$bill->ship_three_id}}/{{$bill->ship_three_company}}
                            @endif
                        </li>
                        <li>備註：{{$bill->ship_memo}}</li>
                    </ul>
                </div>

                
                

				<div class="billTable">
					<table style="width: 100%">	

						<tr class="product-title-TR">
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
							<th>總金額</th>
						</tr>
						<tr>
							<td class="TDproduct">
								<table style="width: 100%;">
									
									@foreach($products as $product)
										<tr class="product-TR">
											<td class="TNT1">{{$product->name}}</td>
											<td class="TNT2">{{$product->price}}</td>
											<td class="TNT3">{{$product->quantity}}</td>
										</tr>
									@endforeach
									
								</table>
							</td>
							<td>{{$bill->bonus_use}}</td>
							<td class="TDtotal">{{$bill->price}}</td>
						</tr>
					</table>
				</div>


                

                <div class="payBy">
                    <div class="inner-payBy">
                        <a href="/bill" style="color: white;" class="payByBtn btn btn-success">我的訂單</a>
                    </div>
                </div>

			</div>
		</div>
	</div>

</div>
@endsection



@section('scripts')


@endsection
