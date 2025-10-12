<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model
{
    protected $fillable = [
        'inventory_id', 'batch_number', 'quantity', 'manufactured_date'
    ];

    protected $dates = [
        'manufactured_date'
    ];

    /**
     * 關聯到 Inventory 模型
     */
    public function inventory()
    {
        return $this->belongsTo('App\Inventory');
    }

    /**
     * 更新批號數量
     * @param int $quantity 要增加或減少的數量（正數為增加，負數為減少）
     */
    public function updateQuantity($quantity)
    {
        $this->quantity += $quantity;
        $this->save();
    }

    /**
     * 取得特定庫存的所有批號
     * @param int $inventoryId
     * @return Collection
     */
    public static function getByInventoryId($inventoryId)
    {
        return static::where('inventory_id', $inventoryId)
                     ->orderBy('manufactured_date', 'asc')
                     ->get();
    }

    /**
     * 取得或建立批號
     * @param int $inventoryId
     * @param string $batchNumber
     * @return InventoryBatch
     */
    public static function getOrCreate($inventoryId, $batchNumber)
    {
        return static::firstOrCreate(
            ['inventory_id' => $inventoryId, 'batch_number' => $batchNumber],
            ['quantity' => 0]
        );
    }
}
