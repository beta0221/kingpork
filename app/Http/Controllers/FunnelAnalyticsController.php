<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CheckoutFunnelLog;
use Carbon\Carbon;
use DB;

class FunnelAnalyticsController extends Controller
{
    /**
     * 顯示漏斗分析頁面
     */
    public function index(Request $request)
    {
        // 預設日期範圍：最近7天
        $startDate = $request->input('start_date', Carbon::now()->subDays(7)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // 取得漏斗分析數據
        $funnelData = CheckoutFunnelLog::getFunnelAnalysis(
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        );

        // 取得錯誤分析
        $errorData = CheckoutFunnelLog::getErrorAnalysis(
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        );

        // 取得依付款方式分組的分析
        $paymentMethodData = CheckoutFunnelLog::getFunnelByPaymentMethod(
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        );

        // 計算總體統計
        $totalStats = $this->calculateTotalStats($funnelData);

        // 取得每日趨勢
        $dailyTrend = $this->getDailyTrend($startDate, $endDate);

        return view('admin.funnel-analytics.index', [
            'funnelData' => $funnelData,
            'errorData' => $errorData,
            'paymentMethodData' => $paymentMethodData,
            'totalStats' => $totalStats,
            'dailyTrend' => $dailyTrend,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * 計算總體統計
     */
    private function calculateTotalStats($funnelData)
    {
        $cartView = $funnelData[CheckoutFunnelLog::STEP_CART_VIEW]['count'] ?? 0;
        $checkoutStart = $funnelData[CheckoutFunnelLog::STEP_CHECKOUT_START]['count'] ?? 0;
        $orderCreated = $funnelData[CheckoutFunnelLog::STEP_ORDER_CREATED]['count'] ?? 0;
        $paymentCompleted = $funnelData[CheckoutFunnelLog::STEP_PAYMENT_COMPLETED]['count'] ?? 0;
        $thankyouView = $funnelData[CheckoutFunnelLog::STEP_THANKYOU_PAGE_VIEW]['count'] ?? 0;

        return [
            'cart_to_checkout_rate' => $cartView > 0 ? round(($checkoutStart / $cartView) * 100, 2) : 0,
            'checkout_to_order_rate' => $checkoutStart > 0 ? round(($orderCreated / $checkoutStart) * 100, 2) : 0,
            'order_to_payment_rate' => $orderCreated > 0 ? round(($paymentCompleted / $orderCreated) * 100, 2) : 0,
            'overall_conversion_rate' => $cartView > 0 ? round(($thankyouView / $cartView) * 100, 2) : 0,
            'cart_abandonment_rate' => $cartView > 0 ? round((($cartView - $checkoutStart) / $cartView) * 100, 2) : 0,
            'payment_abandonment_rate' => $orderCreated > 0 ? round((($orderCreated - $paymentCompleted) / $orderCreated) * 100, 2) : 0,
        ];
    }

    /**
     * 取得每日趨勢
     */
    private function getDailyTrend($startDate, $endDate)
    {
        $data = CheckoutFunnelLog::select(
                DB::raw('DATE(created_at) as date'),
                'step',
                DB::raw('COUNT(DISTINCT session_id) as count')
            )
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date', 'step')
            ->orderBy('date')
            ->get();

        // 整理成前端可用的格式
        $trend = [];
        foreach ($data as $row) {
            if (!isset($trend[$row->date])) {
                $trend[$row->date] = [];
            }
            $trend[$row->date][$row->step] = $row->count;
        }

        return $trend;
    }

    /**
     * 匯出數據為 CSV
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(7)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $logs = CheckoutFunnelLog::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'funnel_logs_' . $startDate . '_to_' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'ID',
                'Session ID',
                'User ID',
                'Bill ID',
                'Step',
                'Status',
                'Error Message',
                'Payment Method',
                'Amount',
                'IP Address',
                'User Agent',
                'Created At'
            ]);

            // CSV Rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->session_id,
                    $log->user_id,
                    $log->bill_id,
                    $log->step,
                    $log->status,
                    $log->error_message,
                    $log->payment_method,
                    $log->amount,
                    $log->ip_address,
                    $log->user_agent,
                    $log->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API: 取得即時統計
     */
    public function stats(Request $request)
    {
        $minutes = $request->input('minutes', 60); // 預設最近1小時

        $cutoffTime = Carbon::now()->subMinutes($minutes);

        $data = CheckoutFunnelLog::select('step', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $cutoffTime)
            ->groupBy('step')
            ->get();

        $stats = [];
        foreach ($data as $row) {
            $stats[$row->step] = $row->count;
        }

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'timeframe' => $minutes . ' minutes',
        ]);
    }

    /**
     * 取得放棄的 Sessions
     */
    public function abandonedSessions(Request $request)
    {
        $minutesAgo = $request->input('minutes_ago', 30);

        $sessions = CheckoutFunnelTracker::getAbandonedSessions($minutesAgo);

        return view('admin.funnel-analytics.abandoned-sessions', [
            'sessions' => $sessions,
            'minutesAgo' => $minutesAgo,
        ]);
    }
}
