<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteAddress extends Model
{
    protected $fillable = [
        'county', 'district', 'address', 'isDefault',
        'ship_name', 'ship_phone', 'ship_email', 'ship_receipt',
        'ship_three_id', 'ship_three_company', 'ship_gender'
    ];


    /**
     * 前端格式資料
     */ 
    public function format()
    {
        return [
            'id' => $this->id,
            'address' => $this->county . ' ' . $this->district . ' ' . $this->address
        ];
    }

}
