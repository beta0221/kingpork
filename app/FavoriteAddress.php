<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteAddress extends Model
{
    protected $fillable = [
        'county', 'district', 'address', 'isDefault'
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
