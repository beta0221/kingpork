<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceLog extends Model
{
    public static function log($bill_id,$info){
        $log = new InvoiceLog();
        $log->bill_id = $bill_id;
        $log->info = $info;
        $log->save();
    }
}
