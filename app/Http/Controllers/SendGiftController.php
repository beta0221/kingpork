<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Products;

class SendGiftController extends Controller
{

	public function __construct()
	{
		// $this->middleware('auth',['only'=>['index']]);
	}

	
    public function index()
    {
		return response('非常抱歉，禮盒目前暫停供應。');
    	$giftProduct = Products::where('slug','30007')->firstOrFail();
    	return view('sendGift.index',['giftProduct'=>$giftProduct]);
    }
}
