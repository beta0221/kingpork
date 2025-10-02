<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Products;

class GroupBuyController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth', ['only' => ['index']]);
	}

	public function index(Request $request)
	{
		$user = $request->user();
		$products = Products::where('category_id', 6)->where('public', 1)->get();

		return view('groupBuy.index', [
			'products' => $products,
			'addresses' => $user->addresses()->orderBy('isDefault', 'desc')->get()
		]);
	}
}
