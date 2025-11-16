<?php

namespace App\Http\Controllers;

use App\PromotionalLink;

class PromoController extends Controller
{
    /**
     * 捕捉優惠連結並儲存到 Session
     *
     * @param  string  $code
     * @return \Illuminate\Http\Response
     */
    public function capture($code)
    {
        // 將優惠碼轉為大寫並存入 Session
        session(['promo_code' => strtoupper($code)]);

        // 驗證優惠碼是否有效
        $promotionalLink = PromotionalLink::findByCode(strtoupper($code));

        if ($promotionalLink && $promotionalLink->isValid()) {
            // 優惠碼有效，導向首頁並顯示成功訊息
            return redirect('/')->with('promo_success', "已套用優惠：{$promotionalLink->name}");
        } else {
            // 優惠碼無效或已過期，清除 Session
            session()->forget('promo_code');
            return redirect('/')->with('promo_error', '優惠碼無效或已過期');
        }
    }
}
