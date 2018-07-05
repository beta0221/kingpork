<?php

namespace App\Http\Controllers;

use App\Banner;
use Illuminate\Http\Request;
use Image;
use Storage;


class BannerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banners = Banner::all();
        return view('banner.index',['banners' => $banners]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('banner.create');
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
            'image'=>'required',
        ]);

        $banner = new Banner;
        $banner->link = $request->link;
        $banner->alt = $request->alt;

        $image = $request->file('image');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $location = public_path('images/banner/'.$filename);
        Image::make($image)->save($location);

        $banner->image = $filename;

        $banner->save();

        return redirect()->route('banner.index');
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $banner = Banner::find($id);

        return view('banner.edit',['banner'=>$banner]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::find($id);

        //after validate

        $banner->link = $request->input('link');
        $banner->alt = $request->input('alt');

        if($request->hasFile('image')){

            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images/banner/' . $filename);
            Image::make($image)->save($location);

            $oldFilename = $banner->image;
            $banner->image = $filename;
            
            Storage::delete('banner/' . $oldFilename);
        }

        $banner->save();

        return redirect()->route('banner.index');

    }

    public function switch(Request $request)
    {

       
        if ($request->switch == 0) {
            $from = $request->banner;
            $to = $from - 1;
        }
        if ($request->switch == 1) {
            $from = $request->banner;
            $to = $from + 1;
        }

        $bannerTo = Banner::findOrFail($to);
        $bannerTo->id = 0;
        $bannerTo->save();

        $bannerFrom = Banner::findOrFail($from);
        $bannerFrom->id = $to;
        $bannerFrom->save();

        $return = Banner::findOrFail(0);
        $return->id = $from;
        $return->save();


        return response()->json('success');
        
    }


    public function publicBanner($id)
    {

        $banner = Banner::find($id);

        if ($banner->public == 1) {
            $banner->public = 0;
            $banner->save();
            return response()->json(0);    
        }else{
            $banner->public = 1;
            $banner->save();
            return response()->json(1);
        }

        
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner = Banner::find($id);
        Storage::delete('banner/' . $banner->image);
        $banner->delete();
        return response()->json(['msg'=>'成功刪除']);
    }
}
