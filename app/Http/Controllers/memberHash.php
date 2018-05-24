<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class memberHash extends Controller
{
	public function memberHash()
	{

		//584~841重複Hash要全部重新匯入＆Hash

		$users = User::where('id','>=',859)->where('id','<=',1100)->get();
		foreach ($users as $user) {
			$user->password=Hash::make($user->password);
			$user->save();
		}
		
		return('Hashing has done');
	}
    
}
