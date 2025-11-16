@extends('admin_main')

@section('title', '| ' . (isset($promotionalLink) ? '編輯優惠連結' : '新增優惠連結'))

@section('content')

<div class="container-fluid mt-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>{{ isset($promotionalLink) ? '編輯優惠連結' : '新增優惠連結' }}</h2>
        </div>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-3">
                    <form action="{{ isset($promotionalLink) ? route('admin.promotional-links.update', $promotionalLink->id) : route('admin.promotional-links.store') }}"
                          method="POST">
                        {{ csrf_field() }}
                        @if(isset($promotionalLink))
                            {{ method_field('PUT') }}
                        @endif

                        <div class="form-group">
                            <label for="code">優惠碼 <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}"
                                   id="code"
                                   name="code"
                                   value="{{ old('code', isset($promotionalLink) ? $promotionalLink->code : '') }}"
                                   placeholder="例如：DOUBLE11"
                                   style="text-transform: uppercase;"
                                   required>
                            @if($errors->has('code'))
                                <div class="invalid-feedback">{{ $errors->first('code') }}</div>
                            @endif
                            <small class="form-text text-muted">
                                只能包含大寫英文字母和數字（系統會自動轉換為大寫）
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="name">活動名稱 <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', isset($promotionalLink) ? $promotionalLink->name : '') }}"
                                   placeholder="例如：雙11購物節"
                                   required>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="discount_percentage">折扣百分比 <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control {{ $errors->has('discount_percentage') ? 'is-invalid' : '' }}"
                                       id="discount_percentage"
                                       name="discount_percentage"
                                       step="0.01"
                                       min="0.01"
                                       max="100"
                                       value="{{ old('discount_percentage', isset($promotionalLink) ? $promotionalLink->discount_percentage : '10') }}"
                                       placeholder="10"
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">% OFF</span>
                                </div>
                                @if($errors->has('discount_percentage'))
                                    <div class="invalid-feedback">{{ $errors->first('discount_percentage') }}</div>
                                @endif
                            </div>
                            <small class="form-text text-muted">
                                輸入 10 代表打 9 折（折扣 10%），輸入 20 代表打 8 折（折扣 20%）
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="applicable_categories">適用商品類別</label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                @foreach($categories as $category)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class=""
                                           id="category_{{ $category->id }}"
                                           name="applicable_categories[]"
                                           value="{{ $category->id }}"
                                           {{ (isset($promotionalLink) && in_array($category->id, $promotionalLink->applicable_categories ?? [])) || (old('applicable_categories') && in_array($category->id, old('applicable_categories'))) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="category_{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">
                                若不勾選任何類別，則此優惠適用於所有商品
                            </small>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="start_date">開始日期 <span class="text-danger">*</span></label>
                                <input type="datetime-local"
                                       class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}"
                                       id="start_date"
                                       name="start_date"
                                       value="{{ old('start_date', isset($promotionalLink) ? $promotionalLink->start_date->format('Y-m-d\TH:i') : '') }}"
                                       required>
                                @if($errors->has('start_date'))
                                    <div class="invalid-feedback">{{ $errors->first('start_date') }}</div>
                                @endif
                            </div>

                            <div class="form-group col-md-6">
                                <label for="end_date">結束日期 <span class="text-danger">*</span></label>
                                <input type="datetime-local"
                                       class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}"
                                       id="end_date"
                                       name="end_date"
                                       value="{{ old('end_date', isset($promotionalLink) ? $promotionalLink->end_date->format('Y-m-d\TH:i') : '') }}"
                                       required>
                                @if($errors->has('end_date'))
                                    <div class="invalid-feedback">{{ $errors->first('end_date') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', isset($promotionalLink) ? $promotionalLink->is_active : true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    啟用此優惠連結
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                停用的優惠連結即使在活動時間內也不會生效
                            </small>
                        </div>

                        @if(isset($promotionalLink))
                        <div class="alert alert-info">
                            <strong>優惠連結：</strong>
                            <code>{{ url('/promo/' . $promotionalLink->code) }}</code>
                            <button type="button" class="btn btn-sm btn-info ml-2" onclick="copyToClipboard('{{ url('/promo/' . $promotionalLink->code) }}')">
                                複製
                            </button>
                        </div>
                        @endif

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <span class="glyphicon glyphicon-floppy-disk"></span>
                                {{ isset($promotionalLink) ? '更新優惠連結' : '新增優惠連結' }}
                            </button>
                            <a href="{{ route('admin.promotional-links.index') }}" class="btn btn-default">
                                <span class="glyphicon glyphicon-remove"></span>
                                取消
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">說明</h5>
                </div>
                <div class="card-body p-3">
                    <h6>優惠連結功能</h6>
                    <p class="small">
                        建立專屬的優惠連結，客戶點擊連結後購物即可享有折扣。
                    </p>

                    <h6 class="mt-3">使用方式</h6>
                    <ul class="small">
                        <li>建立優惠連結後，系統會生成專屬 URL</li>
                        <li>格式：<code>yoursite.com/promo/CODE</code></li>
                        <li>客戶點擊連結後，折扣會自動套用</li>
                        <li>結帳時會顯示優惠折扣金額</li>
                    </ul>

                    <h6 class="mt-3">折扣計算範例</h6>
                    <ul class="small">
                        <li><strong>10%</strong> = 1000 元打 9 折 = 900 元</li>
                        <li><strong>15%</strong> = 1000 元打 85 折 = 850 元</li>
                        <li><strong>20%</strong> = 1000 元打 8 折 = 800 元</li>
                    </ul>

                    <h6 class="mt-3">適用商品類別</h6>
                    <p class="small">
                        您可以限制此優惠只適用於特定商品類別。未選擇任何類別時，優惠將適用於全部商品。
                    </p>

                    <h6 class="mt-3">注意事項</h6>
                    <ul class="small">
                        <li>優惠連結可與紅利點數折抵併用</li>
                        <li>折扣先套用，再扣除紅利點數</li>
                        <li>優惠碼必須唯一，不可重複</li>
                        <li>已過期的連結將自動失效</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 自動轉換優惠碼為大寫
document.getElementById('code').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
});

// 複製連結功能
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            alert('連結已複製: ' + text);
        }).catch(err => {
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        alert('連結已複製: ' + text);
    } catch (err) {
        alert('無法複製連結，請手動複製: ' + text);
    }
    document.body.removeChild(textarea);
}
</script>

@endsection
