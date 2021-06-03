<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    public function toggleStatus(){

        if($this->status == 1){
            $this->status = 0;
        }else{
            $this->status = 1;
        }
        $this->save();
        
    }
}
