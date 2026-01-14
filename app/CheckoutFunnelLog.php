<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckoutFunnelLog extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'bill_id',
        'step',
        'status',
        'error_message',
        'metadata',
        'ip_address',
        'user_agent',
        'payment_method',
        'amount',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // 流程步驟常數
    const STEP_CART_VIEW = 'cart_view';
    const STEP_CHECKOUT_START = 'checkout_start';
    const STEP_CHECKOUT_FORM_SUBMIT = 'checkout_form_submit';
    const STEP_ORDER_CREATED = 'order_created';
    const STEP_PAYMENT_PAGE_VIEW = 'payment_page_view';
    const STEP_PAYMENT_TOKEN_REQUESTED = 'payment_token_requested';
    const STEP_PAYMENT_TOKEN_RECEIVED = 'payment_token_received';
    const STEP_PAYMENT_FORM_SUBMIT = 'payment_form_submit';
    const STEP_PAYMENT_REDIRECT = 'payment_redirect';
    const STEP_PAYMENT_3D_VERIFY = 'payment_3d_verify';
    const STEP_PAYMENT_COMPLETED = 'payment_completed';
    const STEP_THANKYOU_PAGE_VIEW = 'thankyou_page_view';

    // 狀態常數
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const STATUS_ABANDONED = 'abandoned';

    /**
     * 取得所有步驟
     */
    public static function getAllSteps()
    {
        return [
            self::STEP_CART_VIEW => '查看購物車',
            self::STEP_CHECKOUT_START => '開始結帳',
            self::STEP_CHECKOUT_FORM_SUBMIT => '提交結帳表單',
            self::STEP_ORDER_CREATED => '訂單建立成功',
            self::STEP_PAYMENT_PAGE_VIEW => '進入付款頁面',
            self::STEP_PAYMENT_TOKEN_REQUESTED => '請求付款Token',
            self::STEP_PAYMENT_TOKEN_RECEIVED => '收到付款Token',
            self::STEP_PAYMENT_FORM_SUBMIT => '提交付款表單',
            self::STEP_PAYMENT_REDIRECT => '導向ECPay',
            self::STEP_PAYMENT_3D_VERIFY => '3D驗證',
            self::STEP_PAYMENT_COMPLETED => '付款完成',
            self::STEP_THANKYOU_PAGE_VIEW => '感謝頁面',
        ];
    }

    /**
     * 關聯到使用者
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * 關聯到訂單
     */
    public function bill()
    {
        return $this->belongsTo('App\Bill', 'bill_id', 'bill_id');
    }

    /**
     * 取得漏斗分析數據
     * @param string $startDate 開始日期
     * @param string $endDate 結束日期
     * @param string $countMode 計數模式：
     *   - 'unique_sessions': 計算唯一 session 數（適合分析用戶轉換率）
     *   - 'total_events': 計算總事件數（適合分析系統使用量）
     * @return array
     */
    public static function getFunnelAnalysis($startDate = null, $endDate = null, $countMode = 'unique_sessions')
    {
        // 根據 countMode 選擇不同的計數方式
        if ($countMode === 'total_events') {
            $query = self::select('step', \DB::raw('COUNT(*) as count'));
        } else {
            // 預設：unique_sessions
            $query = self::select('step', \DB::raw('COUNT(DISTINCT session_id) as count'));
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $data = $query->groupBy('step')->get();

        $steps = self::getAllSteps();
        $result = [];

        foreach ($steps as $stepKey => $stepName) {
            $count = $data->where('step', $stepKey)->first();
            $result[$stepKey] = [
                'name' => $stepName,
                'count' => $count ? $count->count : 0,
            ];
        }

        return $result;
    }

    /**
     * 取得錯誤分析
     */
    public static function getErrorAnalysis($startDate = null, $endDate = null)
    {
        $query = self::where('status', self::STATUS_ERROR)
            ->select('step', 'error_message', \DB::raw('COUNT(*) as count'));

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->groupBy('step', 'error_message')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * 取得各付款方式的專屬漏斗步驟
     *
     * @return array 付款方式 => [名稱, 步驟陣列]
     */
    public static function getPaymentMethodFunnels()
    {
        return [
            'CREDIT' => [
                'name' => '信用卡',
                'steps' => [
                    self::STEP_CHECKOUT_FORM_SUBMIT,
                    self::STEP_ORDER_CREATED,
                    self::STEP_PAYMENT_PAGE_VIEW,
                    self::STEP_PAYMENT_TOKEN_REQUESTED,
                    self::STEP_PAYMENT_TOKEN_RECEIVED,
                    self::STEP_PAYMENT_FORM_SUBMIT,
                    self::STEP_PAYMENT_REDIRECT,
                    self::STEP_PAYMENT_3D_VERIFY,
                    self::STEP_PAYMENT_COMPLETED,
                    self::STEP_THANKYOU_PAGE_VIEW,
                ]
            ],
            'ATM' => [
                'name' => 'ATM轉帳',
                'steps' => [
                    self::STEP_CHECKOUT_FORM_SUBMIT,
                    self::STEP_ORDER_CREATED,
                    self::STEP_PAYMENT_PAGE_VIEW,
                    self::STEP_PAYMENT_TOKEN_REQUESTED,
                    self::STEP_PAYMENT_TOKEN_RECEIVED,
                    self::STEP_PAYMENT_FORM_SUBMIT,
                    self::STEP_PAYMENT_REDIRECT,
                    self::STEP_PAYMENT_COMPLETED,
                ]
            ],
            '貨到付款' => [
                'name' => '貨到付款',
                'steps' => [
                    self::STEP_CHECKOUT_FORM_SUBMIT,
                    self::STEP_ORDER_CREATED,
                    self::STEP_PAYMENT_COMPLETED,
                    self::STEP_THANKYOU_PAGE_VIEW,
                ]
            ],
            'FAMILY' => [
                'name' => '全家代收',
                'steps' => [
                    self::STEP_CHECKOUT_FORM_SUBMIT,
                    self::STEP_ORDER_CREATED,
                    self::STEP_PAYMENT_COMPLETED,
                    self::STEP_THANKYOU_PAGE_VIEW,
                ]
            ],
        ];
    }

    /**
     * 轉換數據為結構化格式並計算轉換率
     *
     * @param \Illuminate\Support\Collection $rawData 原始查詢數據
     * @return array 結構化的付款方式漏斗數據
     */
    private static function transformPaymentMethodData($rawData)
    {
        $funnels = self::getPaymentMethodFunnels();
        $allSteps = self::getAllSteps();
        $result = [];

        foreach ($funnels as $paymentMethod => $funnelConfig) {
            $steps = [];
            $previousCount = null;
            $firstStepCount = null;

            foreach ($funnelConfig['steps'] as $stepKey) {
                // 取得此付款方式和步驟的計數
                $record = $rawData->where('payment_method', $paymentMethod)
                                 ->where('step', $stepKey)
                                 ->first();

                $count = $record ? $record->count : 0;

                // 記錄第一步驟的數量作為基準
                if ($firstStepCount === null) {
                    $firstStepCount = $count;
                }

                // 計算相對於前一步驟的轉換率
                $conversionRate = 100.00;
                if ($previousCount !== null && $previousCount > 0) {
                    $conversionRate = round(($count / $previousCount) * 100, 2);
                }

                // 計算相對於第一步驟的流失率
                $dropOffRate = 0.00;
                if ($firstStepCount > 0) {
                    $dropOffRate = round((($firstStepCount - $count) / $firstStepCount) * 100, 2);
                }

                $steps[] = [
                    'step' => $stepKey,
                    'name' => $allSteps[$stepKey] ?? $stepKey,
                    'count' => $count,
                    'conversion_rate' => $conversionRate,  // 相對於前一步驟
                    'drop_off_rate' => $dropOffRate,        // 相對於第一步驟
                ];

                $previousCount = $count;
            }

            $result[$paymentMethod] = [
                'name' => $funnelConfig['name'],
                'total_sessions' => $firstStepCount ?? 0,
                'steps' => $steps,
            ];
        }

        return $result;
    }

    /**
     * 取得依付款方式分組的漏斗分析數據
     *
     * @param string|null $startDate 開始日期時間
     * @param string|null $endDate 結束日期時間
     * @param string $countMode 計數模式：
     *   - 'unique_sessions': 計算唯一 session 數（適合分析用戶轉換率）
     *   - 'total_events': 計算總事件數（適合分析系統使用量）
     * @return array 結構化的付款方式漏斗數據
     */
    public static function getFunnelByPaymentMethod($startDate = null, $endDate = null, $countMode = 'unique_sessions')
    {
        // 根據計數模式選擇不同的計數方式
        if ($countMode === 'total_events') {
            $countExpression = \DB::raw('COUNT(*) as count');
        } else {
            // 預設：unique_sessions
            $countExpression = \DB::raw('COUNT(DISTINCT session_id) as count');
        }

        // 查詢資料庫
        $query = self::select('payment_method', 'step', $countExpression)
            ->whereNotNull('payment_method');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $rawData = $query->groupBy('payment_method', 'step')->get();

        // 轉換為結構化格式
        return self::transformPaymentMethodData($rawData);
    }
}
