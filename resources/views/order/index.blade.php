@extends('admin_main')

@section('title','| 訂單管理')

@section('stylesheets')
<style>
	.nav{
		width: 100%;
		height: 80px;
		background-color: rgba(0,0,0,0.2);
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
		text-align: center;
		border:1pt solid gray;
		padding: 6px 4px;
	}
	.table-tr:hover{
		background-color: rgba(0,0,0,0.2);
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
	.th-green,.th-red,.th-yellow{
		color: #fff;
	}
	.th-green{
		background-color: green;
	}
	.th-red{
		background-color: #d9534f;
	}
	.th-yellow{
		background-color: #ec971f;
	}
	.table img{
		width: 26px;
	}
	.fromSingle{
		background-color: #0275d8;
		color: #fff;
	}
	.fromKol {
		background-color: #36c28c;
		color: #fff;
	}

	.dropdown {
		position: relative;
		display: inline-block;
	}

	.dropdown-content {
		display: none;
		position: absolute;
		background-color: lightgray;
		box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
		z-index: 1;
		right: 0;
	}

	.dropdown-content button {
		color: #fff;
		padding: 4px 8px;
		margin: 8px;
		text-decoration: none;
		display: block;
		cursor: pointer;
	}

	.dropdown-content button:hover {background-color: #ddd;}

	.dropdown:hover .dropdown-content {display: block;}
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

<div class="modal fade" id="markingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">註記</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<span>訂單編號：</span><span id="markingID"></span>
        <textarea id="markingTextarea" style="height: 200px;width: 100%;border-radius: 5px;" placeholder="註記..."></textarea>
      </div>
      <div class="modal-footer">
      	<button onclick="markingDown();" type="button" class="btn btn-primary" data-dismiss="modal">註記</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="exampleModalLabel">匯入資料</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<form action="/order/uploadKolOrder" method="POST">
				{{ csrf_field() }}
				<div>
					<span>廠商：</span>
				</div>
				<select class="form-control" name="kol">
					<option value="waymay">為美</option>
					<option value="bawmami">寶媽咪</option>
				</select>
				<div class="mt-2">
					<span>訂單資料：</span>
				</div>
				<div>
					<input type="file" class="form-control" id="uploadKolOrder" accept=".xlsx, .xls">
				</div>
				<input id="excel_data" type="hidden" name="excel_data">
				<button class="btn btn-primary mt-2" type="submit">上傳</button>
				<pre id="excel_output"></pre>				
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
		</div>
	  </div>
	</div>
  </div>

  <div class="modal fade" id="uploadShipmentNumModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="exampleModalLabel">匯入黑貓單號</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<form action="/order/uploadShipmentNum" method="POST">
				{{ csrf_field() }}
				<div class="mt-2">
					<span>貨運資料：</span>
				</div>
				<div>
					<input type="file" class="form-control" id="uploadShipmentNum" accept=".csv">
				</div>
				<input id="shipmentNumData" type="hidden" name="shipmentNumData">
				<button class="btn btn-primary mt-2" type="submit">上傳</button>
				<pre id="shipmentNumOutput"></pre>				
			</form>
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
				<input style="width: 130px;" type="text" name="ship_phone" class="input form-control" placeholder="電話" value="{{isset($_GET['ship_phone'])?$_GET['ship_phone']:''}}">
				-
				<input style="width: 130px;" id="bill_id" name="bill_id" type="text" class="input form-control" placeholder="訂單編號" value="{{isset($_GET['bill_id'])?$_GET['bill_id']:''}}">
				-
				<input style="width: 170px;" id="date1" name="date1" type="date" class="input form-control" value="{{isset($_GET['date1'])?$_GET['date1']:''}}">~
				<input style="width: 170px;" id="date2" name="date2" type="date" class="input form-control" value="{{isset($_GET['date2'])?$_GET['date2']:''}}">
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
				<span>/</span>
				<input name="pay_by_family" type="checkbox" value="FAMILY" {{isset($_GET['pay_by_family'])?'checked':''}}>全家代收
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

				<span>-</span>
				{{-- <select id="select_carrier" name="carrier_id">
					<option value="">貨運方式</option>
					@foreach ($carriers as $id => $name)
						<option value="{{$id}}">{{$name}}</option>
					@endforeach
				</select> --}}
				<select id="select_kol" name="kol">
					<option value="">官方訂單</option>
					<option value="waymay">為美</option>
					<option value="bawmami">寶媽咪</option>
				</select>
				

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
				<span>-</span>
				<button id="search-btn" class="btn btn-sm btn-primary" type="submit">搜尋</button>
				

			</form>
			

			<div class="tool-box">
				<button style="background-color: #000;color: #fff" onclick="selectAll();" class="btn btn-sm">全選</button>
				{{-- <button style="background-color: #008000;color: #fff" onclick="excel_family();" class="btn btn-sm">全家</button> --}}
				{{-- <button style="background-color: steelblue;color: #fff" onclick="csv_download();" class="btn btn-sm">黑貓</button> --}}
				{{-- <button style="background-color: teal;color: #fff" onclick="excel_hct();" class="btn btn-sm">新竹</button> --}}
				{{-- <button style="background-color: #d9534f;color: #fff" onclick="excel_accountant();" class="btn btn-sm">會計</button> --}}
				<button style="background-color: #000;color: #fff" onclick="selectPush();" class="btn btn-sm">下階段</button>
				
				<div class="dropdown">
					<button class="btn btn-sm text-white" style="background-color: green">匯出▼</button>
					<div class="dropdown-content">
					  <button class="btn btn-sm" style="background-color: steelblue;" onclick="csv_download();">黑貓</button>
					  <button class="btn btn-sm" style="background-color: #d9534f;" onclick="excel_accountant();">會計</button>
					  <button class="btn btn-sm" style="background-color:cadetblue ;" onclick="excel_shipmentNum();">貨運單</button>
					</div>
				</div>

				<div class="dropdown">
					<button class="btn btn-sm text-white" style="background-color: orange">匯入▼</button>
					<div class="dropdown-content">
						<button class="btn btn-sm" style="background-color: orange;" onclick="import_kol();">KOL訂單</button>
						<button class="btn btn-sm" style="background-color: steelblue;" onclick="import_carrierNum();">貨運單號</button>
					</div>
				</div>
			</div>

			{{-- <div class="tool-box2">
				<button style="background-color: orange; color: #fff" onclick="import_kol();" class="btn btn-sm">匯入訂單</button>
				<button style="background-color: #008000; color: #fff" onclick="import_carrierNum();" class="btn btn-sm">匯入貨運單</button>
				<button style="background-color: steelblue; color: #fff" onclick="excel_shipmentNum();" class="btn btn-sm">貨運單</button>
			</div> --}}


			<form id="csvForm" action="csv_download.php" target="_blank" method="POST" style="display:none ;">
				
				<input id="selectArray" type="text" name="orders" value="">
				{{-- <button id="csvGo" type="submit">go</button> --}}
			</form>
			<form id="excelForm_accountant" action="/order/ExportExcelForAccountant" target="_blank" method="POST" style="display:none ;">
				{{ csrf_field() }}
				<input id="selectArray_accountant" type="text" name="bill_id">
			</form>
			<form id="excelForm_shipmentNum" action="/order/ExportExcelForShipmentNum" target="_blank" method="POST" style="display:none ;">
				{{ csrf_field() }}
				<input id="selectArray_shipmentNum" type="text" name="bill_id">
			</form>
			{{-- <form id="excelForm_family" action="/order/ExportExcelForFamily" target="_blank" method="POST" style="display:none ;">
				{{ csrf_field() }}
				<input id="selectArray_family" type="text" name="bill_id">
			</form> --}}
			{{-- <form id="excelForm_hct" action="/order/ExportExcelForHCT" target="_blank" method="POST" style="display:none ;">
				{{ csrf_field() }}
				<input id="selectArray_hct" type="text" name="bill_id">
			</form> --}}

		</div>


		<div style="width: 100%;">
		<table class="table">
			<thead>
				<tr>
					<th class="th-green">#</th>
					<th class="th-green">訂單日期</th>
					<th class="th-green">訂購人</th>
					<th class="th-green">編號</th>
					{{-- <th>訂購人</th> --}}
					{{-- <th class="th-green">商品</th> --}}
					<th class="th-yellow">總價</th>
					<th class="th-yellow">付款方式</th>
					<th class="th-yellow">託運單號</th>

					<th class="th-red">付款狀態</th>

					<th class="th-green">到貨日</th>
					<th class="th-green">時間</th>
					
					{{-- <th>性別</th> --}}
					{{-- <th>電話</th> --}}
					{{-- <th>地址</th> --}}
					<th class="th-red">-</th>
					<th class="th-red">出貨</th>
					
					<th class="th-green">發票</th>
					
					<th class="th-green">備註</th>
					<th class="th-red">註記</th>
					<th class="th-red">刪除</th>
					
				</tr>
			</thead>
			<tbody>
				
				<?php $i=1 ?>
				@foreach($orders as $order)

				<tr class="table-tr">
					<td class="{{$order['user_id']==null?'fromSingle':''}} {{$order['pay_by']=='KOL'?'fromKol':''}}">
						{{$i++}}
					</td>
					<td>
						{!!str_replace(" ","<br>",$order->created_at)!!}
					</td>
					
					<td>
						{{$order->user_name}}
					</td>

					<td>
						<a href="{{url('order/showAll').'/'.$order['bill_id']}}" target="_blank">{{$order['bill_id']}}</a>
						<?php
							if($order['pay_by'] == "KOL") {
								echo '<br>' . '(' . $order['kolOrderNum'] . ')';
							}
						?>
					</td>

					<td>{{$order['price']}}</td>

					<td>
						{{$order['pay_by']}}
						<?php 
							if($order['pay_by'] == "KOL") {
								echo '-' . $order['kol'] . '<br>';
								echo '(' . $order['dumpNum'] . ')';
							}
						?>
					</td>

					<td>
						{{-- @if ($order->carrier_id == 1)
							全家店取
						@endif --}}
						{{($order->shipmentNum) ? $order->shipmentNum : '-'}}
					</td>

					<td>{{$order['status']}}</td>

					<td>
						@if($order['ship_arriveDate'] == null)-@else{{$order['ship_arriveDate']}}@endif
					</td>

					<td>
						@if($order['ship_time'] == 'no')-@else{{$order['ship_time']}}@endif
					</td>

					<td>
						<input type="checkbox" class="select-check-box" value="{{$order['bill_id']}}">
					</td>

					<td>
						<?php 
							$readyToGo = ($order->pay_by == "貨到付款" OR $order->pay_by == "FAMILY" OR $order->pay_by == "KOL" OR $order->status == 1) 
						?>

						@if($order->shipment == 0 AND $readyToGo)
						<button class="btn btn-sm btn-danger shipmentBtn" id="{{$order['bill_id']}}" onclick="shipment('{{$order['bill_id']}}','{{$order['shipment']}}');">可準備</button>

						@elseif($order->shipment == 1 AND $readyToGo)
						<button class="btn btn-sm btn-warning shipmentBtn" id="{{$order['bill_id']}}" onclick="shipment('{{$order['bill_id']}}','{{$order['shipment']}}');">準備中</button>

						@elseif($order->shipment==2)
						<button class="btn btn-sm btn-success shipmentBtn" id="{{$order['bill_id']}}" onclick="shipment('{{$order['bill_id']}}','{{$order['shipment']}}');">已出貨</button>

						@elseif($order->shipment==3)
							<font class="btn-sm" style="background-color: green;" color="#fff">＊結案＊</font>

						@else
						-
						@endif
					</td>

					<td>
						@if($order['ship_receipt'] == '2')二聯@else <font color="red">3聯</font>@endif
					</td>

					<td onclick="showMemo('{{$order['bill_id']}}');" data-toggle="modal" data-target="#exampleModal">
						@if($order['ship_memo'] != null)
							<font style="cursor: pointer;background-color: red;" color="yellow">！</font>
						@endif
					</td>

					<td>
						@if($order['allReturn'] == null)
						<img id="mark_{{$order['bill_id']}}" src="{{asset('images/admin_icon_markgray.png')}}" onclick="mark({{$order['bill_id']}});" data-toggle="modal" data-target="#markingModal">
						@else
						<img id="mark_{{$order['bill_id']}}" src="{{asset('images/admin_icon_markred.png')}}" onclick="mark({{$order['bill_id']}});" data-toggle="modal" data-target="#markingModal">
						@endif
					</td>

					<td>
						@if($order['status']!= 1 AND $order['shipment']==0)
						<img onclick="cancelBill({{$order['bill_id']}})" src="{{asset('images/admin_icon_delete.png')}}">
						@endif
					</td>


				</tr>
				@endforeach
				
			</tbody>
		</table>
		</div>

@endsection

@section('scripts')
{{ Html::script('js/bootstrap/bootstrap.min.js') }}
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
<script>
    document.getElementById('uploadKolOrder').addEventListener('change', (event) => {
      const file = event.target.files[0];
      const reader = new FileReader();

      reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const firstSheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];
        const jsonData = XLSX.utils.sheet_to_json(worksheet);

        document.getElementById('excel_output').textContent = JSON.stringify(jsonData, null, 2);
		document.getElementById('excel_data').value = JSON.stringify(jsonData, null, 2);
      };

      reader.readAsArrayBuffer(file);
    });

	document.getElementById('uploadShipmentNum').addEventListener('change', (event) => {
      const file = event.target.files[0];
	  if (file) {
		const reader = new FileReader();
		reader.onload = (e) => {
			const data = new Uint8Array(e.target.result);
			const decoder = new TextDecoder("big5");
			const text = decoder.decode(data);
			Papa.parse(text, {
				header: true,
				dynamicTyping: true,
				complete: (results) => {
					const jsonData = results.data;
					document.getElementById('shipmentNumOutput').textContent = JSON.stringify(jsonData, null, 2);
					document.getElementById('shipmentNumData').value = JSON.stringify(jsonData, null, 2);
				}
			});
		}
		reader.readAsArrayBuffer(file);
      }
    });

</script>
<script src="{{asset('js/order_marking.js')}}"></script>
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

		// @if (isset($_GET['carrier_id']))
		// $('#select_carrier').val('{{$_GET['carrier_id']}}');
		// @endif

		@if (isset($_GET['kol']))
		$('#select_kol').val('{{$_GET['kol']}}');
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

	function shipment($id,$val){
		
		if ($val == 2) {
			var r = confirm('確定修改出貨狀態');
			if(r ==false){
				return;
			}
		}


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
					$('#'+$id).attr("onclick","shipment('"+$id+"',"+"'"+1+"');");
				}else if(response == 2){
					$('#'+$id).removeClass('btn-warning').addClass('btn-success');
					$('#'+$id).html('已出貨');
					$('#'+$id).attr("onclick","shipment('"+$id+"',"+"'"+2+"');");
				}else if(response == 0){
					$('#'+$id).removeClass('btn-success').addClass('btn-danger');
					$('#'+$id).html('可準備');
					$('#'+$id).attr("onclick","shipment('"+$id+"',"+"'"+0+"');");
				}

			},
			error: function () {
	            alert('錯誤');

	        },
		});


	};

	function showMemo($id){
		$('#exampleModal .modal-body').empty();
		$('#exampleModal .modal-title').empty();
		
		$.ajax({
			type:'GET',
			url:'/order/'+$id,
			dataType:'json',
			success: function (response) {
				$('#exampleModal .modal-body').append(response.memo);
				$('#exampleModal .modal-title').append($id);
			},
			error: function () {
	            alert('錯誤');
	        },
		});
	}

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

		var selected = getSelectedBillId();
		if (selected.length <= 0) {
			alert('請選取訂單');
			return;
		}
		
		var r = confirm('確定送出');
		if(r ==false){ return; }

		$.ajax({
			type:'POST',
			url:'/order/updateShipment',
			dataType:'json',
			data: {
				selectArray:selected,
			},
			success: function (response) {
				location.reload();
			},
			error: function () {
		        alert('錯誤');
	        },
		});

	}

	function csv_download(){

		var selected = getSelectedBillId();
		
		if (selected.length > 0) {
			
			$.ajax({
				type:'POST',
				url:'/order/get_csv',
				dataType:'json',
				data: {
					type:0,
					selectArray:selected,
				},
				success: function (response) {
					
					$('#selectArray').val(response);
					// alert(response);
					
				},
				error: function (e) {
		            alert('錯誤');
		            console.log(e);
		        },
		        complete:function(){
		        	$('#csvForm').submit();	
		        },
			});
			
		}else{
			alert('請選取訂單');
		}

		

	}

	// function excel_hct() {
	// 	var selected = getSelectedBillId();
		
	// 	if (selected.length > 0) {
	// 		$('#selectArray_hct').val(JSON.stringify(selected));
	// 		$('#excelForm_hct').submit();
	// 	}else{
	// 		alert('請選取訂單');
	// 	}
	// }

	function excel_accountant(){

		var selected = getSelectedBillId();
		
		if (selected.length > 0) {
			$('#selectArray_accountant').val(JSON.stringify(selected));
			$('#excelForm_accountant').submit();
		}else{
			alert('請選取訂單');
		}

	}

	// function excel_family(){

	// 	var selected = getSelectedBillId();
		
	// 	if (selected.length > 0) {
	// 		$('#selectArray_family').val(JSON.stringify(selected));
	// 		$('#excelForm_family').submit();
	// 	}else{
	// 		alert('請選取訂單');
	// 	}

	// }

	function excel_shipmentNum(){
		var selected = getSelectedBillId();
		if (selected.length > 0) {
			$('#selectArray_shipmentNum').val(JSON.stringify(selected));
			$('#excelForm_shipmentNum').submit();
		}else{
			alert('請選取訂單');
		}
	}

	function import_kol() {
		$('#uploadModal').modal('show');
	}

	function import_carrierNum() {
		$('#uploadShipmentNumModal').modal('show');
	}

	function upload_file() {

	}

	function getSelectedBillId(){
		var selected = [];
		$('.select-check-box:checked').each(function(){
			selected.push($(this).val());
		});
		return selected;
	}

	</script>
@endsection
