@extends('admin_main')

@section('title','| 出貨計劃詳情')

@section('stylesheets')
<style>
	.info-box{
		background-color: #f5f5f5;
		padding: 20px;
		margin-bottom: 20px;
		border-radius: 4px;
		border: 1px solid #ddd;
	}
	.info-box h3{
		margin-top: 0;
		color: #337ab7;
	}
	.info-box .info-row{
		margin-bottom: 10px;
	}
	.info-box .info-label{
		font-weight: bold;
		display: inline-block;
		width: 120px;
	}
	.status-badge{
		padding: 5px 10px;
		border-radius: 3px;
		color: white;
		font-size: 13px;
		font-weight: bold;
	}
	.status-pending{ background-color: #f0ad4e; }
	.status-in_progress{ background-color: #5bc0de; }
	.status-completed{ background-color: #5cb85c; }

	.stage-box{
		background-color: #fff;
		border: 1px solid #ddd;
		border-radius: 4px;
		padding: 15px;
		margin-bottom: 15px;
	}
	.stage-box h4{
		margin-top: 0;
		color: #5bc0de;
		border-bottom: 2px solid #5bc0de;
		padding-bottom: 10px;
	}
	.order-item{
		background-color: #f9f9f9;
		border-left: 3px solid #5cb85c;
		padding: 10px;
		margin-bottom: 10px;
	}
	.order-item strong{
		color: #333;
	}
	.batch-usage-table{
		font-size: 12px;
		margin-top: 8px;
	}
	.batch-usage-table th{
		background-color: #337ab7;
		color: white;
	}
	.action-buttons{
		margin-top: 30px;
		padding: 20px;
		background-color: #f5f5f5;
		border-radius: 4px;
		text-align: center;
	}
</style>
@endsection

@section('content')

<div class="container-fluid">

	<!-- 頁首 -->
	<div style="margin-bottom: 20px;">
		<a href="{{ url('order/shipment-plan') }}" class="btn btn-default">
			<i class="glyphicon glyphicon-arrow-left"></i> 返回列表
		</a>
	</div>

	<!-- 計劃基本資訊 -->
	<div class="info-box">
		<h3><i class="glyphicon glyphicon-info-sign"></i> 計劃資訊</h3>
		<div class="info-row">
			<span class="info-label">計劃名稱：</span>
			<strong>{{ $plan->plan_name }}</strong>
		</div>
		<div class="info-row">
			<span class="info-label">狀態：</span>
			<span class="status-badge status-{{ $plan->status }}">
				@if($plan->status === 'pending')
					待處理
				@elseif($plan->status === 'in_progress')
					進行中
				@else
					已完成
				@endif
			</span>
		</div>
		<div class="info-row">
			<span class="info-label">總訂單數：</span>
			<strong>{{ $plan->total_orders }}</strong> 筆
		</div>
		<div class="info-row">
			<span class="info-label">總階段數：</span>
			<strong>{{ $plan->total_stages }}</strong> 階段
		</div>
		<div class="info-row">
			<span class="info-label">建立時間：</span>
			{{ $plan->created_at }}
		</div>
		<div class="info-row">
			<span class="info-label">更新時間：</span>
			{{ $plan->updated_at }}
		</div>
	</div>

	<!-- 階段詳情 -->
	@if(isset($plan->plan_data['stages']) && is_array($plan->plan_data['stages']))
		<h3 style="color: #337ab7; border-bottom: 2px solid #337ab7; padding-bottom: 10px;">
			<i class="glyphicon glyphicon-list-alt"></i> 階段詳情
		</h3>

		@foreach($plan->plan_data['stages'] as $stageIndex => $stageData)
			<?php $data = $stageData['response']; ?>
			<div class="stage-box">
				<h4>
					<i class="glyphicon glyphicon-record"></i>
					階段 {{ $data['stage'] }}
					<small class="text-muted">
						(本階段完成 {{ $data['completed_count'] }} 筆訂單)
					</small>
				</h4>

				<!-- 階段統計 -->
				<div style="margin-bottom: 15px; padding: 10px; background-color: #e7f3ff; border-radius: 4px;">
					<div class="row">
						<div class="col-md-3">
							<strong>本階段完成：</strong> {{ $data['completed_count'] }} 筆
						</div>
						<div class="col-md-3">
							<strong>累積完成：</strong> {{ $data['total_completed_count'] ?? $data['completed_count'] }} 筆
						</div>
						<div class="col-md-3">
							<strong>剩餘訂單：</strong> {{ $data['remaining_orders'] ?? 0 }} 筆
						</div>
						<div class="col-md-3">
							<strong>訂單範圍：</strong> {{ $data['bill_range'] ?? '無' }}
						</div>
					</div>
				</div>

				<!-- 已完成訂單 -->
				@if(isset($data['completed_orders']) && count($data['completed_orders']) > 0)
					<h5 style="color: #5cb85c; font-weight: bold;">
						<i class="glyphicon glyphicon-ok-circle"></i> 已完成訂單
					</h5>
					<div style="max-height: 400px; overflow-y: auto;">
						@foreach($data['completed_orders'] as $order)
							<div class="order-item">
								<strong>訂單 {{ $order['bill_id'] }}</strong>
								- {{ $order['user_name'] }}
								({{ $order['created_at'] }})

								@if(isset($order['batch_usage']) && count($order['batch_usage']) > 0)
									<table class="table table-bordered batch-usage-table">
										<thead>
											<tr>
												<th style="width: 30%;">原料</th>
												<th style="width: 30%;">批號</th>
												<th style="width: 40%;">使用數量</th>
											</tr>
										</thead>
										<tbody>
											@foreach($order['batch_usage'] as $slug => $batches)
												@foreach($batches as $batch)
													<tr>
														<td>{{ $batch['inventory_name'] }}</td>
														<td>{{ $batch['batch_number'] }}</td>
														<td><strong>{{ $batch['quantity_used'] }}</strong></td>
													</tr>
												@endforeach
											@endforeach
										</tbody>
									</table>
								@else
									<p style="font-size: 11px; color: #999; margin: 5px 0 0 0;">無批號使用記錄</p>
								@endif
							</div>
						@endforeach
					</div>
				@endif

				<!-- 停止訂單 -->
				@if(isset($data['stop_order']))
					<h5 style="color: #d9534f; font-weight: bold; margin-top: 20px;">
						<i class="glyphicon glyphicon-warning-sign"></i> 停止訂單
					</h5>
					<div style="padding: 10px; background-color: #fcf8e3; border-left: 3px solid #f0ad4e;">
						<p><strong>訂單編號：</strong> {{ $data['stop_order']['bill_id'] }}</p>
						<p><strong>客戶姓名：</strong> {{ $data['stop_order']['user_name'] }}</p>
						<p><strong>訂單日期：</strong> {{ $data['stop_order']['created_at'] }}</p>

						@if(isset($data['stop_reason']))
							<h6 style="color: #d9534f; font-weight: bold;">停止原因（原料不足）</h6>
							<ul>
								@foreach($data['stop_reason'] as $reason)
									<li>
										{{ $reason['name'] }}：
										需要 <strong>{{ $reason['required'] }}</strong>，
										剩餘 <strong>{{ $reason['available'] }}</strong>
										<span class="text-danger">(不足 {{ $reason['required'] - $reason['available'] }})</span>
									</li>
								@endforeach
							</ul>
						@endif
					</div>
				@endif

				<!-- 批號剩餘量 -->
				@if(isset($data['batch_remaining']))
					<h5 style="font-weight: bold; margin-top: 20px;">
						<i class="glyphicon glyphicon-th-list"></i> 批號預計剩餘量
					</h5>
					<table class="table table-bordered" style="font-size: 12px;">
						<thead>
							<tr style="background-color: #337ab7; color: white;">
								<th>原料</th>
								<th>批號</th>
								<th>原始數量</th>
								<th>剩餘數量</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data['batch_remaining'] as $batchId => $batch)
								<?php $used = $batch['original'] - $batch['available']; ?>
								<tr>
									<td>{{ $batch['inventory_name'] }}</td>
									<td>{{ $batch['batch_number'] }}</td>
									<td>{{ $batch['original'] }}</td>
									<td>
										{{ $batch['available'] }}
										<small class="text-muted">(已使用 {{ $used }})</small>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				@endif
			</div>
		@endforeach
	@endif

	<!-- 操作按鈕 -->
	@if($plan->status === 'in_progress')
		<div class="action-buttons">
			<button class="btn btn-success btn-lg" onclick="executePlan()">
				<i class="glyphicon glyphicon-play"></i> 執行出貨計劃
			</button>
			<p class="text-muted" style="margin-top: 10px;">
				<i class="glyphicon glyphicon-info-sign"></i>
				執行後將實際扣除庫存並更新訂單狀態為「已出貨」
			</p>
		</div>
	@elseif($plan->status === 'completed')
		<div class="alert alert-success text-center" style="margin-top: 30px;">
			<h4><i class="glyphicon glyphicon-ok-circle"></i> 此計劃已完成</h4>
			<p>庫存已扣除，訂單狀態已更新為「已出貨」</p>
		</div>
	@endif

</div>

@endsection

@section('scripts')
<script>
$(document).ready(function(){
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		}
	});
});

function executePlan() {
	if (!confirm('確定要執行此出貨計劃嗎？\n\n執行後將：\n1. 實際扣除批號庫存\n2. 更新所有訂單狀態為「已出貨」\n3. 標記計劃為「已完成」\n\n此操作無法復原！')) {
		return;
	}

	$.ajax({
		url: '{{ url("order/shipment-plan/" . $plan->id . "/complete") }}',
		method: 'POST',
		beforeSend: function() {
			$('.action-buttons').html('<div class="text-center"><i class="glyphicon glyphicon-refresh spinning"></i> 執行中，請稍候...</div>');
		},
		success: function(response) {
			if (response.success) {
				alert('出貨計劃已成功執行！\n\n' + response.message);
				location.reload();
			} else {
				alert('執行失敗：' + (response.message || '未知錯誤'));
				location.reload();
			}
		},
		error: function(xhr) {
			var errorMsg = '執行失敗，請稍後再試';
			if (xhr.responseJSON && xhr.responseJSON.message) {
				errorMsg = xhr.responseJSON.message;
			}
			alert(errorMsg);
			location.reload();
		}
	});
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
