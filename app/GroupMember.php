<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    public function membersBill(){
    	return $this->hasMany('App\GroupMembersBill','member_id','id');
    }


    protected $guarded = [];
}
