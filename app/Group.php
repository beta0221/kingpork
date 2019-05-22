<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public function products(){
    	return $this->belongsToMany('App\Products','group_product','group_id','product_id');
    }

    public function members(){
    	return $this->hasMany('App\GroupMember','group_id','id');
    }


    public function productSum($id){

    	$members = $this->members;

    	$total = 0;

    	foreach ($members as $member) {
    		$total += $member->membersBill()->where('product_id',$id)->sum('amount');
    	}

    	// return $total;
    	return $total;

    }

}
