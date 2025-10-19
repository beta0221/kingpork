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
		display: table-row;
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
	/* 批號狀態樣式 */
	.batch-depleted{
		border: 2px solid #d9534f !important;
		background-color: #f2dede !important;
		opacity: 0.6;
		cursor: not-allowed !important;
	}
	.batch-depleted label{
		cursor: not-allowed !important;
	}
	.batch-partial{
		border: 2px solid #f0ad4e !important;
		background-color: #fcf8e3 !important;
	}
	.batch-fresh{
		border: 1px solid #5cb85c !important;
	}
</style>
@endsection

@section('content')

<div class="container-fluid">	

	<!-- 篩選區 -->
	<div class="filter-box">
		<form class="d-inline-block" action="{{url('order/materials')}}" method="GET">
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

		<button class="btn btn-primary" data-toggle="modal" data-target="#shipmentPlanModal">
			<i class="glyphicon glyphicon-cog"></i> 設置出貨階段
		</button>

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
						<span class="toggle-icon" id="icon-{{$rowId}}">▼</span>
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
				<tr id="{{$rowId}}" class="materials-detail" style="display: table-row;">
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
							<?php
								// 取得第一個批號作為預設選中
								$firstBatch = $batchGroup->first();
								$inventorySlug = $firstBatch->inventory->slug;
							?>
							<h5 style="margin-top: 15px; color: #337ab7;">
								<strong>{{ $inventoryName }}</strong>
							</h5>
							@foreach($batchGroup as $index => $batch)
								<div class="batch-input-group" data-inventory-slug="{{ $inventorySlug }}">
									<label style="margin-bottom: 0; display: flex; align-items: center; cursor: pointer;">
										<input
											type="radio"
											name="inventory_{{ $inventorySlug }}"
											value="{{ $batch->id }}"
											data-batch-id="{{ $batch->id }}"
											data-inventory-slug="{{ $inventorySlug }}"
											data-quantity="{{ $batch->quantity }}"
											data-batch-number="{{ $batch->batch_number }}"
											{{ $index === 0 ? 'checked' : '' }}
											style="margin-right: 8px;"
										>
										<span class="batch-input-label" style="width: auto;">
											批號: {{ $batch->batch_number }}
											<small class="text-muted">(庫存: {{ $batch->quantity }})</small>
										</span>
									</label>
								</div>
							@endforeach
						@endforeach
					</div>

					<button type="button" class="btn btn-primary" onclick="calculatePlan()">
						<i class="glyphicon glyphicon-check"></i> 計算出貨計劃
					</button>
					<button type="button" class="btn btn-warning" onclick="resetPlan()">
						<i class="glyphicon glyphicon-refresh"></i> 重新計劃
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
// 全域狀態：追蹤多階段計劃
var shipmentPlanState = {
	stage: 0,  // 當前階段
	allCompletedOrders: [],  // 所有已完成訂單
	lastStopOrder: null,  // 最後停止訂單
	startFromBillId: null,  // 從哪筆訂單開始
	batchRemaining: {},  // 批號剩餘量 {batchId: {available, original, ...}}
	stopOrderConsumed: {},  // 停止訂單已消耗量 {slug: quantity}
	stopOrderBatchUsage: {},  // 停止訂單的批號使用明細 {slug: [{batch_number, quantity_used, ...}]}
	stageHistory: []  // 階段歷史記錄（用於回復上階段）
};

// 頁面載入時保存所有批號的原始數量
var originalBatchQuantities = {};
$(document).ready(function() {
	var allBatchGroups = document.querySelectorAll('.batch-input-group');
	allBatchGroups.forEach(function(group) {
		var radio = group.querySelector('input[type="radio"]');
		var batchId = radio.getAttribute('data-batch-id');
		var quantity = radio.getAttribute('data-quantity');
		var batchNumber = radio.getAttribute('data-batch-number');

		originalBatchQuantities[batchId] = {
			quantity: parseInt(quantity),
			batchNumber: batchNumber
		};
	});
});

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
	// 收集批號數據：從選中的 radio 直接讀取全部數量
	var batchPlan = {};

	// 找到所有被選中的 radio buttons
	var selectedRadios = document.querySelectorAll('#batchPlanForm input[type="radio"]:checked');

	selectedRadios.forEach(function(radio) {
		var batchId = radio.getAttribute('data-batch-id');
		var quantity = parseInt(radio.getAttribute('data-quantity')) || 0;

		if (quantity > 0) {
			batchPlan[batchId] = quantity;
		}
	});

	if (Object.keys(batchPlan).length === 0) {
		alert('請選擇至少一個有庫存的批號');
		return;
	}

	// 準備多階段參數
	shipmentPlanState.stage += 1;
	var requestData = {
		batch_plan: batchPlan,
		stage: shipmentPlanState.stage,
		start_from_bill_id: shipmentPlanState.startFromBillId,
		previous_completed_orders: shipmentPlanState.allCompletedOrders,
		stop_order_consumed: shipmentPlanState.stopOrderConsumed,  // 停止訂單已消耗量
		stop_order_batch_usage: shipmentPlanState.stopOrderBatchUsage,  // 停止訂單的批號使用明細
		previous_batch_remaining: shipmentPlanState.batchRemaining,  // 前階段批號剩餘量
		_token: '{{ csrf_token() }}'
	};

	// 發送 AJAX 請求
	$.ajax({
		url: '{{ url("order/materials/calculate-plan") }}',
		method: 'POST',
		data: requestData,
		beforeSend: function() {
			$('#planResult').html('<div class="text-center"><i class="glyphicon glyphicon-refresh spinning"></i> 計算中...</div>');
		},
		success: function(response) {
			// 更新全域狀態
			shipmentPlanState.allCompletedOrders = response.all_completed_orders;
			shipmentPlanState.lastStopOrder = response.stop_order;
			shipmentPlanState.batchRemaining = response.batch_remaining || {};  // 保存批號剩餘量
			if (response.stop_order) {
				shipmentPlanState.startFromBillId = response.stop_order.bill_id;
				// 保存停止訂單的累積消耗量
				shipmentPlanState.stopOrderConsumed = response.stop_order.consumed || {};
				// 保存停止訂單的批號使用明細
				shipmentPlanState.stopOrderBatchUsage = response.stop_order.batch_usage || {};
			} else {
				// 如果沒有停止訂單，清空消耗記錄
				shipmentPlanState.stopOrderConsumed = {};
				shipmentPlanState.stopOrderBatchUsage = {};
			}

			// 保存當前階段的完整 response 到歷史記錄（用於回復上階段）
			shipmentPlanState.stageHistory.push({
				stage: shipmentPlanState.stage,
				response: JSON.parse(JSON.stringify(response))  // 深拷貝 response
			});

			displayResult(response);
		},
		error: function(xhr) {
			shipmentPlanState.stage -= 1;  // 計算失敗，回退階段
			$('#planResult').html('<div class="alert alert-danger">計算失敗，請稍後再試</div>');
		}
	});
}

// 回復到上一個階段
function goBackToPreviousStage() {
	if (shipmentPlanState.stageHistory.length <= 1) {
		alert('已經是第一階段，無法返回');
		return;
	}

	// 移除當前階段
	shipmentPlanState.stageHistory.pop();

	// 獲取上一階段的資料
	var previousStageData = shipmentPlanState.stageHistory[shipmentPlanState.stageHistory.length - 1];
	var previousResponse = previousStageData.response;

	// 恢復全域狀態到上一階段
	shipmentPlanState.stage = previousStageData.stage;
	shipmentPlanState.allCompletedOrders = previousResponse.all_completed_orders;
	shipmentPlanState.lastStopOrder = previousResponse.stop_order;
	shipmentPlanState.batchRemaining = previousResponse.batch_remaining || {};
	if (previousResponse.stop_order) {
		shipmentPlanState.startFromBillId = previousResponse.stop_order.bill_id;
		shipmentPlanState.stopOrderConsumed = previousResponse.stop_order.consumed || {};
		shipmentPlanState.stopOrderBatchUsage = previousResponse.stop_order.batch_usage || {};
	} else {
		shipmentPlanState.startFromBillId = null;
		shipmentPlanState.stopOrderConsumed = {};
		shipmentPlanState.stopOrderBatchUsage = {};
	}

	// 重新顯示上一階段的結果
	displayResult(previousResponse);

	// 更新批號狀態（不清空結果）
	updateBatchDisplayOnly();
}

// 僅更新批號顯示狀態（不清空結果）
function updateBatchDisplayOnly() {
	// 追蹤每個原料當前選中的批號和是否需要切換
	var inventorySwitchNeeded = {};  // {slug: {currentBatchId, shouldSwitch}}

	// 第一步：更新所有批號的 data-quantity 和顯示文字
	var allBatchGroups = document.querySelectorAll('.batch-input-group');
	allBatchGroups.forEach(function(group) {
		var inventorySlug = group.getAttribute('data-inventory-slug');
		var radio = group.querySelector('input[type="radio"]');
		var batchId = radio.getAttribute('data-batch-id');
		var labelSpan = group.querySelector('.batch-input-label');

		// 移除舊的樣式類別
		group.classList.remove('batch-depleted', 'batch-partial', 'batch-fresh');

		// 如果該批號在上一階段有使用記錄
		if (shipmentPlanState.batchRemaining[batchId]) {
			var batchInfo = shipmentPlanState.batchRemaining[batchId];
			var remaining = batchInfo.available;
			var original = batchInfo.original;
			var batchNumber = batchInfo.batch_number;

			// 更新 radio 的 data-quantity 為剩餘數量
			radio.setAttribute('data-quantity', remaining);

			// 檢查該批號是否被選中
			var isCurrentlySelected = radio.checked;

			// 記錄此批號的狀態
			if (isCurrentlySelected) {
				if (!inventorySwitchNeeded[inventorySlug]) {
					inventorySwitchNeeded[inventorySlug] = {
						currentBatchId: batchId,
						shouldSwitch: (remaining === 0)
					};
				}
			}

			// 更新顯示文字和禁用狀態
			if (remaining === 0) {
				// 已用完 - 禁用此批號
				radio.disabled = true;
				labelSpan.innerHTML = '批號: ' + batchNumber +
					' <small class="text-danger"><strong>⚠️ 已用完</strong> (原始: ' + original + ')</small>';
				group.classList.add('batch-depleted');
			} else if (remaining < original) {
				// 部分使用 - 確保可用
				radio.disabled = false;
				labelSpan.innerHTML = '批號: ' + batchNumber +
					' <small class="text-warning"><strong>剩餘: ' + remaining + '</strong> (原始: ' + original + ')</small>';
				group.classList.add('batch-partial');
			} else {
				// 未使用（剩餘 = 原始）- 確保可用
				radio.disabled = false;
				labelSpan.innerHTML = '批號: ' + batchNumber +
					' <small class="text-muted">(庫存: ' + original + ')</small>';
				group.classList.add('batch-fresh');
			}
		} else {
			// 該批號在上一階段沒有使用記錄 - 恢復為原始狀態
			var originalBatchNumber = radio.getAttribute('data-batch-number');
			var originalQuantityAttr = radio.getAttribute('data-original-quantity');

			// 如果還沒有記錄原始數量，現在記錄
			if (!originalQuantityAttr) {
				var currentQty = radio.getAttribute('data-quantity');
				radio.setAttribute('data-original-quantity', currentQty);
				originalQuantityAttr = currentQty;
			}

			var originalQuantity = parseInt(originalQuantityAttr);

			// 恢復為未使用狀態
			radio.disabled = false;
			radio.setAttribute('data-quantity', originalQuantity);
			labelSpan.innerHTML = '批號: ' + originalBatchNumber +
				' <small class="text-muted">(庫存: ' + originalQuantity + ')</small>';
			group.classList.add('batch-fresh');
		}
	});

	// 第二步：如果當前選中的批號已用完，自動切換到下一個可用批號
	for (var inventorySlug in inventorySwitchNeeded) {
		if (inventorySwitchNeeded[inventorySlug].shouldSwitch) {
			// 找到該原料的所有批號
			var allRadiosForInventory = document.querySelectorAll('input[name="inventory_' + inventorySlug + '"]');
			var foundAvailable = false;

			allRadiosForInventory.forEach(function(radio) {
				if (foundAvailable) return;

				var batchId = radio.getAttribute('data-batch-id');
				var batchInfo = shipmentPlanState.batchRemaining[batchId];

				// 如果該批號有剩餘量，選中它
				if (batchInfo && batchInfo.available > 0) {
					radio.checked = true;
					foundAvailable = true;
				}
			});

			// 如果沒有找到可用批號，保持當前選擇（數量為0）
		}
	}
}

// 繼續出貨計劃（補充批號後繼續）
function continuePlan() {
	// 清空結果顯示，準備下一階段
	$('#planResult').html('<div class="alert alert-info"><i class="glyphicon glyphicon-info-sign"></i> 請選擇批號後，點擊「計算出貨計劃」繼續</div>');

	// 更新批號狀態
	updateBatchDisplayOnly();

	// 滾動到批號輸入區
	$('#batchInputs')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// 重新計劃（重置所有階段狀態）
function resetPlan() {
	if (!confirm('確定要重新計劃嗎？這將清除所有階段的計算結果。')) {
		return;
	}

	// 重置全域狀態
	shipmentPlanState = {
		stage: 0,
		allCompletedOrders: [],
		lastStopOrder: null,
		startFromBillId: null,
		batchRemaining: {},
		stopOrderConsumed: {},
		stopOrderBatchUsage: {},
		stageHistory: []
	};

	// 清空結果顯示
	$('#planResult').html('');

	// 恢復所有批號的原始狀態
	var allBatchGroups = document.querySelectorAll('.batch-input-group');
	allBatchGroups.forEach(function(group) {
		var radio = group.querySelector('input[type="radio"]');
		var labelSpan = group.querySelector('.batch-input-label');
		var batchId = radio.getAttribute('data-batch-id');

		// 從保存的原始數量中取得
		var originalData = originalBatchQuantities[batchId];
		if (originalData) {
			var originalQuantity = originalData.quantity;
			var batchNumber = originalData.batchNumber;

			// 恢復原始數量
			radio.setAttribute('data-quantity', originalQuantity);
			radio.disabled = false;

			// 恢復原始顯示
			labelSpan.innerHTML = '批號: ' + batchNumber +
				' <small class="text-muted">(庫存: ' + originalQuantity + ')</small>';

			// 移除所有狀態樣式
			group.classList.remove('batch-depleted', 'batch-partial', 'batch-fresh');
		}
	});

	// 提示訊息
	$('#planResult').html('<div class="alert alert-success"><i class="glyphicon glyphicon-ok"></i> 已重置計劃狀態，請重新選擇批號並計算</div>');
}

function displayResult(data) {
	var html = '<div class="result-box ' + (data.stop_order ? 'result-warning' : 'result-success') + '">';

	// 顯示階段資訊
	if (data.stage > 1) {
		html += '<div class="alert alert-info" style="margin-bottom: 15px;">';
		html += '<div style="display: flex; justify-content: space-between; align-items: center;">';
		html += '<strong>階段 ' + data.stage + '</strong>';
		html += '<button type="button" class="btn btn-default btn-sm" onclick="goBackToPreviousStage()">';
		html += '<i class="glyphicon glyphicon-arrow-left"></i> 回復上階段';
		html += '</button>';
		html += '</div>';
		html += '</div>';
	}

	html += '<h4><strong>計算結果</strong></h4>';

	// 顯示本階段和累積資訊
	if (data.stage > 1) {
		html += '<p><strong>本階段完成訂單：</strong>' + data.completed_count + ' 筆</p>';
		html += '<p><strong>累積完成訂單：</strong>' + data.total_completed_count + ' 筆</p>';
		html += '<p><strong>剩餘訂單：</strong>' + data.remaining_orders + ' 筆</p>';
	} else {
		html += '<p><strong>可完成訂單數量：</strong>' + data.completed_count + ' 筆</p>';
	}

	html += '<h5 class="text-primary"><strong>出貨範圍</strong></h5>';
	if (data.stage > 1 && data.all_bill_range) {
		html += '<p>本階段：' + (data.bill_range || '無') + '</p>';
		html += '<p>累積範圍：' + data.all_bill_range + '</p>';
	} else {
		html += '<p>訂單編號：' + (data.bill_range || '無') + '</p>';
	}

	// 顯示已完成訂單的批號使用明細
	if (data.completed_count > 0 && data.completed_orders) {
		html += '<hr>';
		html += '<h5 class="text-success"><strong>已完成訂單批號使用明細</strong></h5>';
		html += '<div style="max-height: 300px; overflow-y: auto;">';

		data.completed_orders.forEach(function(order) {
			html += '<div style="margin-bottom: 15px; padding: 10px; background-color: #f9f9f9; border-left: 3px solid #5cb85c;">';
			html += '<strong>訂單 ' + order.bill_id + '</strong> - ' + order.user_name + ' (' + order.created_at + ')';

			if (order.batch_usage && Object.keys(order.batch_usage).length > 0) {
				html += '<table class="table table-sm table-bordered" style="margin-top: 8px; font-size: 11px; background-color: white;">';
				html += '<tr><th style="width: 30%;">原料</th><th style="width: 30%;">批號</th><th style="width: 40%;">使用數量</th></tr>';

				for (var slug in order.batch_usage) {
					var batches = order.batch_usage[slug];
					batches.forEach(function(batch) {
						html += '<tr>';
						html += '<td>' + batch.inventory_name + '</td>';
						html += '<td>' + batch.batch_number + '</td>';
						html += '<td><strong>' + batch.quantity_used + '</strong></td>';
						html += '</tr>';
					});
				}

				html += '</table>';
			} else {
				html += '<p style="font-size: 11px; color: #999; margin: 5px 0 0 0;">無批號使用記錄</p>';
			}

			html += '</div>';
		});

		html += '</div>';
	}

	if (data.stop_order) {
		html += '<hr>';
		html += '<h5 class="text-danger"><strong>停止訂單</strong></h5>';
		html += '<p>訂單編號：<strong>' + data.stop_order.bill_id + '</strong></p>';
		html += '<p>客戶姓名：' + data.stop_order.user_name + '</p>';
		html += '<p>訂單日期：' + data.stop_order.created_at + '</p>';

		html += '<h5 class="text-danger"><strong>停止原因（原料不足）</strong></h5>';
		html += '<ul>';
		data.stop_reason.forEach(function(reason) {
			html += '<li>' + reason.name + '：需要 <strong>' + reason.required + '</strong>，剩餘 <strong>' + reason.available + '</strong>';
			var shortage = reason.required - reason.available;
			html += ' <span class="text-danger">(不足 ' + shortage + ')</span>';
			html += '</li>';
		});
		html += '</ul>';

		// 顯示停止訂單已消耗的批號明細
		if (data.stop_order.batch_usage && Object.keys(data.stop_order.batch_usage).length > 0) {
			html += '<h5 class="text-warning"><strong>本訂單已使用批號明細</strong></h5>';
			html += '<div style="padding: 10px; background-color: #fff9e6; border-left: 3px solid #f0ad4e; margin-bottom: 15px;">';
			html += '<table class="table table-sm table-bordered" style="font-size: 11px; background-color: white;">';
			html += '<tr><th style="width: 30%;">原料</th><th style="width: 30%;">批號</th><th style="width: 40%;">已使用數量</th></tr>';

			for (var slug in data.stop_order.batch_usage) {
				var batches = data.stop_order.batch_usage[slug];
				batches.forEach(function(batch) {
					html += '<tr>';
					html += '<td>' + batch.inventory_name + '</td>';
					html += '<td>' + batch.batch_number + '</td>';
					html += '<td><strong>' + batch.quantity_used + '</strong></td>';
					html += '</tr>';
				});
			}

			html += '</table>';
			html += '<p style="font-size: 11px; color: #856404; margin: 5px 0 0 0;"><i class="glyphicon glyphicon-info-sign"></i> 此訂單因原料不足而停止，上方為已消耗的批號明細</p>';
			html += '</div>';
		}

		// 如果還有剩餘訂單，顯示「繼續計劃」按鈕
		if (data.can_continue) {
			html += '<div style="margin-top: 20px; padding: 15px; background-color: #fcf8e3; border: 1px solid #faebcc; border-radius: 4px;">';
			html += '<p><strong><i class="glyphicon glyphicon-exclamation-sign"></i> 還有 ' + data.remaining_orders + ' 筆訂單待處理</strong></p>';
			html += '<p>請補充批號數量後，點擊下方按鈕繼續出貨計劃</p>';
			html += '<button type="button" class="btn btn-warning btn-lg" onclick="continuePlan()">';
			html += '<i class="glyphicon glyphicon-forward"></i> 繼續出貨計劃';
			html += '</button>';
			html += '</div>';
		}
	} else {
		html += '<p class="text-success"><strong><i class="glyphicon glyphicon-ok-circle"></i> 所有訂單都可以完成！</strong></p>';
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
