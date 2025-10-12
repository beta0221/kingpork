@extends('admin_main')

@section('title','| 批號詳細資料')

@section('stylesheets')
<style>
    .detail-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .detail-row {
        display: flex;
        padding: 15px 0;
        border-bottom: 1px solid #e9ecef;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-weight: bold;
        width: 180px;
        color: #495057;
    }
    .detail-value {
        flex: 1;
        color: #212529;
    }
    .badge-lg {
        font-size: 1.1em;
        padding: 8px 15px;
    }
    .action-buttons {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #e9ecef;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-4">
    <div class="mb-3">
        <a href="{{ route('inventoryBatch.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>

    <div class="detail-container">
        <h3 class="mb-4">批號詳細資料</h3>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="detail-row">
            <div class="detail-label">批號 ID:</div>
            <div class="detail-value">
                <strong>{{ $inventoryBatch->id }}</strong>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">庫存類別:</div>
            <div class="detail-value">
                <span class="badge badge-secondary badge-lg">
                    {{ $inventoryBatch->inventory->category }}
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">庫存名稱:</div>
            <div class="detail-value">
                <strong>{{ $inventoryBatch->inventory->name }}</strong>
                <small class="text-muted ml-2">({{ $inventoryBatch->inventory->slug }})</small>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">批號:</div>
            <div class="detail-value">
                <span class="badge badge-primary badge-lg">
                    {{ $inventoryBatch->batch_number }}
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">數量:</div>
            <div class="detail-value">
                <span class="badge badge-{{ $inventoryBatch->quantity > 0 ? 'success' : 'warning' }} badge-lg">
                    {{ $inventoryBatch->quantity }}
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">生產日期:</div>
            <div class="detail-value">
                @if($inventoryBatch->manufactured_date)
                    {{ $inventoryBatch->manufactured_date->format('Y年m月d日') }}
                    <small class="text-muted">
                        ({{ $inventoryBatch->manufactured_date->diffForHumans() }})
                    </small>
                @else
                    <span class="text-muted">未設定</span>
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">建立時間:</div>
            <div class="detail-value">
                {{ $inventoryBatch->created_at->format('Y-m-d H:i:s') }}
                <small class="text-muted">
                    ({{ $inventoryBatch->created_at->diffForHumans() }})
                </small>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">最後更新:</div>
            <div class="detail-value">
                {{ $inventoryBatch->updated_at->format('Y-m-d H:i:s') }}
                <small class="text-muted">
                    ({{ $inventoryBatch->updated_at->diffForHumans() }})
                </small>
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('inventoryBatch.edit', $inventoryBatch->id) }}"
               class="btn btn-warning">
                <i class="fa fa-edit"></i> 編輯
            </a>

            <button type="button"
                    class="btn btn-danger"
                    onclick="confirmDelete()">
                <i class="fa fa-trash"></i> 刪除
            </button>

            <a href="{{ route('inventoryBatch.index') }}"
               class="btn btn-secondary">
                返回列表
            </a>
        </div>
    </div>
</div>

<!-- 刪除確認表單 -->
<form id="delete-form"
      action="{{ route('inventoryBatch.destroy', $inventoryBatch->id) }}"
      method="POST"
      style="display: none;">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
</form>

@endsection

@section('scripts')
<script>
function confirmDelete() {
    if (confirm('確定要刪除此批號嗎？刪除後將無法復原。')) {
        document.getElementById('delete-form').submit();
    }
}

// 自動關閉提示訊息
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 3000);
</script>
@endsection
