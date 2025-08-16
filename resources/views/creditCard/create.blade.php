@extends('main')

@section('title','| 新增信用卡')

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

.security-notice {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.security-notice h6 {
    color: #1976d2;
    margin-bottom: 10px;
}

.security-notice p {
    color: #424242;
    margin-bottom: 0;
    font-size: 14px;
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
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card-form">
                <h3 class="text-center mb-4">新增信用卡</h3>

                <div class="security-notice">
                    <h6><i class="fa fa-shield"></i> 安全說明</h6>
                    <p>我們不會儲存您的完整卡號或安全碼，僅保留卡號前六後四碼用於識別。實際付款將透過綠界科技安全處理。</p>
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

                <form method="POST" action="{{ route('creditCard.store') }}">
                    {{ csrf_field() }}
                    
                    <div class="form-group">
                        <label for="card_alias">卡片別名 *</label>
                        <input type="text" class="form-control" id="card_alias" name="card_alias" 
                               value="{{ old('card_alias') }}" placeholder="例如：我的主要信用卡" required>
                        <small class="form-text text-muted">為這張卡片取一個容易記住的名稱</small>
                    </div>

                    <div class="form-group">
                        <label for="card_number">信用卡號 *</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" 
                               value="{{ old('card_number') }}" placeholder="1234 5678 9012 3456" 
                               maxlength="19" required>
                        <small class="form-text text-muted">僅用於驗證，不會儲存完整卡號</small>
                    </div>

                    <div class="form-group">
                        <label for="card_holder_name">持卡人姓名 *</label>
                        <input type="text" class="form-control" id="card_holder_name" name="card_holder_name" 
                               value="{{ old('card_holder_name') }}" placeholder="請輸入持卡人姓名" required>
                    </div>

                    <div class="expiry-row">
                        <div class="form-group">
                            <label for="expiry_month">到期月份 *</label>
                            <select class="form-control" id="expiry_month" name="expiry_month" required>
                                <option value="">選擇月份</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('expiry_month') == $i ? 'selected' : '' }}>
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
                                    <option value="{{ $i }}" {{ old('expiry_year') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="card_brand">卡片品牌 *</label>
                        <select class="form-control" id="card_brand" name="card_brand" required>
                            <option value="">選擇卡片品牌</option>
                            <option value="VISA" {{ old('card_brand') == 'VISA' ? 'selected' : '' }}>VISA</option>
                            <option value="MASTERCARD" {{ old('card_brand') == 'MASTERCARD' ? 'selected' : '' }}>MasterCard</option>
                            <option value="JCB" {{ old('card_brand') == 'JCB' ? 'selected' : '' }}>JCB</option>
                            <option value="UNIONPAY" {{ old('card_brand') == 'UNIONPAY' ? 'selected' : '' }}>銀聯</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1"
                                   {{ old('is_default') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">
                                設為預設信用卡
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">新增信用卡</button>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('creditCard.index') }}" class="btn btn-link">返回信用卡管理</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// 格式化信用卡號碼輸入
document.getElementById('card_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    e.target.value = formattedValue;
});
</script>
@endsection