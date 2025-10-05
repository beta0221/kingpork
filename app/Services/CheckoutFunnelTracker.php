<?php

namespace App\Services;

use App\CheckoutFunnelLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CheckoutFunnelTracker
{
    /**
     * 記錄流程步驟
     *
     * @param string $step 步驟名稱 (使用 CheckoutFunnelLog::STEP_* 常數)
     * @param Request|null $request Laravel Request 物件
     * @param array $options 額外選項
     *   - status: 'success', 'error', 'abandoned'
     *   - error_message: 錯誤訊息
     *   - bill_id: 訂單編號
     *   - payment_method: 付款方式
     *   - amount: 金額
     *   - metadata: 其他 metadata (array)
     * @return CheckoutFunnelLog
     */
    public static function track($step, Request $request = null, array $options = [])
    {
        // 如果沒有傳入 request，嘗試從 Laravel 容器取得
        if (!$request) {
            $request = app('request');
        }

        $_payment_method = $options['payment_method'] ?? null;
        if ($_payment_method == 'cod') {
            $_payment_method = '貨到付款';
        }

        $data = [
            'session_id' => self::getSessionId($request),
            'user_id' => Auth::id(),
            'step' => $step,
            'status' => $options['status'] ?? CheckoutFunnelLog::STATUS_SUCCESS,
            'error_message' => $options['error_message'] ?? null,
            'bill_id' => $options['bill_id'] ?? null,
            'payment_method' => $_payment_method,
            'amount' => $options['amount'] ?? null,
            'metadata' => $options['metadata'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        return CheckoutFunnelLog::create($data);
    }

    /**
     * 記錄成功步驟
     */
    public static function trackSuccess($step, Request $request = null, array $options = [])
    {
        $options['status'] = CheckoutFunnelLog::STATUS_SUCCESS;
        return self::track($step, $request, $options);
    }

    /**
     * 記錄錯誤步驟
     */
    public static function trackError($step, $errorMessage, Request $request = null, array $options = [])
    {
        $options['status'] = CheckoutFunnelLog::STATUS_ERROR;
        $options['error_message'] = $errorMessage;
        return self::track($step, $request, $options);
    }

    /**
     * 記錄放棄步驟
     */
    public static function trackAbandoned($step, Request $request = null, array $options = [])
    {
        $options['status'] = CheckoutFunnelLog::STATUS_ABANDONED;
        return self::track($step, $request, $options);
    }

    /**
     * 取得或產生 Session ID
     * 優先使用 Laravel session_id，若不存在則產生唯一識別碼
     */
    private static function getSessionId(Request $request)
    {
        // 嘗試從 session 取得
        if ($request->hasSession()) {
            return $request->session()->getId();
        }

        // 嘗試從 cookie 取得自訂的追蹤 ID
        if ($request->hasCookie('_funnel_sid')) {
            return $request->cookie('_funnel_sid');
        }

        // 產生新的 ID (用於 API 呼叫等情況)
        return uniqid('funnel_', true);
    }

    /**
     * 從 Bill 物件追蹤
     *
     * @param string $step
     * @param \App\Bill $bill
     * @param Request|null $request
     * @param array $options
     */
    public static function trackFromBill($step, $bill, Request $request = null, array $options = [])
    {
        $options['bill_id'] = $bill->bill_id;
        $options['payment_method'] = $bill->pay_by;
        $options['amount'] = $bill->price;

        if (!isset($options['user_id']) && $bill->user_id) {
            $options['user_id'] = $bill->user_id;
        }

        return self::track($step, $request, $options);
    }

    /**
     * API 追蹤 (從前端 AJAX 呼叫)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function apiTrack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'step' => 'required|string',
            'status' => 'nullable|string|in:success,error,abandoned',
            'error_message' => 'nullable|string',
            'bill_id' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'amount' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $request->only([
            'step', 'status', 'error_message', 'bill_id',
            'payment_method', 'amount', 'metadata'
        ]);

        try {
            $log = self::track(
                $validated['step'],
                $request,
                $validated
            );

            return response()->json([
                'success' => true,
                'log_id' => $log->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Checkout Funnel Tracking Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to track'
            ], 500);
        }
    }

    /**
     * 批次記錄多個步驟 (用於補追蹤遺失的步驟)
     */
    public static function trackBatch(array $steps, Request $request = null, array $commonOptions = [])
    {
        $logs = [];
        foreach ($steps as $step => $stepOptions) {
            $options = array_merge($commonOptions, $stepOptions);
            $logs[] = self::track($step, $request, $options);
        }
        return $logs;
    }

    /**
     * 取得當前 session 的追蹤歷程
     */
    public static function getSessionJourney(Request $request = null)
    {
        if (!$request) {
            $request = app('request');
        }

        $sessionId = self::getSessionId($request);

        return CheckoutFunnelLog::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * 檢查某個 session 是否完成整個流程
     */
    public static function isJourneyCompleted($sessionId)
    {
        return CheckoutFunnelLog::where('session_id', $sessionId)
            ->where('step', CheckoutFunnelLog::STEP_THANKYOU_PAGE_VIEW)
            ->exists();
    }

    /**
     * 找出未完成的 sessions (超過指定時間未完成)
     *
     * @param int $minutesAgo 多少分鐘前
     * @return \Illuminate\Support\Collection
     */
    public static function getAbandonedSessions($minutesAgo = 30)
    {
        $cutoffTime = now()->subMinutes($minutesAgo);

        // 找出有開始但未完成的 sessions
        $startedSessions = CheckoutFunnelLog::where('step', CheckoutFunnelLog::STEP_CHECKOUT_START)
            ->where('created_at', '<=', $cutoffTime)
            ->pluck('session_id');

        $completedSessions = CheckoutFunnelLog::where('step', CheckoutFunnelLog::STEP_THANKYOU_PAGE_VIEW)
            ->whereIn('session_id', $startedSessions)
            ->pluck('session_id');

        $abandonedSessionIds = $startedSessions->diff($completedSessions);

        return CheckoutFunnelLog::whereIn('session_id', $abandonedSessionIds)
            ->orderBy('session_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('session_id');
    }
}
