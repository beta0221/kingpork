@extends('admin_main')

@section('title','| 訂單管理')

@section('stylesheets')
<style>
	.nav{
		width: 100%;
		height: 80px;
		border:1pt solid #000;
		padding: 12px 12px;
	}
	.input{
		height: 28px;
		width: auto;
		display: inline-block;
	}
	.shipmentBtn{
		cursor: pointer;
	}
	.table,.nav{
		font-size: 14px;
	}
	.table td,.table th{
		padding-left: 2px;
		padding-right: 2px;
	}
	#separater{
		margin-bottom: 4px;

	}
	.next-prev{
		display: inline-block;
		background-color: #0275d8;
		color: #fff;
		border-radius: 0.2rem;
		border: none;
		height: 20px;
		width: 20px;
		text-align: center;
		cursor: pointer;
	}
	.next-prev span{
		line-height: 20px;
	}
	.tool-box{
		position: absolute;
		right: 12px;
		top: 12px;
	}
	input.select-check-box{
	}
</style>
@endsection

@section('content')

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
      </div>
    </div>
  </div>
</div>
{{-- Modal --}}

		<div class="nav">
			<form id="searchForm" action="{{URL::current()}}" method="GET">
				
				<input style="width: 100px;" type="text" name="user_name" class="input form-control" placeholder="姓名" value="{{isset($_GET['user_name'])?$_GET['user_name']:''}}">
				-
				<input style="width: 150px;" type="text" name="ship_phone" class="input form-control" placeholder="電話" value="{{isset($_GET['ship_phone'])?$_GET['ship_phone']:''}}">
				-
				<input style="width: 150px;" id="bill_id" name="bill_id" type="text" class="input form-control" placeholder="訂單編號" value="{{isset($_GET['bill_id'])?$_GET['bill_id']:''}}">
				-
				<input id="date1" name="date1" type="date" class="input form-control" value="{{isset($_GET['date1'])?$_GET['date1']:''}}">~
				<input id="date2" name="date2" type="date" class="input form-control" value="{{isset($_GET['date2'])?$_GET['date2']:''}}">
				-
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
				<p id="separater"> </p>
				<input name="pay_by_ATM" type="checkbox" value="ATM" {{isset($_GET['pay_by_ATM'])?'checked':''}}>ATM
				<span>/</span>
				<input name="pay_by_cod" type="checkbox" value="貨到付款" {{isset($_GET['pay_by_cod'])?'checked':''}}>貨到付款
				<span>/</span>
				<input name="pay_by_credit" type="checkbox" value="CREDIT" {{isset($_GET['pay_by_credit'])?'checked':''}}>信用卡
				-
				<input name="shipment_0" type="checkbox" value="0" {{isset($_GET['shipment_0'])?'checked':''}}>
				<span style="color:#d9534f;">可準備</span>
				<span>/</span>
				<input name="shipment_1" type="checkbox" value="1" {{isset($_GET['shipment_1'])?'checked':''}}>
				<span style="color: #eb9316;">準備中</span>
				<span>/</span>
				<input name="shipment_2" type="checkbox" value="2"{{isset($_GET['shipment_2'])?'checked':''}}>
				<span style="color: #5cb85c;">已出貨</span>
				<span>/</span>
				<input name="shipment_3" type="checkbox" value="3"{{isset($_GET['shipment_3'])?'checked':''}}>
				<span style="color: green;">已結案</span>

				
				
				<span> - 每頁比數:</span>
				<select name="data_take" id="data_taker">
					<option value="20">20</option>
					<option value="50">50</option>
					<option value="100">100</option>
				</select>
				
				<span>-</span>
				<div onclick="prevPage();" id="prev" class="next-prev"><span><</span></div>
				<span>第</span>
				<select name="page" id="pageSelecter">
					@for($i = 1;$i <= $page_amount;$i ++)
					<option value="{{$i}}">{{$i}}</option>
					@endfor
				</select>
				<span>頁</span>
				<div onclick="nextPage();" id="next" class="next-prev"><span>></span></div>
				<span>　　-</span>
				<button id="search-btn" class="btn btn-sm btn-primary" type="submit">搜尋</button>
				

			</form>
			

			<div class="tool-box">
				<button style="background-color: #000;color: #fff" onclick="selectAll();" class="btn btn-sm">全選</button>
				<button style="background-color: #000;color: #fff" onclick="selectPush();" class="btn btn-sm">下階段</button>
			</div>
		</div>


		<div style="width: 100%;">
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>時間</th>
					<th>收貨人</th>
					<th>編號</th>
					{{-- <th>訂購人</th> --}}
					<th>商品</th>

					<th>到貨日</th>
					<th>時間</th>
					<th>發票</th>

					<th>總價</th>
					
					{{-- <th>性別</th> --}}
					{{-- <th>電話</th> --}}
					{{-- <th>地址</th> --}}
					<th>付款方式</th>
					<th>付款狀態</th>
					
					<th>備註</th>
					<th>出貨</th>
					<th>-</th>
				</tr>
			</thead>
			<tbody>
				
				<?php $i=1 ?>
				@foreach($orders as $order)

				<tr>
					<td>{{$i++}}</td>
					<td>{{$order['created_at']}}</td>
					
					<td>
						@if($order['ship_gender']==1)
							<font color="green">{{$order['ship_name']}}</font>
						@else
							<font color="purple">{{$order['ship_name']}}</font>
						@endif
					</td>

					<td><a href="{{url('order/showAll').'/'.$order['bill_id']}}" target="_blank">{{$order['bill_id']}}</a></td>

					<td>
						@foreach($order['item'] as $item)
						{{$item['name']}}*{{$item['quantity']}}<br>
						@endforeach
					</td>

					<td>
						@if($order['ship_arriveDate'] == null)-@else{{$order['ship_arriveDate']}}@endif
					</td>

					<td>
						@if($order['ship_time'] == 'no')-@else{{$order['ship_time']}}@endif
					</td>

					<td>
						@if($order['ship_receipt'] == '2')二連@else 3連@endif
					</td>

					<td>{{$order['price']}}</td>

					<td>{{$order['pay_by']}}</td>

					<td>{{$order['status']}}</td>
					


					<td onclick="showMemo('{{$order['bill_id']}}');" data-toggle="modal" data-target="#exampleModal">
						@if($order['ship_memo'] != null)
							<font style="cursor: pointer;background-color: red;" color="yellow">！</font>
						@endif
					</td>


					<td>
						@if(($order['pay_by'] == '貨到付款' AND $order['shipment'] == 0) OR ($order['status'] == 1 AND $order['shipment'] == 0))
						<button class="btn btn-sm btn-danger shipmentBtn" id="{{$order['bill_id']}}" onclick="shipment('{{$order['bill_id']}}');">可準備</button>

						@elseif(($order['pay_by'] == '貨到付款' AND $order['shipment'] == 1) OR ($order['status'] == 1 AND $order['shipment'] == 1))
						<button class="btn btn-sm btn-warning shipmentBtn" id="{{$order['bill_id']}}" onclick="shipment('{{$order['bill_id']}}');">準備中</button>

						@elseif($order['shipment']==2)
						<button class="btn btn-sm btn-success shipmentBtn" id="{{$order['bill_id']}}" onclick="shipment('{{$order['bill_id']}}');">已出貨</button>

						@elseif($order['shipment']==3)
							<font class="btn-sm" style="background-color: green;" color="#fff">＊結案＊</font>

						@else
						-
						@endif
					</td>

					<td>
						<input type="checkbox" class="select-check-box" value="{{$order['bill_id']}}">
					</td>


				</tr>
				@endforeach
				
			</tbody>
		</table>
		</div>

@endsection

@section('scripts')
{{ Html::script('js/bootstrap/bootstrap.min.js') }}
	<script>
	$(document).ready(function(){

		$.ajaxSetup({
	  		headers: {
	    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
	  		}
		});

		@if (isset($_GET['ship_county']))
		$('#select_county').val('{{$_GET['ship_county']}}');
		@endif

		$('#date1').change(function(){
			$('#date2').val($('#date1').val());
		});

		@if (isset($_GET['data_take']))
		$('#data_taker').val('{{$_GET['data_take']}}');
		@endif

		@if (isset($_GET['page']))
		$('#pageSelecter').val('{{$_GET['page']}}');
		@endif

		if (parseInt($('#pageSelecter').val()) == {{$page_amount}}) {
			$('#next').css('display','none');
		}
		if (parseInt($('#pageSelecter').val()) == 1) {
			$('#prev').css('display','none');
		}

		$('#data_taker').change(function(){
			$('#pageSelecter').val('1');
			$('#searchForm').submit();
		});

		$('#pageSelecter').change(function(){
			$('#searchForm').submit();
		});
	});

	function shipment($id){
		
		$.ajax({
			type:'POST',
			url:'order/'+$id,
			dataType:'json',
			data: {
				_method: 'PUT',
			},
			success: function (response) {
				if (response == 1) {
					$('#'+$id).removeClass('btn-danger').addClass('btn-warning');
					$('#'+$id).html('準備中');
				}else if(response == 2){
					$('#'+$id).removeClass('btn-warning').addClass('btn-success');
					$('#'+$id).html('已出貨');
				}else if(response == 0){
					$('#'+$id).removeClass('btn-success').addClass('btn-danger');
					$('#'+$id).html('可準備');
				}

			},
			error: function () {
	            alert('錯誤');

	        },
		});
	};

	function showMemo($id){
		$('.modal-body').empty();
		$('.modal-title').empty();
		
		$.ajax({
			type:'GET',
			url:'order/'+$id,
			dataType:'json',
			success: function (response) {
				$('.modal-body').append(response);
				$('.modal-title').append($id);
			},
			error: function () {
	            alert('錯誤');
	        },
		});
	};

	function nextPage(){
		var i = parseInt($('#pageSelecter').val());		
		i = i + 1;
		$('#pageSelecter').val(i);
		$('#searchForm').submit();
	}

	function prevPage(){
		var i = parseInt($('#pageSelecter').val());		
		i = i - 1;
		$('#pageSelecter').val(i);
		$('#searchForm').submit();
	}

	var sel = 0;
	function selectAll(){

		if (sel == 0) {
			$('.select-check-box').prop("checked",true);
			sel = 1;	
		}else{
			$('.select-check-box').prop("checked",false);
			sel = 0;
		}
		
	}

	function selectPush(){

		var selected = [];
		var i = 0;
		$('.select-check-box:checked').each(function(){
			selected[i] = $(this).val();
			i++;
		});

		if (selected.length > 0) {


			confirm("確定送出");

			$.ajax({
				type:'POST',
				url:'order/1',
				dataType:'json',
				data: {
					_method: 'PUT',
					selectArray:selected,
				},
				success: function (response) {
					location.reload();
				},
				error: function () {
		            alert('錯誤');
		        },
			});
		}else{
			alert('請選取訂單');
		}

	}

	</script>
@endsection
