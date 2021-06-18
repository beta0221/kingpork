<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Retailer extends Model
{
    public static function nameDict(){
        $retailers = Retailer::all();
        $dict = [];
        foreach ($retailers as $retailer) {
            $dict[$retailer->id] = $retailer->name;
        }
        return $dict;
    }
}
