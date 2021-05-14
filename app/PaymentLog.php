<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    const TYPE_CREATE_PAYMENT = "CreatePayment";
    const TYPE_PAY_REQUEST = "PayRequest";

    public static function insert_row(int $bill_id,string $type,int $TransCode,string $TransMsg,string $Data){
        $log = new PaymentLog();
        $log->bill_id = $bill_id;
        $log->type = $type;
        $log->TransCode = $TransCode;
        $log->TransMsg = $TransMsg;
        $log->Data = $Data;
        $log->save();
    }

}
