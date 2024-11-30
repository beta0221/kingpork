<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteAddress extends Model
{
    protected $fillable = [
        'county', 'district', 'address', 'isDefault'
    ];

}
