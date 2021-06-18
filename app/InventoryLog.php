<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class InventoryLog extends Model
{

    public $timestamps = false;
    
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
