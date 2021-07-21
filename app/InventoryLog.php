<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class InventoryLog extends Model
{

    public $timestamps = false;

    const ACTION_PURCHASE = 'purchase';
    const ACTION_PRODUCE = 'produce';
    const ACTION_PACK = 'pack';
    const ACTION_SALE = 'sale';


    /**全部Action */
    public static function getAllActions(){
        return [
            static::ACTION_PURCHASE=>'進貨',
            static::ACTION_PRODUCE=>'生產',
            static::ACTION_PACK=>'包裝',
            static::ACTION_SALE=>'銷貨',
        ];
    }

    /**Action對應庫存類別的表 */
    public static function getActionMap(){
        return[
            static::ACTION_PURCHASE =>[
                Inventory::CAT_RAW_MATERIAL=>'+',
                Inventory::CAT_INNER_WRAP=>'+',
                Inventory::CAT_OTTER_WRAP=>'+',
                Inventory::CAT_PRODUCT=>'+',
                Inventory::CAT_SEMI_PRODUCT=>null,
            ],
            static::ACTION_PRODUCE =>[
                Inventory::CAT_RAW_MATERIAL=>'-',
                Inventory::CAT_INNER_WRAP=>'-',
                Inventory::CAT_OTTER_WRAP=>null,
                Inventory::CAT_PRODUCT=>'+',
                Inventory::CAT_SEMI_PRODUCT=>'+',
            ],
            static::ACTION_PACK =>[
                Inventory::CAT_RAW_MATERIAL=>null,
                Inventory::CAT_INNER_WRAP=>'-',
                Inventory::CAT_OTTER_WRAP=>null,
                Inventory::CAT_PRODUCT=>'+',
                Inventory::CAT_SEMI_PRODUCT=>'-',
            ],
            static::ACTION_SALE =>[
                Inventory::CAT_RAW_MATERIAL=>null,
                Inventory::CAT_INNER_WRAP=>null,
                Inventory::CAT_OTTER_WRAP=>'-',
                Inventory::CAT_PRODUCT=>'-',
                Inventory::CAT_SEMI_PRODUCT=>null,
            ],
        ];
    }
    
    public function inventories(){
        return $this->belongsToMany('App\Inventory','inventory_logs_inventory','inventory_logs_id','inventory_id')->withPivot('quantity');
    }

    public static function insert_row(Request $request){

        $retailer_id = ($request->action == 'sale')?$request->retailer_id:null;

        $log = new InventoryLog();
        $log->date = $request->date;
        $log->action = $request->action;
        $log->event = $request->event;
        $log->retailer_id = $retailer_id;
        $log->save();
        return $log;
    }
}
