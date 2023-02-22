<?php

namespace App\Http\Controllers;

use App\PackageItem;
use Illuminate\Http\Request;

class PackageItemController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $packageItems = PackageItem::orderBy('id','desc')->paginate(10);
        return view('packageItem.index',[
            'packageItems' => $packageItems
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'short' => 'required',
            'erp_key' => 'required'
        ]);

        $request->merge(['image'=>'test.png']);

        // PackageItem::create($request->only(['name', 'short', 'erp_key', 'image']));
        PackageItem::create($request->all());

        return redirect()->route('packageItem.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PackageItem  $packageItem
     * @return \Illuminate\Http\Response
     */
    public function show(PackageItem $packageItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PackageItem  $packageItem
     * @return \Illuminate\Http\Response
     */
    public function edit(PackageItem $packageItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PackageItem  $packageItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PackageItem $packageItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PackageItem  $packageItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(PackageItem $packageItem)
    {
        //
    }
}
