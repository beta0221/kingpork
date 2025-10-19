@extends('admin_main')

@section('title','| 出貨計劃列表')

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
	.status-badge{
		padding: 4px 8px;
		border-radius: 3px;
		color: white;
		font-size: 12px;
		font-weight: bold;
	}
	.status-pending{ background-color: #f0ad4e; }
	.status-in_progress{ background-color: #5bc0de; }
	.status-completed{ background-color: #5cb85c; }
</style>
@endsection

@section('content')

<div class="container-fluid">

	<!-- 頁首 -->
	<div class="filter-box">
		<h3 style="margin: 0; display: inline-block;">
			<i class="glyphicon glyphicon-list"></i> 出貨計劃列表
		</h3>
		<a href="{{ url('order/materials') }}" class="btn btn-primary pull-right">
			<i class="glyphicon glyphicon-plus"></i> 建立新計劃
		</a>
	</div>

	<!-- 計劃列表 -->
	@if($plans->isEmpty())
		<div class="alert alert-info text-center" style="margin-top: 50px;">
			<h4>目前沒有出貨計劃</h4>
			<p>請前往<a href="{{ url('order/materials') }}">訂單原料需求</a>頁面建立新的出貨計劃</p>
		</div>
	@else
		<table class="table table-bordered">
			<thead>
				<tr style="background-color: #337ab7; color: white;">
					<th style="width: 60px;">#</th>
					<th>計劃名稱</th>
					<th style="width: 120px;">狀態</th>
					<th style="width: 100px;">訂單數</th>
					<th style="width: 100px;">階段數</th>
					<th style="width: 180px;">建立時間</th>
					<th style="width: 150px;">操作</th>
				</tr>
			</thead>
			<tbody>
				@foreach($plans as $index => $plan)
					<tr class="table-tr">
						<td>{{ $index + 1 }}</td>
						<td style="text-align: left; padding-left: 15px;">
							<strong>{{ $plan->plan_name }}</strong>
						</td>
						<td>
							<span class="status-badge status-{{ $plan->status }}">
								@if($plan->status === 'pending')
									待處理
								@elseif($plan->status === 'in_progress')
									進行中
								@else
									已完成
								@endif
							</span>
						</td>
						<td>{{ $plan->total_orders }} 筆</td>
						<td>{{ $plan->total_stages }} 階段</td>
						<td>{{ $plan->created_at }}</td>
						<td>
							<a href="{{ url('order/shipment-plan/' . $plan->id) }}" class="btn btn-sm btn-info">
								<i class="glyphicon glyphicon-eye-open"></i> 檢視
							</a>
							@if($plan->status !== 'completed')
								<button class="btn btn-sm btn-danger" onclick="deletePlan({{ $plan->id }})">
									<i class="glyphicon glyphicon-trash"></i> 刪除
								</button>
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<!-- 分頁 -->
		<div style="text-align: center; margin-top: 20px;">
			{{ $plans->links() }}
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

function deletePlan(planId) {
	if (!confirm('確定要刪除此出貨計劃嗎？此操作無法復原。')) {
		return;
	}

	$.ajax({
		url: '{{ url("order/shipment-plan") }}/' + planId,
		method: 'DELETE',
		beforeSend: function() {
			// 禁用刪除按鈕
			event.target.disabled = true;
			event.target.innerHTML = '<i class="glyphicon glyphicon-refresh"></i> 刪除中...';
		},
		success: function(response) {
			if (response.success) {
				alert('出貨計劃已成功刪除');
				location.reload();
			} else {
				alert('刪除失敗：' + (response.message || '未知錯誤'));
				event.target.disabled = false;
				event.target.innerHTML = '<i class="glyphicon glyphicon-trash"></i> 刪除';
			}
		},
		error: function(xhr) {
			var errorMsg = '刪除失敗，請稍後再試';
			if (xhr.responseJSON && xhr.responseJSON.message) {
				errorMsg = xhr.responseJSON.message;
			}
			alert(errorMsg);
			event.target.disabled = false;
			event.target.innerHTML = '<i class="glyphicon glyphicon-trash"></i> 刪除';
		}
	});
}
</script>
@endsection
