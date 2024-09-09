<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    
    protected $guarded = [];

    public $timestamps = false;

    public function product(){
        return $this->hasOne('App\Products','id','product_id');
    }

    public static function insert_row(int $bill_id,Products $product){
        $billItem = new BillItem();
        $billItem->bill_id = $bill_id;
        $billItem->product_id = $product->id;
        $billItem->name = $product->name;
        $billItem->price = $product->price;
        $billItem->quantity = $product->quantity;
        $billItem->short = $product->short;
        $billItem->description = $product->discription;
        $billItem->save();
    }


    public function sumInventoryAmount($_quantity = null){
        $quantity = (is_null($_quantity) ? $this->quantity : $_quantity);
        return $this->product->sumInventoryAmount($quantity);
    }

}
