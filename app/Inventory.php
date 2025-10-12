<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{

    const CAT_RAW_MATERIAL = "生產原料";
    const CAT_INNER_WRAP = "內包裝";
    const CAT_OTTER_WRAP = "外包裝";
    const CAT_SEMI_PRODUCT = "半成品";
    const CAT_PRODUCT = "成品";


    protected $fillable = [
        'name','slug','category','amount'
    ];

    /**全部類別 */
    public static function getAllCats(){
        return [
            static::CAT_PRODUCT,
            static::CAT_SEMI_PRODUCT,
            static::CAT_RAW_MATERIAL,
            static::CAT_INNER_WRAP,
            static::CAT_OTTER_WRAP,
        ];
    }

    public static function allGroupByCat(){
        $inventories = Inventory::all();
        $data = [];
        foreach ($inventories as $inventory) {
            if(!isset($data[$inventory->category])){
                $data[$inventory->category] = [];
            }
            $data[$inventory->category][] = $inventory;
        }
        return $data;
    }

    /**字典 */
    public static function nameDict(){
        $inventories = Inventory::all();
        $dict = [];
        foreach ($inventories as $inventory) {
            $dict[$inventory->id] = $inventory->name;
        }
        return $dict;
    }

    /**變更數量 */
    public function updateAmount($quantity){
        $this->amount += $quantity;
        $this->save();
    }

    /**
     * 關聯到 InventoryBatch 模型
     */
    public function batches()
    {
        return $this->hasMany('App\InventoryBatch');
    }

}
