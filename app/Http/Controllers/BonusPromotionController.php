<?php

namespace App\Http\Controllers;

use App\BonusPromotion;
use Illuminate\Http\Request;

class BonusPromotionController extends Controller
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
        $promotions = BonusPromotion::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.bonus-promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.bonus-promotions.form');
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
            'name' => 'required|string|max:255',
            'multiplier' => 'required|numeric|min:1|max:999',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        // 轉換日期時間格式
        $startTime = date('Y-m-d H:i:s', strtotime($request->start_time));
        $endTime = date('Y-m-d H:i:s', strtotime($request->end_time));

        BonusPromotion::create([
            'name' => $request->name,
            'multiplier' => $request->multiplier,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_active' => $request->has('is_active') ? true : false
        ]);

        return redirect()->route('admin.bonus-promotions.index')
            ->with('success', '紅利活動已新增');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $promotion = BonusPromotion::findOrFail($id);
        return view('admin.bonus-promotions.form', compact('promotion'));
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
        $promotion = BonusPromotion::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'multiplier' => 'required|numeric|min:1|max:999',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        // 轉換日期時間格式
        $startTime = date('Y-m-d H:i:s', strtotime($request->start_time));
        $endTime = date('Y-m-d H:i:s', strtotime($request->end_time));

        $promotion->update([
            'name' => $request->name,
            'multiplier' => $request->multiplier,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_active' => $request->has('is_active') ? true : false
        ]);

        return redirect()->route('admin.bonus-promotions.index')
            ->with('success', '紅利活動已更新');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $promotion = BonusPromotion::findOrFail($id);
        $promotion->delete();

        return redirect()->route('admin.bonus-promotions.index')
            ->with('success', '紅利活動已刪除');
    }

    /**
     * Toggle the active status of the promotion
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggle($id)
    {
        $promotion = BonusPromotion::findOrFail($id);
        $promotion->is_active = !$promotion->is_active;
        $promotion->save();

        $status = $promotion->is_active ? '啟用' : '停用';
        return redirect()->route('admin.bonus-promotions.index')
            ->with('success', "活動已{$status}");
    }
}
