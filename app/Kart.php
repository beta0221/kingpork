<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kart extends Model
{
    public $table = "kart";

    public function user()
    {
    	return $this->belongsTo('App\User','user_id');
    }

}
