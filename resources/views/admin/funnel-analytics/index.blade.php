@extends('admin_main')

@section('title','| 結帳流程漏斗分析')

@section('content')
<div class="container-fluid">
    <!-- 日期篩選 -->
    <div class="card mt-4 mb-4">
        <div class="card-body p-2">
            <form method="GET" action="{{ url('/admin/funnel-analytics') }}" class="form-inline">
                <label class="mr-2">日期範圍:</label>
                <input type="date" name="start_date" class="form-control mr-2" value="{{ $startDate }}">
                <span class="mr-2">至</span>
                <input type="date" name="end_date" class="form-control mr-2" value="{{ $endDate }}">
                <button type="submit" class="btn btn-primary">查詢</button>
                <a href="{{ url('/admin/funnel-analytics/export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-success ml-2">匯出 CSV</a>
            </form>
        </div>
    </div>

    <!-- 總體統計卡片 -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary p-2">
                <div class="card-body">
                    <h5 class="card-title">總體轉換率</h5>
                    <h2>{{ $totalStats['overall_conversion_rate'] }}%</h2>
                    <small>從購物車到完成的比率</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info p-2">
                <div class="card-body">
                    <h5 class="card-title">購物車放棄率</h5>
                    <h2>{{ $totalStats['cart_abandonment_rate'] }}%</h2>
                    <small>查看購物車但未結帳</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning p-2">
                <div class="card-body">
                    <h5 class="card-title">付款放棄率</h5>
                    <h2>{{ $totalStats['payment_abandonment_rate'] }}%</h2>
                    <small>建立訂單但未付款</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success p-2">
                <div class="card-body">
                    <h5 class="card-title">訂單付款成功率</h5>
                    <h2>{{ $totalStats['order_to_payment_rate'] }}%</h2>
                    <small>訂單成功付款比率</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 漏斗圖表 -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>總數量</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        @foreach($funnelData as $stepKey => $data)
                            <th class="text-center">{{ $data['name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    @foreach($funnelData as $stepKey => $data)
                        <td class="text-center">{{ number_format($data['count']) }}</td>
                    @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 錯誤分析 -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>錯誤分析 (Top 10)</h4>
        </div>
        <div class="card-body">
            @if($errorData->count() > 0)
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>步驟</th>
                        <th>錯誤訊息</th>
                        <th>發生次數</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($errorData->take(10) as $error)
                    <tr>
                        <td><span class="badge badge-warning">{{ $error->step }}</span></td>
                        <td>{{ $error->error_message }}</td>
                        <td><strong>{{ $error->count }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted">目前沒有錯誤記錄</p>
            @endif
        </div>
    </div>

    <!-- 依付款方式分析 -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>依付款方式分析</h4>
        </div>
        <div class="card-body">
            @if(count($paymentMethodData) > 0)
                @foreach($paymentMethodData as $paymentMethod => $methodData)
                <div class="mb-4">
                    <h5>{{ $methodData['name'] }}
                        <small class="text-muted">(總數: {{ $methodData['total_sessions'] }} sessions)</small>
                    </h5>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>步驟</th>
                                <th class="text-center">數量</th>
                                <th class="text-center">轉換率</th>
                                <th class="text-center">流失率</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($methodData['steps'] as $step)
                            <tr>
                                <td>{{ $step['name'] }}</td>
                                <td class="text-center">{{ $step['count'] }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $step['conversion_rate'] >= 90 ? 'success' : ($step['conversion_rate'] >= 70 ? 'warning' : 'danger') }}">
                                        {{ $step['conversion_rate'] }}%
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $step['drop_off_rate'] <= 10 ? 'success' : ($step['drop_off_rate'] <= 30 ? 'warning' : 'danger') }}">
                                        {{ $step['drop_off_rate'] }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endforeach
            @else
            <p class="text-muted">目前沒有數據</p>
            @endif
        </div>
    </div>

    <!-- 每日趨勢 -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>每日趨勢</h4>
        </div>
        <div class="card-body">
            @if(count($dailyTrend) > 0)
            <div style="overflow-x: auto;">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>日期</th>
                            <th>購物車</th>
                            <th>開始結帳</th>
                            <th>訂單建立</th>
                            <th>付款完成</th>
                            <th>感謝頁面</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyTrend as $date => $steps)
                        <tr>
                            <td>{{ $date }}</td>
                            <td>{{ $steps['cart_view'] ?? 0 }}</td>
                            <td>{{ $steps['checkout_start'] ?? 0 }}</td>
                            <td>{{ $steps['order_created'] ?? 0 }}</td>
                            <td>{{ $steps['payment_completed'] ?? 0 }}</td>
                            <td>{{ $steps['thankyou_page_view'] ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">目前沒有數據</p>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.progress {
    background-color: #e9ecef;
}
</style>
@endsection
