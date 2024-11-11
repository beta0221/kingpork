<?php

namespace App\Http\ApiControllers;

use App\Bill;
use App\BillItem;
use App\Helpers\ECPay;
use App\Http\Controllers\BillController;
use App\Kart;
use App\Products;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;
use Validator;

class _BillController extends BillController
{

    /**
     * 成立訂單
     */
    public function checkout(Request $request)
    {

        $validator = Validator::make($request->all(), static::CHECKOUT_RULES);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        date_default_timezone_set('Asia/Taipei');

        $productDict = [];
        $slugs = array_filter(array_map(function ($item) {
            if (isset($item['slug'])) {
                return $item['slug'];
            }
            return null;
        }, $request->items));
        $slugs = array_values(array_unique($slugs));
        $_products = Products::whereIn('slug', $slugs)->get();
        foreach ($_products as $_product) {
            $productDict[$_product->slug] = $_product;
        }

        $MerchantTradeNo = Bill::genMerchantTradeNo(); //先給訂單編號
        $user_id = null;
        $user_name = null;
        $useBonus = 0;
        $total = 0;
        $getBonus = 0;
        $products = [];
        foreach ($request->items as $i => $item) {
            $slug = $item['slug'];
            $quantity = $item['quantity'];
            if ($slug == "99999") {
                continue;
            }

            $product = clone $productDict[$slug];
            $product->quantity = (int) $quantity;
            $products[] = $product;

            $getBonus += ($product->bonus * (int) $quantity);
            $total += ($product->price * (int) $quantity);
        }

        Log::info("Total: $total");
        Log::info("GetBonus: $getBonus");

        // 未達到 免運門檻 => 加入運費
        if ($total < static::SHIPPING_FEE_THRESHOLD) {
            $shippingFee = Products::where('slug', "99999")->firstOrFail();
            $shippingFee->quantity = 1;
            $products[] = $shippingFee;
            $total += (int) $shippingFee->price;
        }

        $user = $request->user();
        if ($user) {
            $user_id = $user->id;
            $user_name = $user->name;

            $bonus = $request->bonus;               // bonus{
            if ($bonus > $user->bonus) {
                $bonus = $user->bonus;
            }
            if (fmod($bonus, 50) != 0) {
                $bonus = $bonus - fmod($bonus, 50);
            }
            if ($bonus / 50 > $total) {
                $bonus = $total * 50;
            }
            if ($bonus < 0) {
                $bonus = 0;
            }
            $useBonus = $bonus / 50;
            $total = $total - $useBonus;          // }bonus    
        }

        DB::transaction(function () use ($user_id, $user_name, $MerchantTradeNo, $useBonus, $total, $getBonus, $request, $products) {
            $bill = Bill::insert_row($user_id, $user_name, $MerchantTradeNo, $useBonus, $total, $getBonus, $request);
            foreach ($products as $product) {
                BillItem::insert_row($bill->id, $product);
            }
        });

        if ($user) {
            Kart::where('user_id', $user->id)->delete(); //清除購物車
            if ($bonus != 0) {
                $user->updateBonus($bonus);  //扣除使用者紅利點數
            }
        }

        return response(['bill_id' => $MerchantTradeNo]);
    }

    /**
     * 我的訂單列表
     */
    public function list(Request $request)
    {

        $perPage = 10;
        $page = ($request->has('page')) ? intval($request->page) : 1;

        $user = $request->user();
        $query = Bill::where('user_id', $user->id)->orderBy('id', 'desc');
        $total = $query->count();

        $bills = $query->skip($perPage * ($page - 1))
            ->take($perPage)
            ->get();

        $billCollection = [];
        foreach ($bills as $bill) {
            $billCollection[] = $bill->formatAsList();
        }

        $paginator = new LengthAwarePaginator(
            $billCollection,
            $total,
            $perPage,
            $page
        );

        return response($paginator);
    }

    /**
     * 我的訂單 詳細資料
     */
    public function detail($bill_id)
    {
        $bill = Bill::where('bill_id', $bill_id)->firstOrFail();

        $products = $bill->products();
        $atmInfo = new stdClass;
        $cardInfo = new stdClass;

        if ($data = $bill->getPaymentInfo()) {
            switch ($bill->pay_by) {
                case 'ATM':
                    if (isset($data['ATMInfo'])) {
                        $atmInfo = (object) $data[ECPay::PAYMENT_INFO_ATM];
                    }
                    break;
                case 'CREDIT':
                    if (isset($data['CardInfo'])) {
                        $cardInfo = (object) $data[ECPay::PAYMENT_INFO_CARD];
                    }
                    break;
                default:
                    break;
            }
        }

        return response([
            'bill' => $bill->formatAsDetail(),
            'products' => $products,
            'atmInfo' => $atmInfo,
            'cardInfo' => $cardInfo
        ]);
    }

    /**
     * 取得 ECpay token
     */
    public function token($bill_id)
    {
        $bill = Bill::where('bill_id', $bill_id)->firstOrFail();
        $ecpay = new ECPay($bill);
        $ecpay->setOrderResultURL(app('url')->to("/bills/$bill_id"));   //訂單內頁

        if (!$token = $ecpay->getToken()) {
            return response(['error' => '系統錯誤']);
        }

        return response(['token' => $token]);
    }

    /**
     * 進行付款
     */
    public function pay(Request $request, $bill_id)
    {
        if (!$request->has('payToken')) {
            return response(['error' => '系統錯誤'], 403);
        }
        $bill = Bill::where('bill_id', $bill_id)->firstOrFail();
        $ecpay = new ECPay($bill);
        if (!$resultUrl = $ecpay->createPayment($request->payToken)) {
            return response(['error' => '系統錯誤'], 403);
        }

        $url = !is_null($ecpay->ThreeDURL) ? $ecpay->ThreeDURL : "/bills/$bill_id";

        return response([
            'success' => true,
            'url' => $url
        ]);
    }
}
