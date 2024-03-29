<?php

namespace App\Http\Controllers;

use App\Group;
use App\ProductCategory;
use App\Products;
use Illuminate\Http\Request;
use App\User;
use App\GroupMember;
use App\GroupMemberBill;
use Illuminate\Support\Facades\Auth;
use Image;
use Storage;
use Session;
use Excel;
use Illuminate\Support\Facades\DB;

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
            $groups = Group::where('dealer_id',Auth::user()->id)->orderBy('id','DESC')->get();
        
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
        $products = ProductCategory::find(8)->productsById()->get();

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
            $image = $req->file('image'); //先把檔案存到 $image 變數
            $filename = $this->storeImage($image,$group_code);
            $group->image = $filename;//存進資料庫語法跟其他欄位一樣只是放進來是$filename變數
        }
            

        $group->save();

        $group->products()->sync($req->products);

        return redirect()->route('group.index');

    }
    private function storeImage($image,$group_code){
        //image stuff
        $filename = time() . '.' . $image->getClientOriginalExtension(); //取得檔案完整原檔名再加上 時間在前面
        $path = 'groupIMG/'.$group_code;
        $location = public_path('images/groupIMG/'.$group_code .'/' . $filename);//把圖片url存到$location變數裡面
        if(!Storage::exists($path)){
            Storage::makeDirectory($path);   
        }else{
            $files = Storage::allFiles($path);
            Storage::delete($files);
        }
        //resize(800,400)
        Image::make($image)->save($location);//把圖面resize之後存進路徑
        return $filename;
    }


    public function get_group($group_code)
    {
        $group = Group::where('group_code',$group_code)->firstOrFail();
        
        $itemArray = [];
        
        $allTotal = 0;
        foreach ($group->products as $key => $product) {
            // $max = Products::find($product->id)->min_for_dealer;
            $total = $group->productSum($product->id);
            $totalPrice = $total * $product->price;

            $allTotal += $totalPrice;
            // if ($max == $total) {
                $itemArray[$product->slug] = $total; 
                
            // }

        }

        //門檻5000
        if($allTotal < 5000){
            $itemArray = [];
        }

        $result = [
            'address'=>$group->address,
            'itemArray'=>$itemArray,
        ];


        $group->is_done = true;
        $group->save();

        return response()->json($result);
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

        $productTotal = 0;
        foreach ($group->products as $product) {
            $sum = $group->productSum($product->id);
            $productTotal += $sum * $product->price;
        }


        return view('dealer.dealer_join',[
            'group'=>$group,
            'productTotal'=>$productTotal
        ]);
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
        ]);

        // $isChecked = true;
        // $group = Group::where('group_code',$req->group_code)->firstOrFail();
        
        
        // $productAmount = [];
        // foreach ($req->product as $key=>$product) {
        //     $max = Products::find($product)->min_for_dealer;
        //     $total = $group->productSum($product);
        //     $productAmount[$product] = $total;
        //     if ($total + $req->amount[$key] > $max) {
        //         $isChecked = false;
        //     }
        // }

        // if (!$isChecked) {
            // return response()->json($productAmount);
        // }else{
            $member = GroupMember::create($req->except(['group_code','product','amount']));
            foreach ($req->product as $i=>$product) {
                $member->membersBill()->create([
                    'product_id'=>$product,
                    'amount'=>$req->amount[$i],
                ]);
            }
            return response()->json('success');
        // }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        $products = $group->products()->get();
        return view('dealer.dealer_create',[
            'products'=>$products,
            'group'=>$group
        ]);
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
        $group->title = $request->title;
        $group->deadline = $request->deadline;
        $group->address = $request->address;
        $group->comment = $request->comment;

        if ($request->hasFile('image')){
            $image = $request->file('image');
            $filename = $this->storeImage($image,$group->group_code);
            // return response($filename);
            $group->image = $filename;
        }

        $group->save();
            
        return redirect()->route('group.show',$group->group_code);
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

    public function export($group_code)
    {

        $cellData = [
            ['姓名','電話','訂購產品','數量','團購價','小計','訂購時間','備註'],
        ];

        $dataSet = DB::select('SELECT member.`name` as member_name,member.`phone`,member.`comment`,products.`name` as product_name,products.`price` as price,bill.`amount`,member.`created_at` FROM `groups` as groups ,`group_members` as member, `group_members_bills` as bill, `products` as products WHERE groups.`group_code` = :group_code and groups.`id` = member.`group_id` and member.`id` = bill.`member_id` and bill.`product_id` = products.`id`;',['group_code'=>$group_code]);

        foreach ($dataSet as $key => $row) {
            $newArray = [];
            $subtotal = $row->amount * $row->price;
            array_push($newArray,$row->member_name,$row->phone,$row->product_name,$row->amount,$row->price,$subtotal,$row->created_at,$row->comment);
            array_push($cellData,$newArray);
        }

        $title = Group::where('group_code',$group_code)->firstOrFail()->title;

        Excel::create($title, function($excel)use($cellData) {

            $excel->sheet('Sheetname', function($sheet)use($cellData) {

                $sheet->rows($cellData);

            });

        })->download('xls');

        // return response()->json($cellData);

    }



}
