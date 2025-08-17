@extends('main')

@section('title','| 編輯信用卡')

@section('stylesheets')
<style>
.card-form {
    max-width: 500px;
    margin: 0 auto;
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.form-group label {
    font-weight: 600;
    color: #333;
}

.form-control {
    border-radius: 5px;
    border: 1px solid #ddd;
    padding: 12px;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.btn-block {
    padding: 12px;
    font-weight: 600;
}

.expiry-row {
    display: flex;
    gap: 15px;
}

.expiry-row .form-group {
    flex: 1;
}

.card-info {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.card-number-display {
    font-size: 18px;
    letter-spacing: 2px;
    color: #495057;
    margin-bottom: 5px;
}
</style>
@endsection

@section('content')
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <div class="card-form">
                <h3 class="text-center mb-4">編輯信用卡</h3>

                <div class="card-info">
                    <h6>目前信用卡資訊</h6>
                    <div class="card-number-display">{{ $creditCard->masked_card_number }}</div>
                    <div><strong>品牌：</strong>{{ $creditCard->card_brand }}</div>
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

                <form method="POST" action="{{ route('creditCard.update', $creditCard->id) }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="form-group">
                        <label for="card_alias">卡片別名 *</label>
                        <input type="text" class="form-control" id="card_alias" name="card_alias" 
                               value="{{ old('card_alias', $creditCard->card_alias) }}" placeholder="例如：我的主要信用卡" required>
                        <small class="form-text text-muted">為這張卡片取一個容易記住的名稱</small>
                    </div>

                    <div class="form-group">
                        <label for="card_holder_name">持卡人姓名 *</label>
                        <input type="text" class="form-control" id="card_holder_name" name="card_holder_name" 
                               value="{{ old('card_holder_name', $creditCard->card_holder_name) }}" placeholder="請輸入持卡人姓名" required>
                    </div>

                    <div class="expiry-row">
                        <div class="form-group">
                            <label for="expiry_month">到期月份 *</label>
                            <select class="form-control" id="expiry_month" name="expiry_month" required>
                                <option value="">選擇月份</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" 
                                        {{ old('expiry_month', $creditCard->expiry_month) == $i ? 'selected' : '' }}>
                                        {{ sprintf('%02d', $i) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="expiry_year">到期年份 *</label>
                            <select class="form-control" id="expiry_year" name="expiry_year" required>
                                <option value="">選擇年份</option>
                                @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                    <option value="{{ $i }}" 
                                        {{ old('expiry_year', $creditCard->expiry_year) == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1"
                                   {{ old('is_default', $creditCard->is_default) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">
                                設為預設信用卡
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">更新信用卡</button>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('creditCard.index') }}" class="btn btn-link">返回信用卡管理</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection