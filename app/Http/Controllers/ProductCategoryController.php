<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductCategory;

class ProductCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin',['except'=>['show','view_vipProducts']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productCategorys = ProductCategory::all();

        return view('productCategorys.index',['productCategorys'=>$productCategorys]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('productCategorys.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'=>'required|max:255'
        ]);

        $productCategory = new ProductCategory;
        $productCategory->name = $request->name;
        $productCategory->content = $request->content;
        $productCategory->save();

        return redirect()->route('productCategory.store');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $additionalCategory = null;
        $addableCat = [1,2,3,9];
        $productCategory = ProductCategory::find($id);
        
        if(in_array($id,$addableCat)){
            $additionalCategory = ProductCategory::find(12);
        }

        return view('productCategorys.show',[
            'productCategory'=>$productCategory,
            'additionalCategory'=>$additionalCategory
        ]);
    }


    public function view_vipProducts($vip){
        
        $vipCat = [
            'yong_shun' => 25,//永順
            'cgmh_emba' => 25,//長庚EMBA
            'tcesia' => 25,//桃園仲介公會
            'chaoyang_rotary' => 25,//朝陽扶輪社
        ];
        if(!isset($vipCat[$vip])){ abort(404); }

        $cat_id = $vipCat[$vip];

        return $this->show($cat_id);

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $PC=ProductCategory::find($id);
        return view('productCategorys.edit',['PC'=>$PC]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $PC=ProductCategory::find($id);
        $PC->name = $request->name;
        $PC->content = $request->content;
        $PC->save();

        return redirect()->route('productCategory.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
