@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">結帳流程漏斗分析</h1>

    <!-- 日期篩選 -->
    <div class="card mb-4">
        <div class="card-body">
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
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">總體轉換率</h5>
                    <h2>{{ $totalStats['overall_conversion_rate'] }}%</h2>
                    <small>從購物車到完成的比率</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">購物車放棄率</h5>
                    <h2>{{ $totalStats['cart_abandonment_rate'] }}%</h2>
                    <small>查看購物車但未結帳</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">付款放棄率</h5>
                    <h2>{{ $totalStats['payment_abandonment_rate'] }}%</h2>
                    <small>建立訂單但未付款</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
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
            <h4>流程漏斗圖</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>步驟</th>
                        <th>人數</th>
                        <th>轉換率</th>
                        <th>流失率</th>
                        <th>視覺化</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($funnelData as $stepKey => $data)
                    <tr>
                        <td>{{ $data['name'] }}</td>
                        <td>{{ number_format($data['count']) }}</td>
                        <td>
                            <span class="badge badge-success">{{ $data['conversion_rate'] }}%</span>
                        </td>
                        <td>
                            @if($data['drop_rate'] > 0)
                                <span class="badge badge-danger">-{{ $data['drop_rate'] }}%</span>
                            @else
                                <span class="badge badge-secondary">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ $data['conversion_rate'] }}%;"
                                     aria-valuenow="{{ $data['conversion_rate'] }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    {{ $data['conversion_rate'] }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
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
            @php
                $groupedByPayment = $paymentMethodData->groupBy('payment_method');
            @endphp

            @if($groupedByPayment->count() > 0)
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>付款方式</th>
                        @foreach(\App\CheckoutFunnelLog::getAllSteps() as $key => $name)
                            <th class="text-center">{{ $name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedByPayment as $paymentMethod => $steps)
                    <tr>
                        <td><strong>{{ $paymentMethod }}</strong></td>
                        @foreach(\App\CheckoutFunnelLog::getAllSteps() as $key => $name)
                            @php
                                $stepData = $steps->where('step', $key)->first();
                                $count = $stepData ? $stepData->count : 0;
                            @endphp
                            <td class="text-center">{{ $count }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
