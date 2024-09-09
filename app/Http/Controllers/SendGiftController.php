<?php

namespace App\Http\Controllers;

use App\ProductCategory;
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
		// return response('非常抱歉，禮盒目前暫停供應。');

		$cat = ProductCategory::getGiftCategory();
		$products = $cat->products()->get();
    	
    	return view('sendGift.index',[
			'products' => $products
		]);
    }
}
