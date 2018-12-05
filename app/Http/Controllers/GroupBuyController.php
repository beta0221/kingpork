<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Products;

class GroupBuyController extends Controller
{
    public function index()
    {
    	
    	$products = Products::where('category_id',6)->get();
    	
    	return view('groupBuy.test',['products'=>$products]);
    	// return view('groupBuy.index',['products'=>$products]);
    }

}
