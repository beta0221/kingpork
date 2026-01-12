<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\BillItem;
use App\FamilyStore;
use App\Products;
use App\Kart;
use App\Helpers\ECPay;
use App\Jobs\ECPayInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleAnalyticsService;
use App\Services\CheckoutFunnelTracker;
use App\CheckoutFunnelLog;
use Mail;


// require 'vendor/autoload.php';

class BillController extends Controller
{

    /** 免運門檻 */
    const SHIPPING_FEE_THRESHOLD = 799;

    public function __construct()
    {
        $this->middleware('auth',['only'=>['index','cancelBill', 'view_billDetail']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bills = Bill::where('user_id',Auth::user()->id)->orderBy('id','desc')->paginate(10);
        return view('bill.index',['bills'=>$bills]);
    }

    public function store(Request $request){

        // 追蹤：提交結帳表單
        CheckoutFunnelTracker::trackSuccess(
            CheckoutFunnelLog::STEP_CHECKOUT_FORM_SUBMIT,
            $request,
            ['payment_method' => $request->ship_pay_by]
        );

        try {
            $this->validate($request,[
                'item.*'=>'required',
                'quantity.*'=>'required|integer|min:0',
                'ship_name'=>'required',
                'ship_phone'=>'required',
                'ship_address'=>'required_if:use_favorite_address,0',
                'ship_email'=>'required|E-mail',
                'ship_pay_by'=>'required',
                'carrier_id'=>'required',
                'store_number'=>'required_if:carrier_id,1',
                'store_name'=>'required_if:carrier_id,1',
                'store_address'=>'required_if:carrier_id,1',
                'favorite_address'=>'required_if:use_favorite_address,1',
                'save_credit_card'=>'boolean',
                // 'use_saved_card'=>'integer|exists:user_credit_cards,id'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 追蹤：表單驗證失敗
            CheckoutFunnelTracker::trackError(
                CheckoutFunnelLog::STEP_CHECKOUT_FORM_SUBMIT,
                '表單驗證失敗: ' . json_encode($e->errors()),
                $request,
                ['payment_method' => $request->ship_pay_by]
            );
            throw $e;
        }

        if($request->carrier_id == Bill::CARRIER_ID_FAMILY_MART && $request->ship_pay_by == 'cod'){
            // 追蹤：業務規則錯誤
            CheckoutFunnelTracker::trackError(
                CheckoutFunnelLog::STEP_CHECKOUT_FORM_SUBMIT,
                '全家超商不支援貨到付款',
                $request,
                ['payment_method' => $request->ship_pay_by]
            );
            return ('錯誤');
        }

        $additionalProducts = Products::getAdditionalProducts('slug');
        $hasAdditionalProduct = false;
        $hasMainProduct = false;

        foreach ($request->item as $slug) {
            if(in_array($slug,$additionalProducts)){
                $hasAdditionalProduct = true;
            }else{
                if($slug != "99999"){   //排除運費
                    $hasMainProduct = true;
                }
            }
        }

        if($hasAdditionalProduct == true && $hasMainProduct == false){
            return redirect()->route('kart.index');
        }

        date_default_timezone_set('Asia/Taipei');
        $MerchantTradeNo = Bill::genMerchantTradeNo(); //先給訂單編號
        //$MerchantTradeNo = time() . rand(10,99);//先給訂單編號

        $user_id = null;
        $user_name = $request->user_name;
        $useBonus = 0;
        $total = 0;
        $getBonus = 0;
        $products = [];

        foreach ($request->item as $index => $slug) {
            if ($slug == "99999") { continue; }
            $quantity = $request->quantity[$index];
            $product = Products::where('slug', $slug)->firstOrFail();
            $product->quantity = (int)$quantity;
            $products[] = $product;

            $getBonus += ($product->bonus * (int)$quantity);
            $total += ($product->price * (int)$quantity);
        }

        // 未達到 免運門檻
        if ($total < static::SHIPPING_FEE_THRESHOLD) {
            // 加入運費
            $product = Products::where('slug', "99999")->firstOrFail();
            $product->quantity = 1;
            $products[] = $product;
            $total += (int)$product->price;
        }

        if (!in_array('99999',$request->item) AND $total < static::SHIPPING_FEE_THRESHOLD) { return('錯誤'); }

        // 處理優惠碼折扣（在紅利點數之前）
        $promoCode = null;
        $promoDiscount = 0;
        if (session()->has('promo_code')) {
            $promoCode = session('promo_code');
            $promotionalLink = \App\PromotionalLink::findByCode($promoCode);

            if ($promotionalLink && $promotionalLink->isValid()) {
                // 準備購物車商品資料（不包含運費）
                $cartItems = [];
                foreach ($products as $product) {
                    if ($product->slug != "99999") { // 排除運費
                        $cartItems[] = $product;
                    }
                }

                // 計算優惠折扣（僅針對適用類別的商品）
                $discountResult = $promotionalLink->calculateDiscount($cartItems);
                $promoDiscount = $discountResult['discount'];

                // 套用折扣到總金額
                if ($promoDiscount > 0) {
                    $total = $total - $promoDiscount;
                }
            } else {
                // 優惠碼無效，清除 session
                session()->forget('promo_code');
                $promoCode = null;
            }
        }

        $user = $request->user();
        if($user){
            $user_id = $user->id;
            $user_name = $user->name;

            $bonus = $request->bonus;               // bonus{
            if ($bonus > $user->bonus) { $bonus = $user->bonus; }
            if (fmod($bonus,50) != 0) { $bonus = $bonus - fmod($bonus,50); }
            if ($bonus / 50 > $total) { $bonus = $total * 50; }
            if ($bonus < 0) { $bonus = 0; }
            $useBonus = $bonus / 50;
            $total = $total - $useBonus;          // }bonus
        }

        // 使用常用地址
        if ($request->has('use_favorite_address')) {
            $address = $user->addresses()->findOrFail($request->favorite_address);
            $request->merge([
                'ship_county' => $address->county,
                'ship_district' => $address->district,
                'ship_address' => $address->address,
                'ship_name' => $address->ship_name,
                'ship_phone' => $address->ship_phone,
                'ship_email' => $address->ship_email,
                'ship_receipt' => $address->ship_receipt,
                'ship_three_id' => $address->ship_three_id,
                'ship_three_company' => $address->ship_three_company,
                'ship_gender' => $address->ship_gender
            ]);
        }

        $bill = Bill::insert_row($user_id,$user_name,$MerchantTradeNo,$useBonus,$total,$getBonus,$request,$promoCode,$promoDiscount);

        // 增加優惠碼使用次數
        if ($promoCode && $promoDiscount > 0) {
            $promotionalLink = \App\PromotionalLink::findByCode($promoCode);
            if ($promotionalLink) {
                $promotionalLink->incrementUsage();
            }
        }

        foreach ($products as $product) {
            BillItem::insert_row($bill->id,$product);
        }
        if($request->carrier_id == Bill::CARRIER_ID_FAMILY_MART){
            FamilyStore::insert_row($bill->id,$request);
        }

        if($user){
            Kart::where('user_id',$user->id)->delete(); //清除購物車
            if($bonus != 0){
                $user->updateBonus($bonus);  //扣除使用者紅利點數
            }

            // 使用其他地址 && 設為常用地址
            if (!$request->has('use_favorite_address') && $request->has('add_favorite')) {
                $user->addresses()
                    ->where('isDefault', 1)
                    ->update(['isDefault' => 0]);
                $user->addresses()
                    ->create([
                        'county' => $request->ship_county,
                        'district' => $request->ship_district,
                        'address' => $request->ship_address,
                        'ship_name' => $request->ship_name,
                        'ship_phone' => $request->ship_phone,
                        'ship_email' => $request->ship_email,
                        'ship_receipt' => $request->ship_receipt,
                        'ship_three_id' => $request->ship_three_id,
                        'ship_three_company' => $request->ship_three_company,
                        'ship_gender' => $request->ship_gender,
                        'isDefault' => 1
                    ]);
            }
        }

        // 追蹤：訂單建立成功
        CheckoutFunnelTracker::trackFromBill(
            CheckoutFunnelLog::STEP_ORDER_CREATED,
            $bill,
            $request
        );

        //寄送信件
        switch ($request->ship_pay_by) {
            case Bill::PAY_BY_CREDIT:
            case Bill::PAY_BY_ATM:
                return redirect()->route('payBill',['bill_id'=>$MerchantTradeNo]);
                break;
            case 'cod':
            case Bill::PAY_BY_FAMILY:
                // 追蹤：付款完成
                CheckoutFunnelTracker::trackFromBill(
                    CheckoutFunnelLog::STEP_PAYMENT_COMPLETED,
                    $bill,
                    $request
                );
                return redirect()->route('billThankyou',['bill_id'=>$MerchantTradeNo]);
            default:
                break;
        }

    }

    public function view_payBill($bill_id){

        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $SPToken = $bill->SPToken;

        // 追蹤：進入付款頁面
        CheckoutFunnelTracker::trackFromBill(
            CheckoutFunnelLog::STEP_PAYMENT_PAGE_VIEW,
            $bill,
            request()
        );

        $ecpay = new ECPay($bill);

        // 檢查訂單是否已建立過先前存的 Token
        if (!$SPToken) {
            // 追蹤：請求付款Token（向 API 請求）
            CheckoutFunnelTracker::trackFromBill(
                CheckoutFunnelLog::STEP_PAYMENT_TOKEN_REQUESTED,
                $bill,
                request()
            );

            if(!$token = $ecpay->getToken()){
                // 追蹤：Token取得失敗
                CheckoutFunnelTracker::trackError(
                    CheckoutFunnelLog::STEP_PAYMENT_TOKEN_REQUESTED,
                    'ECPay Token取得失敗: ' . $ecpay->errorMsg,
                    request()
                );
                return $ecpay->errorMsg;
            }

            // 儲存 Token 到資料庫以供下次使用
            $bill->SPToken = $token;
            $bill->save();

            // 追蹤：收到付款Token（向 API 請求）
            CheckoutFunnelTracker::trackFromBill(
                CheckoutFunnelLog::STEP_PAYMENT_TOKEN_RECEIVED,
                $bill,
                request()
            );

            // 本次使用
            $SPToken = $token;
        }

        return view('bill.payBill_v2',[
            'bill_id' => $bill_id,
            'token' => $SPToken,
            'ecpaySDKUrl'=> $ecpay->getEcpaySDKUrl(),
        ]);
    }

    public function payBill(Request $request,$bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();

        // 追蹤：提交付款表單
        CheckoutFunnelTracker::trackFromBill(
            CheckoutFunnelLog::STEP_PAYMENT_FORM_SUBMIT,
            $bill,
            $request
        );

        if(!$request->has('PayToken')){
            CheckoutFunnelTracker::trackError(
                CheckoutFunnelLog::STEP_PAYMENT_FORM_SUBMIT,
                '缺少PayToken',
                $request,
                ['bill_id' => $bill->bill_id, 'payment_method' => $bill->pay_by]
            );
            return '錯誤頁面。';
        }

        $ecpay = new ECPay($bill);

        $resultUrl = $ecpay->createPayment($request->PayToken);

        if(!$resultUrl){
            CheckoutFunnelTracker::trackError(
                CheckoutFunnelLog::STEP_PAYMENT_REDIRECT,
                'ECPay createPayment失敗',
                $request,
                ['bill_id' => $bill->bill_id, 'payment_method' => $bill->pay_by]
            );
            return '錯誤頁面';
        }

        // 追蹤：導向ECPay
        CheckoutFunnelTracker::trackFromBill(
            CheckoutFunnelLog::STEP_PAYMENT_REDIRECT,
            $bill,
            $request,
            ['metadata' => ['redirect_url' => $resultUrl]]
        );

        if ($this->isExternalUrl($resultUrl)) {
            // 追蹤：導向ECPay
            CheckoutFunnelTracker::trackFromBill(
                CheckoutFunnelLog::STEP_PAYMENT_3D_VERIFY,
                $bill,
                $request,
                ['metadata' => ['redirect_url' => $resultUrl]]
            );
        }

        return redirect($resultUrl);
    }

    /** 付款完成api */
    public function api_ecpay_pay(Request $request,$bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $ecpay = new ECPay($bill);
        $isSuccess = $ecpay->handlePayRequest($request);

        if($isSuccess){
            // 追蹤：付款完成
            CheckoutFunnelTracker::trackFromBill(
                CheckoutFunnelLog::STEP_PAYMENT_COMPLETED,
                $bill,
                $request
            );

            $bill->status = 1;
            $bill->save();
            $bill->sendBonusToBuyer();
            dispatch(new ECPayInvoice($bill,ECPayInvoice::TYPE_ISSUE)); //開立發票

            // ATM交易成功時，透過後端發送GA購買轉換事件
            if ($bill->pay_by == Bill::PAY_BY_ATM) { // ATM付款
                try {
                    $gaService = new GoogleAnalyticsService();
                    // 嘗試從請求中取得 client_id，或生成新的
                    $clientId = $this->extractClientId($request) ?? null;
                    $gaService->sendPurchaseEvent($bill, $clientId);
                    
                    Log::info("GA Purchase Event sent for ATM payment", [
                        'bill_id' => $bill_id,
                        'amount' => $bill->price
                    ]);
                } catch (\Exception $e) {
                    Log::error("Failed to send GA Purchase Event for ATM payment", [
                        'bill_id' => $bill_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        Log::info("-----綠界回傳-----");
        Log::info("訂單編號：" . $bill_id);
        Log::info(json_encode($request->all()));
        Log::info("-----------------");


        return "1|OK";
    }


    /** 綠界付款完成頁面  */
    public function view_ecpay_thankyouPage($bill_id){
        return redirect()->route('billThankyou',['bill_id'=>$bill_id]);
    }

    public function view_billThankyou($bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $products = $bill->products();

        // 追蹤：進入感謝頁面 (流程完成)
        CheckoutFunnelTracker::trackFromBill(
            CheckoutFunnelLog::STEP_THANKYOU_PAGE_VIEW,
            $bill,
            request()
        );

        // 準備 GA4 電商追蹤數據
        $gaData = null;
        if (config('app.env') === 'production' && config('app.ga_id')) {
            $items = $bill->products();
            $gaItems = [];
            
            foreach ($items as $item) {
                if ($product = Products::find($item->product_id)) {
                    $gaItems[] = [
                        'item_name' => $item->name,
                        'item_id' => (string)$item->product_id,
                        'price' => (float)$item->price,
                        'item_category' => $product->productCategory->name,
                        'quantity' => (int)$item->quantity,
                    ];
                }
            }
            
            $gaData = [
                'event' => 'purchase',
                'ecommerce' => [
                    'transaction_id' => $bill_id,
                    'value' => (float)$bill->price,
                    'currency' => 'TWD',
                    'items' => $gaItems
                ]
            ];
        }

        return view('bill.thankyou',[
            'bill'=>$bill,
            'products' => $products,
            'gaData' => $gaData,
        ]);
    }

    public function view_billDetail(Request $request, $bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();

        $user = $request->user();
        if ($bill->user_id != $user->id) {
            return response("Forbidden", 403);
        }

        $products = $bill->products();
        $atmInfo = null;
        $cardInfo = null;
        $storeInfo = null;

        if($data = $bill->getPaymentInfo()){
            switch ($bill->pay_by) {
                case 'ATM':
                    if(isset($data['ATMInfo'])){
                        $atmInfo = (object)$data[ECPay::PAYMENT_INFO_ATM];
                    }
                    break;
                case 'CREDIT':
                    if(isset($data['CardInfo'])){
                        $cardInfo = (object)$data[ECPay::PAYMENT_INFO_CARD];
                    }
                    break;
                default:
                    break;
            }
        }

        if($bill->carrier_id == Bill::CARRIER_ID_FAMILY_MART){
            $storeInfo = $bill->familyStore;
        }

        return view('bill.detail',[
            'bill' => $bill,
            'carrierDict' => Bill::getAllCarriers(),
            'products' => $products,
            'atmInfo' => $atmInfo,
            'cardInfo' => $cardInfo,
            'storeInfo' => $storeInfo
        ]);
    }


    public function getDataLayerForGA($bill_id){
        $bill = Bill::where('bill_id',$bill_id)->firstOrFail();
        $items = json_decode($bill->item,true);
        
        $products = [];
        foreach ($items as $item) {
            $product = Products::where('slug',$item['slug'])->first();
            
            if ($product) {
                $category = $product->productCategory()->first();
                $obj = [
                    'item_name' => $product->name,
                    'item_id' => (string)$product->id,
                    'price' => (float)$product->price,
                    'item_category' => $category ? $category->name : '未分類',
                    'quantity' => (int)$item['quantity'],
                ];
                $products[] = $obj;
            }
        }
        
        // GA4 格式的購買事件數據
        $dataLayer = [
            'event' => 'purchase',
            'ecommerce' => [
                'transaction_id' => $bill_id,
                'value' => (float)$bill->price,
                'currency' => 'TWD',
                'items' => $products
            ]
        ];
        
        return response()->json($dataLayer);
    }

    public function sendMail(Request $request)
    {   
        $bill = Bill::where('bill_id',$request->MerchantTradeNo)->firstOrFail();
        
        if ($request->pay_by=='ATM') {
            $bill->status = 's';
            $bill->save();
        }elseif ($request->pay_by == 'CREDIT') {
            $bill->status = '1';
            $bill->save();
        }
        
        $items = json_decode($bill->item,true);
        $i = 0;
        $itemArray = [];
        foreach($items as $item)
        {
            $product = Products::where('slug', $item['slug'])->firstOrFail();   
            $itemArray[$i] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $item['quantity'],
            ];
            $i++;
        }


        if ($request->pay_by == 'ATM') {
            $data = array(
                'user_name'=>$bill->ship_name,
                'ship_gender'=>$bill->ship_gender,
                'ship_name'=>$bill->ship_name,
                'ship_phone'=>$bill->ship_phone,
                'ship_county'=>$bill->ship_county,
                'ship_district'=>$bill->ship_district,
                'ship_address'=>$bill->ship_address,
                'email' => $bill->ship_email,
                'items' => $itemArray,
                'bill_id' =>$bill->bill_id,
                'bonus_use'=>$bill->bonus_use,
                'price' => $bill->price,
                'pay_by'=>'ATM轉帳繳費',
                'TradeDate'=>$request->TradeDate,
                'BankCode'=>$request->BankCode,
                'vAccount'=>$request->vAccount,
                'ExpireDate'=>$request->ExpireDate,
            );
            Mail::send('emails.atm',$data,function($message) use ($data){
                $message->from('kingpork80390254@gmail.com','金園排骨');
                $message->to($data['email']);
                $message->subject('金園排骨-購買確認通知');
            });

            return response()->json('s');

        }elseif ($request->pay_by == 'CREDIT') {
            $data = array(
                'user_name'=>$bill->ship_name,
                'ship_gender'=>$bill->ship_gender,
                'ship_name'=>$bill->ship_name,
                'ship_phone'=>$bill->ship_phone,
                'ship_county'=>$bill->ship_county,
                'ship_district'=>$bill->ship_district,
                'ship_address'=>$bill->ship_address,
                'email' => $bill->ship_email,
                'items' => $itemArray,
                'bill_id' =>$bill->bill_id,
                'bonus_use'=>$bill->bonus_use,
                'price' => $bill->price,
                'pay_by'=>'信用卡繳費',
                'TradeDate'=>$request->TradeDate,
            );
            Mail::send('emails.credit',$data,function($message) use ($data){
                $message->from('kingpork80390254@gmail.com','金園排骨');
                $message->to($data['email']);
                $message->subject('金園排骨-購買確認通知');
            });

            return response()->json('s');
        }   
    }

    public function cancelBill($id)
    {
        $bill = Bill::where('bill_id',$id)->firstOrFail();
        $user = Auth::user();

        if($bill->user_id != $user->id){
            return response()->json('error');
        }
        if ($bill->status == 1 || $bill->shipment != 0){
            return response()->json('error');   
        }

        $amount = $bill->bonus_use * 50;
        $user->updateBonus($amount,false);

        $bill->updateShipment(Bill::SHIPMENT_VOID);
        return response()->json('success');

    }


    // -------- Private Functions ----------

    private function isExternalUrl($url)
    {
        // 取得當前網站的 domain
        $currentHost = parse_url(config('app.url'), PHP_URL_HOST);

        // 解析目標 URL 的 domain
        $targetHost = parse_url($url, PHP_URL_HOST);

        // 如果無法解析 host，視為內部路徑
        if (!$targetHost) {
            return false;
        }

        // 比較 domain 是否相同
        return $currentHost !== $targetHost;
    }

}
