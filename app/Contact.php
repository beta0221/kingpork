<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    protected $fillable = ['name', 'email', 'title', 'message'];

    public function toggleStatus(){

        if($this->status == 1){
            $this->status = 0;
        }else{
            $this->status = 1;
        }
        $this->save();
        
    }
}
