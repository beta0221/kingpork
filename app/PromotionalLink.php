<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromotionalLink extends Model
{
    protected $fillable = [
        'code',
        'name',
        'discount_percentage',
        'applicable_categories',
        'start_date',
        'end_date',
        'is_active',
        'usage_count'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'discount_percentage' => 'float',
        'is_active' => 'boolean',
        'applicable_categories' => 'array',
        'usage_count' => 'integer'
    ];

    /**
     * 透過優惠碼查詢
     */
    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }

    /**
     * 檢查優惠連結是否有效
     */
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        // 使用台灣時區 (UTC+8)
        $now = new \DateTime('now', new \DateTimeZone('Asia/Taipei'));
        $startDate = new \DateTime($this->start_date, new \DateTimeZone('Asia/Taipei'));
        $endDate = new \DateTime($this->end_date, new \DateTimeZone('Asia/Taipei'));

        return $startDate <= $now && $endDate >= $now;
    }

    /**
     * 檢查商品類別是否適用此優惠
     */
    public function isCategoryApplicable($categoryId)
    {
        // 如果沒有設定適用類別，表示全部適用
        if (empty($this->applicable_categories)) {
            return true;
        }

        return in_array($categoryId, $this->applicable_categories);
    }

    /**
     * 計算購物車的優惠折扣金額
     * @param array $cartItems 購物車商品陣列，每個商品需包含 product 物件和 qty
     * @return array ['discount' => 折扣金額, 'applicable_items' => 適用商品列表]
     */
    public function calculateDiscount($cartItems)
    {
        if (!$this->isValid()) {
            return ['discount' => 0, 'applicable_items' => []];
        }

        $applicableTotal = 0;
        $applicableItems = [];

        foreach ($cartItems as $product) {

            // 檢查該商品的類別是否適用此優惠
            if ($this->isCategoryApplicable($product->category_id)) {
                $itemTotal = $product->price * $product->quantity;
                $applicableTotal += $itemTotal;
                $applicableItems[] = $product;
            }
        }

        // 計算折扣金額（折扣百分比）
        $discount = round($applicableTotal * ($this->discount_percentage / 100));

        return [
            'discount' => $discount,
            'applicable_items' => $applicableItems,
            'applicable_total' => $applicableTotal
        ];
    }

    /**
     * 增加使用次數
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * 取得狀態文字
     */
    public function getStatusText()
    {
        if (!$this->is_active) {
            return '已停用';
        }

        $now = new \DateTime('now', new \DateTimeZone('Asia/Taipei'));
        $startDate = new \DateTime($this->start_date, new \DateTimeZone('Asia/Taipei'));
        $endDate = new \DateTime($this->end_date, new \DateTimeZone('Asia/Taipei'));

        if ($now < $startDate) {
            return '未開始';
        } elseif ($now > $endDate) {
            return '已過期';
        } else {
            return '進行中';
        }
    }
}
