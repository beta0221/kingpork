<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShipmentPlan extends Model
{
    protected $table = 'shipment_plans';

    protected $fillable = [
        'plan_name',
        'status',
        'plan_data',
        'total_orders',
        'total_stages'
    ];

    protected $casts = [
        'plan_data' => 'array'
    ];

    // 狀態常數
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    /**
     * 關聯到批號使用記錄
     */
    public function batchUsages()
    {
        return $this->hasMany(BillBatchUsage::class);
    }

    /**
     * 檢查是否可以完成
     */
    public function canComplete()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * 檢查是否可以取消
     */
    public function canCancel()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }
}
