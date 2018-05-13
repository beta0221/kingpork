<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Banner;

class PageController extends Controller
{
    public function getLanding(){

    	$banners = Banner::all();
        if ($banners == null) {
            return view('pages.landingPage',['banners'=>'default.png']);
        }
	    return view('pages.landingPage',['banners'=>$banners]);
    	
    }

    public function getContact(){
    	return view('pages.contact');
    }

    public function showProductPage(){
    	return view('pages.productPage');
    }

    public function guide(){
        return view('pages.guide');
    }
}
