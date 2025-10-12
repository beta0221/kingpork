@extends('admin_main')

@section('title','| 編輯批號')

@section('stylesheets')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .form-group label {
        font-weight: bold;
    }
    .required::after {
        content: ' *';
        color: red;
    }
    .info-badge {
        display: inline-block;
        padding: 5px 10px;
        background: #e9ecef;
        border-radius: 3px;
        margin-bottom: 15px;
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

    <div class="form-container">
        <h3 class="mb-4">編輯批號</h3>

        <div class="info-badge">
            <strong>批號 ID:</strong> {{ $inventoryBatch->id }} |
            <strong>建立時間:</strong> {{ $inventoryBatch->created_at->format('Y-m-d H:i') }}
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('inventoryBatch.update', $inventoryBatch->id) }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('PUT') }}

            <div class="form-group">
                <label for="inventory_id" class="required">選擇庫存</label>
                <select name="inventory_id"
                        id="inventory_id"
                        class="form-control {{ $errors->has('inventory_id') ? 'is-invalid' : '' }}"
                        required>
                    <option value="">-- 請選擇庫存 --</option>
                    @php
                        $currentCategory = null;
                    @endphp
                    @foreach($inventories as $inventory)
                        @if($currentCategory !== $inventory->category)
                            @if($currentCategory !== null)
                                </optgroup>
                            @endif
                            <optgroup label="{{ $inventory->category }}">
                            @php
                                $currentCategory = $inventory->category;
                            @endphp
                        @endif
                        <option value="{{ $inventory->id }}"
                                {{ (old('inventory_id', $inventoryBatch->inventory_id) == $inventory->id) ? 'selected' : '' }}>
                            {{ $inventory->name }} ({{ $inventory->slug }})
                        </option>
                    @endforeach
                    @if($currentCategory !== null)
                        </optgroup>
                    @endif
                </select>
                @if($errors->has('inventory_id'))
                    <div class="invalid-feedback">{{ $errors->first('inventory_id') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="batch_number" class="required">批號</label>
                <input type="text"
                       name="batch_number"
                       id="batch_number"
                       class="form-control {{ $errors->has('batch_number') ? 'is-invalid' : '' }}"
                       value="{{ old('batch_number', $inventoryBatch->batch_number) }}"
                       placeholder="例如: BATCH-2025-001"
                       required>
                @if($errors->has('batch_number'))
                    <div class="invalid-feedback">{{ $errors->first('batch_number') }}</div>
                @endif
                <small class="form-text text-muted">
                    建議格式: BATCH-年份-流水號
                </small>
            </div>

            <div class="form-group">
                <label for="quantity" class="required">數量</label>
                <input type="number"
                       name="quantity"
                       id="quantity"
                       class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}"
                       value="{{ old('quantity', $inventoryBatch->quantity) }}"
                       min="0"
                       required>
                @if($errors->has('quantity'))
                    <div class="invalid-feedback">{{ $errors->first('quantity') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="manufactured_date">生產日期</label>
                <input type="date"
                       name="manufactured_date"
                       id="manufactured_date"
                       class="form-control {{ $errors->has('manufactured_date') ? 'is-invalid' : '' }}"
                       value="{{ old('manufactured_date', $inventoryBatch->manufactured_date ? $inventoryBatch->manufactured_date->format('Y-m-d') : '') }}">
                @if($errors->has('manufactured_date'))
                    <div class="invalid-feedback">{{ $errors->first('manufactured_date') }}</div>
                @endif
            </div>

            <hr class="my-4">

            <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> 更新
                </button>
                <a href="{{ route('inventoryBatch.index') }}" class="btn btn-secondary">
                    取消
                </a>
                <a href="{{ route('inventoryBatch.show', $inventoryBatch->id) }}" class="btn btn-info">
                    查看詳細
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
