<?php

namespace App\Http\Controllers;

use App\PromotionalLink;
use App\ProductCategory;
use Illuminate\Http\Request;

class PromotionalLinkController extends Controller
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
        $promotionalLinks = PromotionalLink::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.promotional-links.index', compact('promotionalLinks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ProductCategory::all();
        return view('admin.promotional-links.form', compact('categories'));
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
            'code' => 'required|string|max:50|unique:promotional_links,code|regex:/^[A-Z0-9]+$/',
            'name' => 'required|string|max:100',
            'discount_percentage' => 'required|numeric|min:0.01|max:100',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'exists:productCategorys,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ], [
            'code.regex' => '優惠碼只能包含大寫英文字母和數字',
            'code.unique' => '此優惠碼已被使用',
            'end_date.after' => '結束日期必須在開始日期之後',
        ]);

        // 轉換日期時間格式
        $startDate = date('Y-m-d H:i:s', strtotime($request->start_date));
        $endDate = date('Y-m-d H:i:s', strtotime($request->end_date));

        PromotionalLink::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'discount_percentage' => $request->discount_percentage,
            'applicable_categories' => $request->applicable_categories,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $request->has('is_active') ? true : false
        ]);

        return redirect()->route('admin.promotional-links.index')
            ->with('success', '優惠連結已新增');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $promotionalLink = PromotionalLink::findOrFail($id);
        $categories = ProductCategory::all();

        return view('admin.promotional-links.show', compact('promotionalLink', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $promotionalLink = PromotionalLink::findOrFail($id);
        $categories = ProductCategory::all();

        return view('admin.promotional-links.form', compact('promotionalLink', 'categories'));
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
        $promotionalLink = PromotionalLink::findOrFail($id);

        $this->validate($request, [
            'code' => 'required|string|max:50|regex:/^[A-Z0-9]+$/|unique:promotional_links,code,' . $id,
            'name' => 'required|string|max:100',
            'discount_percentage' => 'required|numeric|min:0.01|max:100',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'exists:productCategorys,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ], [
            'code.regex' => '優惠碼只能包含大寫英文字母和數字',
            'code.unique' => '此優惠碼已被使用',
            'end_date.after' => '結束日期必須在開始日期之後',
        ]);

        // 轉換日期時間格式
        $startDate = date('Y-m-d H:i:s', strtotime($request->start_date));
        $endDate = date('Y-m-d H:i:s', strtotime($request->end_date));

        $promotionalLink->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'discount_percentage' => $request->discount_percentage,
            'applicable_categories' => $request->applicable_categories,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $request->has('is_active') ? true : false
        ]);

        return redirect()->route('admin.promotional-links.index')
            ->with('success', '優惠連結已更新');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $promotionalLink = PromotionalLink::findOrFail($id);
        $promotionalLink->delete();

        return redirect()->route('admin.promotional-links.index')
            ->with('success', '優惠連結已刪除');
    }

    /**
     * Toggle the active status of the promotional link
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggle($id)
    {
        $promotionalLink = PromotionalLink::findOrFail($id);
        $promotionalLink->is_active = !$promotionalLink->is_active;
        $promotionalLink->save();

        $status = $promotionalLink->is_active ? '啟用' : '停用';
        return redirect()->route('admin.promotional-links.index')
            ->with('success', "優惠連結已{$status}");
    }

    /**
     * Copy promotional link URL to clipboard (returns JSON for AJAX)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getLink($id)
    {
        $promotionalLink = PromotionalLink::findOrFail($id);
        $url = url('/promo/' . $promotionalLink->code);

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }
}
