@extends('admin_main')

@section('title', '| ' . (isset($promotion) ? '編輯紅利活動' : '新增紅利活動'))

@section('content')

<div class="container-fluid mt-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>{{ isset($promotion) ? '編輯紅利活動' : '新增紅利活動' }}</h2>
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
                    <form action="{{ isset($promotion) ? route('admin.bonus-promotions.update', $promotion->id) : route('admin.bonus-promotions.store') }}"
                          method="POST">
                        {{ csrf_field() }}
                        @if(isset($promotion))
                            {{ method_field('PUT') }}
                        @endif

                        <div class="form-group">
                            <label for="name">活動名稱 <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', isset($promotion) ? $promotion->name : '') }}"
                                   placeholder="例如：母親節紅利雙倍送"
                                   required>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="multiplier">紅利倍數 <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control {{ $errors->has('multiplier') ? 'is-invalid' : '' }}"
                                   id="multiplier"
                                   name="multiplier"
                                   step="0.1"
                                   min="1"
                                   max="999"
                                   value="{{ old('multiplier', isset($promotion) ? $promotion->multiplier : '2') }}"
                                   placeholder="例如：2 代表紅利雙倍，1.5 代表紅利 1.5 倍"
                                   required>
                            @if($errors->has('multiplier'))
                                <div class="invalid-feedback">{{ $errors->first('multiplier') }}</div>
                            @endif
                            <small class="form-text text-muted">
                                輸入倍數（例如：1.5、2、3）。1 代表原本的紅利，2 代表雙倍紅利。
                            </small>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="start_time">開始時間 <span class="text-danger">*</span></label>
                                <input type="datetime-local"
                                       class="form-control {{ $errors->has('start_time') ? 'is-invalid' : '' }}"
                                       id="start_time"
                                       name="start_time"
                                       value="{{ old('start_time', isset($promotion) ? $promotion->start_time->format('Y-m-d\TH:i') : '') }}"
                                       required>
                                @if($errors->has('start_time'))
                                    <div class="invalid-feedback">{{ $errors->first('start_time') }}</div>
                                @endif
                            </div>

                            <div class="form-group col-md-6">
                                <label for="end_time">結束時間 <span class="text-danger">*</span></label>
                                <input type="datetime-local"
                                       class="form-control {{ $errors->has('end_time') ? 'is-invalid' : '' }}"
                                       id="end_time"
                                       name="end_time"
                                       value="{{ old('end_time', isset($promotion) ? $promotion->end_time->format('Y-m-d\TH:i') : '') }}"
                                       required>
                                @if($errors->has('end_time'))
                                    <div class="invalid-feedback">{{ $errors->first('end_time') }}</div>
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
                                       {{ old('is_active', isset($promotion) ? $promotion->is_active : true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    啟用此活動
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                停用的活動即使在活動時間內也不會生效。
                            </small>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <span class="glyphicon glyphicon-floppy-disk"></span>
                                {{ isset($promotion) ? '更新活動' : '新增活動' }}
                            </button>
                            <a href="{{ route('admin.bonus-promotions.index') }}" class="btn btn-default">
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
                    <h6>紅利倍數活動</h6>
                    <p class="small">
                        此功能允許您在特定時間內為用戶提供額外的紅利點數獎勵。
                    </p>

                    <h6 class="mt-3">倍數設定範例</h6>
                    <ul class="small">
                        <li><strong>2</strong> = 紅利雙倍送</li>
                        <li><strong>1.5</strong> = 紅利 1.5 倍</li>
                        <li><strong>3</strong> = 紅利三倍送</li>
                    </ul>

                    <h6 class="mt-3">注意事項</h6>
                    <ul class="small">
                        <li>活動時間內的訂單會自動套用倍數</li>
                        <li>多個活動同時進行時，系統會採用最高倍數</li>
                        <li>停用的活動不會生效</li>
                        <li>已結帳的訂單不會追溯調整</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
