<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    const INCREASE = 1;
    const DECREASE = 0;

    const CAT_RAW_MATERIAL = "生產原料";
    const CAT_INNER_WRAP = "外包裝";
    const CAT_OTTER_WRAP = "內包裝";
    const CAT_SEMI_PRODUCT = "半成品";
    const CAT_PRODUCT = "成品";


    protected $fillable = [
        'name','slug','category'
    ];

    /**全部類別 */
    public static function getAllCats(){
        return [
            static::CAT_RAW_MATERIAL,
            static::CAT_INNER_WRAP,
            static::CAT_OTTER_WRAP,
            static::CAT_SEMI_PRODUCT,
            static::CAT_PRODUCT,
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

    public static function updateAmount($id,$quantity,$action){
        if($inventory = Inventory::find($id)){
            
            switch ($action) {
                case 1:
                    $inventory->amount += $quantity;
                    break;
                case 0:
                    $inventory->amount -= $quantity;
                        break;
                default:
                    break;
            }
            
            $inventory->save();
        }
    }

}
