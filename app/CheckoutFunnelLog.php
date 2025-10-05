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
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getFunnelAnalysis($startDate = null, $endDate = null)
    {
        $query = self::select('step', \DB::raw('COUNT(DISTINCT session_id) as count'));

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

        // 計算轉換率
        $previousCount = null;
        foreach ($result as $key => &$value) {
            if ($previousCount !== null && $previousCount > 0) {
                $value['conversion_rate'] = round(($value['count'] / $previousCount) * 100, 2);
                $value['drop_rate'] = round((1 - ($value['count'] / $previousCount)) * 100, 2);
            } else {
                $value['conversion_rate'] = 100;
                $value['drop_rate'] = 0;
            }
            $previousCount = $value['count'];
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
     * 取得依付款方式分組的分析
     */
    public static function getFunnelByPaymentMethod($startDate = null, $endDate = null)
    {
        $query = self::select('payment_method', 'step', \DB::raw('COUNT(DISTINCT session_id) as count'))
            ->whereNotNull('payment_method');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->groupBy('payment_method', 'step')
            ->orderBy('payment_method')
            ->orderBy('step')
            ->get();
    }
}
