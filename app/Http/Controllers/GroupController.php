<?php

namespace App\Http\Controllers;

use App\Group;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\User;
use App\GroupMember;
use App\GroupMemberBill;
use Illuminate\Support\Facades\Auth;
use Image;
use Storage;
use Session;

class GroupController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',['only'=>['create','store','index']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->isDealer) {
            $groups = Group::where('dealer_id',Auth::user()->id)->get();
        
            return view('dealer.dealer_index',['groups'=>$groups]);
        }

        return response('',404);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $products = ProductCategory::find(6)->products()->get();
        return view('dealer.dealer_create',['products'=>$products]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $this->validate($req,[
            'title'=>'required',
            'deadline'=>'required',
            'address'=>'required',
        ]);

        $group = new Group;
        $group_code = 'G' . time() . rand(10,99);
        $group->group_code = $group_code;
        $group->dealer_id = Auth::user()->id;
        $group->title = $req->title;
        $group->deadline = $req->deadline;
        $group->address = $req->address;
        $group->comment = $req->comment;


        if ($req->hasFile('image')) {
            //image stuff
            $image = $req->file('image'); //先把檔案存到 $image 變數
            $filename = time() . '.' . $image->getClientOriginalExtension(); //取得檔案完整原檔名再加上 時間在前面
            $path = 'groupIMG/'.$group_code;
            $location = public_path('images/groupIMG/'.$group_code .'/' . $filename);//把圖片url存到$location變數裡面
            if(!Storage::exists($path)){
                Storage::makeDirectory($path);   
            }else{
                \File::cleanDirectory($path);
            }
            Image::make($image)->resize(800,400)->save($location);//把圖面resize之後存進路徑
            $group->image = $filename;//存進資料庫語法跟其他欄位一樣只是放進來是$filename變數
        }
            

        $group->save();

        $group->products()->sync($req->products);

        return $this->index();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show($group_code)
    {
        $group = Group::where('group_code',$group_code)->firstOrFail();
        return view('dealer.dealer_join',['group'=>$group]);
        // return response()->json($group);
    }


    public function join(Request $req)
    {
        $this->validate($req,[
            'group_id'=>'required',
            'group_code'=>'required',
            'name'=>'required',
            'phone'=>'required',
            'product'=>'required',
            'amount'=>'required',
            'address'=>'required',
        ]);

        $member = GroupMember::create($req->except(['group_code','product','amount']));

        $i = 0;
        foreach ($req->product as $product) {
            $member->membersBill()->create([
                'product_id'=>$product,
                'amount'=>$req->amount[$i],
            ]);
            $i++;
        }
        
        Session::flash('success','成功發送');
        $group = Group::where('group_code',$req->group_code)->firstOrFail();
        return view('dealer.dealer_join',['group'=>$group]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        //
    }
}
