<?php

namespace App\Http\Controllers;

use App\Bill;
use App\Helpers\Pagination;
use App\Inventory;
use Illuminate\Http\Request;
use App\Products;
use App\ProductCategory;
use App\Kart;
use Image;
use Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin',['except'=>['show']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $p = new Pagination($request);
        $category = ($request->category) ? $request->category : null;
        $public = $request->public;

        $query = Products::orderBy($p->orderBy,$p->ascOrdesc);

        if($category){
            $query = $query->where('category_id',$category);
        }

        if(!is_null($public) || $public != ''){
            $query = $query->where('public',$public);
        }

        $total = $query->count();
        $totalPage = ceil($total / $p->rows);
        $products = $query->skip($p->skip)->take($p->rows)->get();

        $cats = ProductCategory::all();
        $inventories = Inventory::all();

        return view('products.index', [
            'products' => $products,
            'cats'=>$cats,
            'totalPage'=>$totalPage,
            'request'=>$request,
            'inventories'=>$inventories
        ]);
    }

    /**取得產品的庫存關聯 */
    public function inventory($id){
        $product = Products::findOrFail($id);
        $inventories = $product->inventory()->get();
        $data = [];
        foreach ($inventories as $inventory) {
            $data[$inventory->pivot->inventory_id] = $inventory->pivot->quantity;
        }
        return response()->json($data);
    }

    /**更新產品庫存關聯 */
    public function updateInventory(Request $request,$id){
        $product = Products::findOrFail($id);

        $sync = [];
        if($request->has('inventory')){
            foreach ($request->inventory as $inventory_id => $quantity) {
                if(!$quantity){ continue; }
                $sync[$inventory_id] = ['quantity'=>$quantity];
            }
        }
        
        $product->inventory()->sync($sync);
        return response()->json(['m'=>'success']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $productCategorys = ProductCategory::all();
        return view('products.create',['productCategorys' => $productCategorys]);
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
            'name'=>'required|max:255',
            'slug'=>'required|alpha_dash|min:5|max:255|unique:products,slug',
            'short'=>'required',
            'category_id'=>'required|integer',
            // 'format'=>'required|max:255',
            'price'=>'required|integer',
            'bonus'=>'required|integer',
            'image'=>'required|image',
            // 'content'->'required'
        ]);

        $product = new Products;
        $product->name = $request->name;
        $product->short = $request->short;
        $product->erp_id = $request->erp_id;
        $product->discription = $request->discription;
        $product->slug = $request->slug;
        $product->category_id=$request->category_id;
        $product->format = $request->format;
        $product->price = $request->price;
        $product->bonus = $request->bonus;
        // $product->content = $request->content;

        //image stuff
        $image = $request->file('image'); //先把檔案存到 $image 變數
        $filename = time() . '.' . $image->getClientOriginalExtension(); //取得檔案完整原檔名再加上 時間在前面
        $location = public_path('images/productsIMG/' . $filename);//把圖片url存到$location變數裡面
        Image::make($image)->resize(600,600)->save($location);//把圖面resize之後存進路徑

        $product->image = $filename;//存進資料庫語法跟其他欄位一樣只是放進來是$filename變數




        $product->save();

        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Auth::user()) {
            $product = Products::find($id);

            $kart = Kart::where('product_id',$id)
            ->where('user_id', Auth::user()->id)
            ->first();

            //判斷是否已加入購物車
            if($kart == null)
            {
                $add = false;
            }
            else
            {
                $add = true;
            }

            return response()->json([
                'id'=>$product->id,
                'name'=>$product->name,
                'slug'=>$product->slug,
                'format'=>$product->format,
                'price'=>$product->price,
                'bonus'=>$product->bonus,
                'image'=>$product->image,
                'content'=>$product->content,
                'add'=>$add
            ]);
        }
        else
        {
            $product = Products::find($id);
            return response()->json([
                'id'=>$product->id,
                'name'=>$product->name,
                'slug'=>$product->slug,
                'format'=>$product->format,
                'price'=>$product->price,
                'bonus'=>$product->bonus,
                'image'=>$product->image,
                'content'=>$product->content,
                'add'=>'guest',
            ]);
        }
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Products::find($id);
        $productCategorys = ProductCategory::all();

        $shit = [];

        foreach($productCategorys as $productCategory){
            $shit[$productCategory->id] = $productCategory->name;
        }
        
        return view('products.edit',[
            'product'=>$product,
            'productCategorys'=>$shit,
            'carriers'=>[Bill::CARRIER_ID_BLACK_CAT=>Bill::CARRIER_BLACK_CAT],
            'carrierRestriction'=>$product->carrierRestriction(),
        ]);

    }

    public function publicProduct($id)
    {

        $product = Products::find($id);

        if ($product->public == 1) {
            $product->public = 0;
            $product->save();
            return response()->json(0);    
        }else{
            $product->public = 1;
            $product->save();
            return response()->json(1);
        }
        
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
        $product = Products::find($id);

        $this->validate($request,[
            'name'=>'required|max:255',
            //'slug'=>"required|alpha_dash|min:5|max:255|unique:products,slug,$id",//unique（table,column,except除了自己以外）!!!外圍一定要用雙引號才有辦法把變數$id放進來!!!
            'short'=>'required',
            'category_id'=>'required|integer',
            // 'format'=>'required|max:255',
            'price'=>'required|integer',
            'bonus'=>'required|integer',
            'image'=>'sometimes|image',
            // 'content'->'required'
        ]);

        $product->name=$request->input('name');
        $product->discription=$request->input('discription');
        $product->short = $request->input('short');
        $product->erp_id = $request->input('erp_id');
        $product->category_id=$request->input('category_id');
        $product->format=$request->input('format');
        $product->price=$request->input('price');
        $product->bonus=$request->input('bonus');
        $product->content=$request->input('content');

        if($request->hasFile('image')){
            //image stuff
            $image = $request->file('image'); //先把檔案存到 $image 變數
            $filename = time() . '.' . $image->getClientOriginalExtension(); //取得檔案完整原檔名再加上 時間在前面
            $location = public_path('images/productsIMG/' . $filename);//把圖片url存到$location變數裡面
            Image::make($image)->resize(600,600)->save($location);//把圖面resize之後存進路徑

            $oldFilename = $product->image;//原來的圖片存進oldFilename變數

            $product->image = $filename;//存進資料庫語法跟其他欄位一樣只是放進來是$filename變數

            Storage::delete('productsIMG/' . $oldFilename);
        }

        $product->save();

        $carrier_id_array = [];
        if($request->has('carrier_id')){
            $carrier_id_array = $request->carrier_id;
        }
        $product->updateCarrierRestriction($carrier_id_array);
        
        if($request->has('previous_url')){
            return redirect($request->previous_url);
        }
        return redirect()->route('products.index');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Products::find($id);
        Storage::delete('productsIMG/' . $product->image);
        $product->delete();

        return redirect()->route('products.index');
    }
}
