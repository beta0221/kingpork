<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    const INCREASE = 1;
    const DECREASE = 0;

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
