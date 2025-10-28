<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusPromotion extends Model
{
    protected $fillable = [
        'name',
        'multiplier',
        'start_time',
        'end_time',
        'is_active'
    ];

    protected $dates = [
        'start_time',
        'end_time',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'multiplier' => 'float',
        'is_active' => 'boolean'
    ];

    /**
     * 取得目前進行中且啟用的活動
     * 如有多個活動同時進行，回傳倍數最高的
     */
    public static function getActivePromotion()
    {
        // 使用台灣時區 (UTC+8)
        $now = new \DateTime('now', new \DateTimeZone('Asia/Taipei'));
        $nowString = $now->format('Y-m-d H:i:s');

        return self::where('is_active', true)
            ->where('start_time', '<=', $nowString)
            ->where('end_time', '>=', $nowString)
            ->orderBy('multiplier', 'desc')
            ->first();
    }

    /**
     * 取得目前有效的紅利倍數
     * 如果沒有進行中的活動，回傳 1.0
     */
    public static function getCurrentMultiplier()
    {
        $promotion = self::getActivePromotion();
        return $promotion ? $promotion->multiplier : 1.0;
    }

    /**
     * 檢查活動是否正在進行中
     */
    public function isOngoing()
    {
        if (!$this->is_active) {
            return false;
        }

        // 使用台灣時區 (UTC+8)
        $now = new \DateTime('now', new \DateTimeZone('Asia/Taipei'));
        $startTime = new \DateTime($this->start_time, new \DateTimeZone('Asia/Taipei'));
        $endTime = new \DateTime($this->end_time, new \DateTimeZone('Asia/Taipei'));

        return $startTime <= $now && $endTime >= $now;
    }
}
