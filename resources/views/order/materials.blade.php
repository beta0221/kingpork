@extends('admin_main')

@section('title','| 訂單原料需求')

@section('stylesheets')
<style>
	.filter-box{
		background-color: rgba(0,0,0,0.1);
		padding: 12px;
		margin-bottom: 20px;
	}
	.table{
		font-size: 14px;
	}
	.table td,.table th{
		text-align: center;
		border:1pt solid gray;
		padding: 6px 4px;
	}
	.table-tr:hover{
		background-color: rgba(0,0,0,0.05);
		cursor: pointer;
	}
	.materials-detail{
		display: none;
		background-color: #f9f9f9;
	}
	.materials-detail td{
		text-align: left;
		padding: 8px 20px;
	}
	.materials-list{
		list-style: none;
		padding: 0;
		margin: 8px 0;
	}
	.materials-list li{
		display: inline-block;
		background-color: #5cb85c;
		color: white;
		padding: 4px 8px;
		margin: 2px;
		border-radius: 3px;
		font-size: 12px;
	}
	.toggle-icon{
		cursor: pointer;
		user-select: none;
	}
	.summary-box{
		background-color: #d9edf7;
		padding: 15px;
		margin-bottom: 20px;
		border-radius: 4px;
		border: 1px solid #bce8f1;
	}
	.summary-box h4{
		margin-top: 0;
		color: #31708f;
	}
	.summary-materials{
		list-style: none;
		padding: 0;
	}
	.summary-materials li{
		display: inline-block;
		background-color: #5bc0de;
		color: white;
		padding: 6px 12px;
		margin: 4px;
		border-radius: 4px;
		font-weight: bold;
	}
	.shipment-badge{
		padding: 2px 6px;
		border-radius: 3px;
		color: white;
		font-size: 12px;
	}
	.shipment-0{ background-color: #d9534f; }
	.shipment-1{ background-color: #f0ad4e; }
	.shipment-2{ background-color: #5cb85c; }
	.shipment-3{ background-color: #5bc0de; }
	.batch-input-group{
		margin-bottom: 8px;
		padding: 8px;
		background-color: #f5f5f5;
		border-radius: 4px;
	}
	.batch-input-label{
		display: inline-block;
		width: 200px;
		font-weight: bold;
	}
	.result-box{
		margin-top: 20px;
		padding: 15px;
		border-radius: 4px;
	}
	.result-success{
		background-color: #dff0d8;
		border: 1px solid #d6e9c6;
	}
	.result-warning{
		background-color: #fcf8e3;
		border: 1px solid #faebcc;
	}
</style>
@endsection

@section('content')

<div class="container-fluid">
	<h2 style="display: inline-block;">出貨計劃</h2>
	<button class="btn btn-primary" style="margin-left: 20px;" data-toggle="modal" data-target="#shipmentPlanModal">
		<i class="glyphicon glyphicon-cog"></i> 設置出貨階段
	</button>

	<!-- 篩選區 -->
	<div class="filter-box">
		<form action="{{url('order/materials')}}" method="GET">
			<div class="form-inline">
				<label>日期區間：</label>
				<input type="date" name="date1" class="form-control input-sm" value="{{request('date1')}}">
				<span>~</span>
				<input type="date" name="date2" class="form-control input-sm" value="{{request('date2')}}">

				<span style="margin-left: 20px;">每頁筆數：</span>
				<select name="per_page" class="form-control input-sm" style="width: 80px; display: inline-block;">
					<option value="20" {{request('per_page', 20) == 20 ? 'selected' : ''}}>20</option>
					<option value="50" {{request('per_page', 20) == 50 ? 'selected' : ''}}>50</option>
					<option value="100" {{request('per_page', 20) == 100 ? 'selected' : ''}}>100</option>
				</select>

				<button type="submit" class="btn btn-primary btn-sm" style="margin-left: 20px;">搜尋</button>
				<a href="{{url('order/materials')}}" class="btn btn-default btn-sm">清除篩選</a>
			</div>
		</form>

		<!-- 分頁 -->
		<div style="text-align: center; margin-top: 12px;">
			{{ $bills->appends(request()->query())->links() }}
		</div>
	</div>

	<!-- 總計區 -->
	@if(!empty($totalMaterials))
	<div class="summary-box">
		<h4>當前頁面原料需求總計</h4>
		<ul class="summary-materials">
			@foreach($totalMaterials as $slug => $quantity)
				<li>
					{{ isset($inventoryDict[$slug]) ? $inventoryDict[$slug] : $slug }}：
					<strong>{{ $quantity }}</strong>
				</li>
			@endforeach
		</ul>
	</div>
	@endif

	<!-- 訂單列表 -->
	<table class="table table-bordered">
		<thead>
			<tr style="background-color: #337ab7; color: white;">
				<th style="width: 40px;"></th>
				<th>訂單編號</th>
				<th>訂單日期</th>
				<th>客戶姓名</th>
				<th>出貨狀態</th>
				<th>訂單金額</th>
			</tr>
		</thead>
		<tbody>
			@forelse($billsWithMaterials as $index => $data)
				<?php
					$bill = $data['bill'];
					$items = $data['items'];
					$materials = $data['materials'];
					$rowId = 'row-' . $bill->id;
				?>
				<tr class="table-tr" onclick="toggleDetail('{{$rowId}}')">
					<td>
						<span class="toggle-icon" id="icon-{{$rowId}}">▶</span>
					</td>
					<td>{{ $bill->bill_id }}</td>
					<td>{{ substr($bill->created_at, 0, 10) }}</td>
					<td>{{ $bill->user_name }}</td>
					<td>
						<span class="shipment-badge shipment-{{$bill->shipment}}">
							{{ $bill->shipmentName() }}
						</span>
					</td>
					<td>{{ number_format($bill->price) }} 元</td>
				</tr>
				<tr id="{{$rowId}}" class="materials-detail">
					<td colspan="6">
						<div class="row">
							<div class="col-md-6">
								<h5><strong>訂單商品明細</strong></h5>
								<table class="table table-sm table-bordered">
									<thead>
										<tr>
											<th>商品名稱</th>
											<th>數量</th>
											<th>單價</th>
											<th>小計</th>
										</tr>
									</thead>
									<tbody>
										@foreach($items as $item)
										<tr>
											<td>{{ $item->name }}</td>
											<td>{{ $item->quantity }}</td>
											<td>{{ number_format($item->price) }}</td>
											<td>{{ number_format($item->price * $item->quantity) }}</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<div class="col-md-6">
								<h5><strong>原料需求</strong></h5>
								@if(!empty($materials))
									<ul class="materials-list">
										@foreach($materials as $slug => $quantity)
											<li>
												{{ isset($inventoryDict[$slug]) ? $inventoryDict[$slug] : $slug }}：
												<strong>{{ $quantity }}</strong>
											</li>
										@endforeach
									</ul>
								@else
									<p style="color: #999;">此訂單無原料需求資料</p>
								@endif
							</div>
						</div>
					</td>
				</tr>
			@empty
				<tr>
					<td colspan="6" style="text-align: center; padding: 30px; color: #999;">
						無符合條件的訂單
					</td>
				</tr>
			@endforelse
		</tbody>
	</table>
</div>

<!-- 設置出貨階段 Modal -->
<div class="modal fade" id="shipmentPlanModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">設置批號使用計劃</h4>
			</div>
			<div class="modal-body">
				<p class="text-muted">請設定本次要使用的批號數量，系統將計算可完成的訂單數量</p>

				<form id="batchPlanForm">
					<div id="batchInputs">
						@foreach($batches as $inventoryName => $batchGroup)
							<h5 style="margin-top: 15px; color: #337ab7;">
								<strong>{{ $inventoryName }}</strong>
							</h5>
							@foreach($batchGroup as $batch)
								<div class="batch-input-group">
									<span class="batch-input-label">
										批號: {{ $batch->batch_number }}
										<small class="text-muted">(庫存: {{ $batch->quantity }})</small>
									</span>
									<input
										type="number"
										class="form-control input-sm"
										name="batch[{{ $batch->id }}]"
										min="0"
										max="{{ $batch->quantity }}"
										value="{{ $batch->quantity }}"
										style="width: 120px; display: inline-block;"
									>
								</div>
							@endforeach
						@endforeach
					</div>

					<button type="button" class="btn btn-primary" onclick="calculatePlan()">
						<i class="glyphicon glyphicon-check"></i> 計算出貨計劃
					</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				</form>

				<!-- 結果顯示區 -->
				<div id="planResult"></div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script>
function toggleDetail(rowId) {
	var detailRow = document.getElementById(rowId);
	var icon = document.getElementById('icon-' + rowId);

	if (detailRow.style.display === 'none' || detailRow.style.display === '') {
		detailRow.style.display = 'table-row';
		icon.textContent = '▼';
	} else {
		detailRow.style.display = 'none';
		icon.textContent = '▶';
	}
}

function calculatePlan() {
	// 收集批號數據
	var batchPlan = {};
	var inputs = document.querySelectorAll('#batchPlanForm input[name^="batch"]');

	inputs.forEach(function(input) {
		var batchId = input.name.match(/\d+/)[0];
		var quantity = parseInt(input.value) || 0;
		if (quantity > 0) {
			batchPlan[batchId] = quantity;
		}
	});

	if (Object.keys(batchPlan).length === 0) {
		alert('請至少設定一個批號的數量');
		return;
	}

	// 發送 AJAX 請求
	$.ajax({
		url: '{{ url("order/materials/calculate-plan") }}',
		method: 'POST',
		data: {
			batch_plan: batchPlan,
			_token: '{{ csrf_token() }}'
		},
		beforeSend: function() {
			$('#planResult').html('<div class="text-center"><i class="glyphicon glyphicon-refresh spinning"></i> 計算中...</div>');
		},
		success: function(response) {
			displayResult(response);
		},
		error: function(xhr) {
			$('#planResult').html('<div class="alert alert-danger">計算失敗，請稍後再試</div>');
		}
	});
}

function displayResult(data) {
	var html = '<div class="result-box ' + (data.stop_order ? 'result-warning' : 'result-success') + '">';

	html += '<h4><strong>計算結果</strong></h4>';
	html += '<p><strong>可完成訂單數量：</strong>' + data.completed_count + ' 筆</p>';

	if (data.stop_order) {
		html += '<hr>';
		html += '<h5 class="text-danger"><strong>停止訂單</strong></h5>';
		html += '<p>訂單編號：<strong>' + data.stop_order.bill_id + '</strong></p>';
		html += '<p>客戶姓名：' + data.stop_order.user_name + '</p>';
		html += '<p>訂單日期：' + data.stop_order.created_at + '</p>';

		html += '<h5 class="text-danger"><strong>停止原因（原料不足）</strong></h5>';
		html += '<ul>';
		data.stop_reason.forEach(function(reason) {
			html += '<li>' + reason.name + '：需要 <strong>' + reason.required + '</strong>，剩餘 <strong>' + reason.available + '</strong></li>';
		});
		html += '</ul>';
	} else {
		html += '<p class="text-success"><strong>所有訂單都可以完成！</strong></p>';
	}

	// 顯示批號剩餘量
	html += '<hr>';
	html += '<h5><strong>批號預計剩餘量</strong></h5>';
	html += '<table class="table table-sm table-bordered" style="font-size: 12px;">';
	html += '<tr><th>原料</th><th>批號</th><th>原始數量</th><th>剩餘數量</th></tr>';

	for (var batchId in data.batch_remaining) {
		var batch = data.batch_remaining[batchId];
		var used = batch.original - batch.available;
		html += '<tr>';
		html += '<td>' + batch.inventory_name + '</td>';
		html += '<td>' + batch.batch_number + '</td>';
		html += '<td>' + batch.original + '</td>';
		html += '<td>' + batch.available + ' <small class="text-muted">(已使用 ' + used + ')</small></td>';
		html += '</tr>';
	}

	html += '</table>';
	html += '</div>';

	$('#planResult').html(html);
}
</script>
<style>
.spinning {
	animation: spin 1s linear infinite;
}
@keyframes spin {
	0% { transform: rotate(0deg); }
	100% { transform: rotate(360deg); }
}
</style>
@endsection
