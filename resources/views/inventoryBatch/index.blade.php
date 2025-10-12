@extends('admin_main')

@section('title','| 批號管理')

@section('stylesheets')
<style>
    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    .batch-table {
        width: 100%;
    }
    .batch-table th {
        background: #007bff;
        color: white;
        padding: 10px;
    }
    .batch-table td {
        padding: 8px;
        border-bottom: 1px solid #dee2e6;
    }
    .batch-table tr:hover {
        background: #f8f9fa;
    }
    .alert {
        margin-top: 15px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>批號管理</h3>
        <a href="{{ route('inventoryBatch.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增批號
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- 篩選區域 -->
    <div class="filter-section">
        <form action="{{ route('inventoryBatch.index') }}" method="GET" class="form-inline">
            <label class="mr-2">篩選庫存:</label>
            <select name="inventory_id" class="form-control mr-2" style="width: 250px;">
                <option value="">-- 全部庫存 --</option>
                @foreach($inventories as $inventory)
                    <option value="{{ $inventory->id }}"
                        {{ request('inventory_id') == $inventory->id ? 'selected' : '' }}>
                        {{ $inventory->category }} - {{ $inventory->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-info">篩選</button>
            <a href="{{ route('inventoryBatch.index') }}" class="btn btn-sm btn-secondary ml-2">清除</a>
        </form>
    </div>

    <!-- 批號列表 -->
    <div class="table-responsive">
        <table class="batch-table table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>庫存類別</th>
                    <th>庫存名稱</th>
                    <th>批號</th>
                    <th>數量</th>
                    <th>生產日期</th>
                    <th>建立時間</th>
                    <th style="width: 200px;">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $batch)
                    <tr>
                        <td>{{ $batch->id }}</td>
                        <td>
                            <span class="badge badge-secondary">
                                {{ $batch->inventory->category }}
                            </span>
                        </td>
                        <td>{{ $batch->inventory->name }}</td>
                        <td><strong>{{ $batch->batch_number }}</strong></td>
                        <td>
                            <span class="badge badge-{{ $batch->quantity > 0 ? 'success' : 'warning' }}">
                                {{ $batch->quantity }}
                            </span>
                        </td>
                        <td>{{ $batch->manufactured_date ? $batch->manufactured_date : '-' }}</td>
                        <td>{{ $batch->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('inventoryBatch.show', $batch->id) }}"
                               class="btn btn-sm btn-info">
                                查看
                            </a>
                            <a href="{{ route('inventoryBatch.edit', $batch->id) }}"
                               class="btn btn-sm btn-warning">
                                編輯
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    onclick="confirmDelete({{ $batch->id }})">
                                刪除
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <p class="py-4 mb-0">尚無批號資料</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3 text-muted">
        共 {{ $batches->count() }} 筆批號資料
    </div>
</div>

<!-- 刪除確認表單 -->
<form id="delete-form" method="POST" style="display: none;">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
</form>

@endsection

@section('scripts')
<script>
function confirmDelete(id) {
    if (confirm('確定要刪除此批號嗎？')) {
        let form = document.getElementById('delete-form');
        form.action = '/inventoryBatch/' + id;
        form.submit();
    }
}

// 自動關閉提示訊息
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 3000);
</script>
@endsection
