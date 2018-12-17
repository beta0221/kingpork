<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Products;

class SendGiftController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth',['only'=>['index']]);
	}

	
    public function index()
    {
    	$giftProduct = Products::where('slug','30002')->firstOrFail();
    	return view('sendGift.index',['giftProduct'=>$giftProduct]);
    }
}
