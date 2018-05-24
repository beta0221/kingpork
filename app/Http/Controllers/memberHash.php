<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class memberHash extends Controller
{
	public function memberHash()
	{
		$users = User::where('id','>=',584)->get();
		foreach ($users as $user) {
			$user->password=Hash::make($user->password);
			$user->save();
		}
		
		return('Hashing has done');
	}
    
}
