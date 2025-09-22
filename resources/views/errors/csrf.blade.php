@extends('main')

@section('title','| 安全驗證過期')

@section('stylesheets')
<style>
    .error-container {
        text-align: center;
        padding: 60px 20px;
        max-width: 600px;
        margin: 0 auto;
        background: rgba(255,255,255,0.9);
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin-top: 50px;
    }
    
    .error-icon {
        font-size: 72px;
        color: #f39c12;
        margin-bottom: 20px;
    }
    
    .error-title {
        font-size: 28px;
        color: #333;
        margin-bottom: 15px;
        font-weight: bold;
    }
    
    .error-message {
        font-size: 16px;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }
    
    .error-actions {
        margin-top: 30px;
    }
    
    .btn-primary {
        background-color: #3498db;
        border-color: #3498db;
        padding: 12px 30px;
        font-size: 16px;
        margin: 0 10px;
    }
    
    .btn-secondary {
        background-color: #95a5a6;
        border-color: #95a5a6;
        padding: 12px 30px;
        font-size: 16px;
        margin: 0 10px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="error-container">
        <div class="error-icon">
            ⚠️
        </div>
        
        <h1 class="error-title">安全驗證已過期</h1>
        
        <div class="error-message">
            <p>很抱歉，您的安全驗證憑證已過期。這通常發生在以下情況：</p>
            <ul style="text-align: left; display: inline-block; margin-top: 15px;">
                <li>頁面開啟時間過長（超過30分鐘）</li>
                <li>瀏覽器會話已過期</li>
                <li>網路連線中斷</li>
            </ul>
        </div>
        
        <div class="error-actions">
            <button class="btn btn-primary" onclick="window.history.back()">
                返回上一頁
            </button>
            <button class="btn btn-secondary" onclick="location.reload()">
                重新載入頁面
            </button>
            <a href="{{ route('kart.index') }}" class="btn btn-primary">
                回到購物車
            </a>
        </div>
        
        <div style="margin-top: 30px; font-size: 14px; color: #999;">
            <p>如果問題持續發生，請清除瀏覽器快取或聯繫客服。</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // 5秒後自動重新導向到購物車
    setTimeout(function() {
        if (confirm('是否要自動返回購物車頁面？')) {
            window.location.href = '{{ route("kart.index") }}';
        }
    }, 5000);
</script>
@endsection