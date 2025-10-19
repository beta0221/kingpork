<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillBatchUsage extends Model
{
    protected $table = 'bill_batch_usage';

    protected $fillable = [
        'bill_id',
        'inventory_batch_id',
        'quantity_used',
        'shipment_plan_id'
    ];

    /**
     * 關聯到訂單
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'bill_id');
    }

    /**
     * 關聯到批號
     */
    public function inventoryBatch()
    {
        return $this->belongsTo(InventoryBatch::class);
    }

    /**
     * 關聯到出貨計劃
     */
    public function shipmentPlan()
    {
        return $this->belongsTo(ShipmentPlan::class);
    }
}
