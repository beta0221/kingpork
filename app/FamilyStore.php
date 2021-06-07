<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FamilyStore extends Model
{
    public $table = "family_store";
    public $timestamps = false;

    protected $fillable = [
        'bill_id','number','name','address'
    ];
    
    public function bill(){
        return $this->hasOne('App\Bill','id','bill_id');
    }

    public static function insert_row(int $bill_id,Request $request){
        $row = new FamilyStore();
        $row->bill_id = $bill_id;
        $row->number = $request->store_number;
        $row->name = $request->store_name;
        $row->address = $request->store_address;
        $row->save();
    }
}
