@extends('main')

@section('title','| 信用卡管理')

@section('stylesheets')
<style>
.credit-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    padding: 20px;
    color: white;
    margin-bottom: 20px;
    position: relative;
    min-height: 120px;
}

.credit-card.visa {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
}

.credit-card.mastercard {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
}

.credit-card.jcb {
    background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
}

.card-number {
    font-size: 18px;
    letter-spacing: 2px;
    margin: 10px 0;
}

.card-holder {
    font-size: 14px;
    margin-top: 15px;
}

.card-brand {
    position: absolute;
    top: 15px;
    right: 20px;
    font-weight: bold;
    font-size: 16px;
}

.default-badge {
    background-color: #28a745;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    position: absolute;
    top: 15px;
    left: 20px;
}

.card-actions {
    margin-top: 15px;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
    margin-right: 5px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    color: #dee2e6;
}
</style>
@endsection

@section('content')
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>信用卡管理</h2>
                <a href="{{ route('creditCard.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> 新增信用卡
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @if($creditCards->isEmpty())
                <div class="empty-state mt-4 mb-4">
                    <i class="fa fa-credit-card"></i>
                    <h4>尚未新增任何信用卡</h4>
                    <p>新增信用卡可以讓您下次結帳更快速便利</p>
                    <a href="{{ route('creditCard.create') }}" class="btn btn-primary">新增第一張信用卡</a>
                </div>
            @else
                <div class="row">
                    @foreach($creditCards as $card)
                        <div class="col-md-6 col-lg-4">
                            <div class="credit-card {{ strtolower($card->card_brand) }}">
                                @if($card->is_default)
                                    <span class="default-badge">預設</span>
                                @endif
                                <div class="card-brand">{{ $card->card_brand }}</div>
                                
                                <div class="card-number">{{ $card->masked_card_number }}</div>
                                
                                <div class="card-holder">
                                    <div>{{ $card->card_holder_name }}</div>
                                    <div style="font-size: 12px; opacity: 0.8;">{{ $card->card_alias }}</div>
                                </div>

                                <div class="card-actions">
                                    @if(!$card->is_default)
                                        <form method="POST" action="{{ route('creditCard.setDefault', $card->id) }}" style="display: inline;">
                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-success btn-sm">設為預設</button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('creditCard.edit', $card->id) }}" class="btn btn-warning btn-sm">編輯</a>
                                    
                                    <form method="POST" action="{{ route('creditCard.destroy', $card->id) }}" style="display: inline;" 
                                          onsubmit="return confirm('確定要刪除這張信用卡嗎？')">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm">刪除</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-secondary">返回會員中心</a>
            </div>
        </div>
    </div>
</div>
@endsection