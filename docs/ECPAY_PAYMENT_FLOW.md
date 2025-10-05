# ECPay 結帳付款流程文件

## 目錄
- [系統架構概覽](#系統架構概覽)
- [結帳流程詳解](#結帳流程詳解)
- [ECPay 金流整合](#ecpay-金流整合)
- [付款方式處理](#付款方式處理)
- [Webhook 回呼機制](#webhook-回呼機制)
- [發票開立流程](#發票開立流程)
- [GA4 追蹤整合](#ga4-追蹤整合)
- [紅利點數系統](#紅利點數系統)
- [結帳流程追蹤系統](#結帳流程追蹤系統) ⭐ NEW
- [環境配置](#環境配置)
- [錯誤處理](#錯誤處理)
- [時序圖](#時序圖)

---

## 系統架構概覽

### 核心檔案
- **控制器**: `app/Http/Controllers/BillController.php`
- **ECPay 輔助類**: `app/Helpers/ECPay.php`
- **訂單模型**: `app/Bill.php`
- **訂單明細**: `app/BillItem.php`
- **付款日誌**: `app/PaymentLog.php`
- **發票工作**: `app/Jobs/ECPayInvoice.php`
- **GA4 服務**: `app/Services/GoogleAnalyticsService.php`
- **流程追蹤服務**: `app/Services/CheckoutFunnelTracker.php` ⭐ NEW
- **流程追蹤模型**: `app/CheckoutFunnelLog.php` ⭐ NEW

### 環境變數
```env
ECPAY_MERCHANT_ID=      # 特店編號
ECPAY_HASH_KEY=         # HashKey
ECPAY_HASH_IV=          # HashIV
```

### 付款方式常數
```php
Bill::PAY_BY_CREDIT     // 信用卡
Bill::PAY_BY_ATM        // ATM 轉帳
Bill::PAY_BY_FAMILY     // 全家超商代收
'cod'                   // 貨到付款
```

---

## 結帳流程詳解

### 1. 訂單建立 (BillController::store)
**位置**: `app/Http/Controllers/BillController.php:75-243`

#### Step 1: 驗證請求資料
```php
// 驗證規則 (lines 79-94)
$this->validate($request, [
    'item.*' => 'required',
    'quantity.*' => 'required|integer|min:0',
    'ship_name' => 'required',
    'ship_phone' => 'required',
    'ship_address' => 'required_if:use_favorite_address,0',
    'ship_email' => 'required|E-mail',
    'ship_pay_by' => 'required',
    'carrier_id' => 'required',
    'store_number' => 'required_if:carrier_id,1',
    'store_name' => 'required_if:carrier_id,1',
    'store_address' => 'required_if:carrier_id,1',
    'favorite_address' => 'required_if:use_favorite_address,1',
    'save_credit_card' => 'boolean',
]);
```

#### Step 2: 業務規則檢查
```php
// 全家超商不能使用貨到付款 (lines 96-98)
if ($request->carrier_id == Bill::CARRIER_ID_FAMILY_MART &&
    $request->ship_pay_by == 'cod') {
    return ('錯誤');
}

// 檢查是否只有附加商品 (lines 102-120)
// 不允許只購買附加商品 (如配菜、醬料等)
if ($hasAdditionalProduct == true && $hasMainProduct == false) {
    return redirect()->route('kart.index');
}
```

#### Step 3: 產生訂單編號
```php
// line 123
date_default_timezone_set('Asia/Taipei');
$MerchantTradeNo = Bill::genMerchantTradeNo();
```

#### Step 4: 計算訂單金額
```php
// lines 129-151
$total = 0;
$getBonus = 0;

foreach ($request->item as $index => $slug) {
    if ($slug == "99999") { continue; } // 跳過運費
    $quantity = $request->quantity[$index];
    $product = Products::where('slug', $slug)->firstOrFail();

    $getBonus += ($product->bonus * (int)$quantity);
    $total += ($product->price * (int)$quantity);
}

// 未達免運門檻 (799元)，加入運費
if ($total < self::SHIPPING_FEE_THRESHOLD) {
    $product = Products::where('slug', "99999")->firstOrFail();
    $product->quantity = 1;
    $products[] = $product;
    $total += (int)$product->price;
}
```

#### Step 5: 紅利點數處理 (會員限定)
```php
// lines 157-169
if ($user) {
    $bonus = $request->bonus;

    // 紅利點數驗證規則
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

    // 計算折扣 (50點 = 1元)
    $useBonus = $bonus / 50;
    $total = $total - $useBonus;
}
```

#### Step 6: 處理常用地址
```php
// lines 172-186
if ($request->has('use_favorite_address')) {
    $address = $user->addresses()->findOrFail($request->favorite_address);
    $request->merge([
        'ship_county' => $address->county,
        'ship_district' => $address->district,
        'ship_address' => $address->address,
        'ship_name' => $address->ship_name,
        'ship_phone' => $address->ship_phone,
        // ... 其他地址欄位
    ]);
}
```

#### Step 7: 寫入資料庫
```php
// lines 188-197
// 建立訂單主檔
$bill = Bill::insert_row($user_id, $user_name, $MerchantTradeNo,
                         $useBonus, $total, $getBonus, $request);

// 建立訂單明細
foreach ($products as $product) {
    BillItem::insert_row($bill->id, $product);
}

// 全家超商額外資料
if ($request->carrier_id == Bill::CARRIER_ID_FAMILY_MART) {
    FamilyStore::insert_row($bill->id, $request);
}
```

#### Step 8: 後續處理 (會員限定)
```php
// lines 201-227
if ($user) {
    // 清除購物車
    Kart::where('user_id', $user->id)->delete();

    // 扣除紅利點數
    if ($bonus != 0) {
        $user->updateBonus($bonus);
    }

    // 儲存為常用地址
    if (!$request->has('use_favorite_address') && $request->has('add_favorite')) {
        $user->addresses()
            ->where('isDefault', 1)
            ->update(['isDefault' => 0]);
        $user->addresses()->create([
            'county' => $request->ship_county,
            'district' => $request->ship_district,
            'address' => $request->ship_address,
            // ... 其他欄位
            'isDefault' => 1
        ]);
    }
}
```

#### Step 9: 付款方式分流
```php
// lines 232-242
switch ($request->ship_pay_by) {
    case Bill::PAY_BY_CREDIT:  // 信用卡
    case Bill::PAY_BY_ATM:     // ATM轉帳
        // 導向付款頁面
        return redirect()->route('payBill', ['bill_id' => $MerchantTradeNo]);

    case 'cod':                // 貨到付款
    case Bill::PAY_BY_FAMILY:  // 全家超商代收
        // 直接導向感謝頁面
        return redirect()->route('billThankyou', ['bill_id' => $MerchantTradeNo]);

    default:
        break;
}
```

---

## ECPay 金流整合

### 1. 取得付款 Token (view_payBill)
**位置**: `BillController.php:246-266`

```php
public function view_payBill($bill_id)
{
    $bill = Bill::where('bill_id', $bill_id)->firstOrFail();
    $ecpay = new ECPay($bill);

    // 取得 ECPay Token
    if (!$token = $ecpay->getToken()) {
        return $ecpay->errorMsg;
    }

    // 渲染付款頁面
    return view('bill.payBill_v2', [
        'bill_id' => $bill_id,
        'token' => $token,
        'ecpaySDKUrl' => $ecpay->getEcpaySDKUrl(),
    ]);
}
```

#### ECPay::getToken() 詳解
**位置**: `app/Helpers/ECPay.php:267-293`

```php
public function getToken()
{
    $this->setItemName(); // 組合商品名稱

    // 發送 API 請求
    $curl = $this->getCurlRequest(
        $this->endpoint_GetTokenbyTrade,
        $this->getBody_TradeToken()
    );

    $res = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        Log::info($err);
        $this->errorMsg = $err;
        return null;
    }

    // 解析回應
    $res = json_decode($res, true);
    if (!isset($res['Data'])) { return null; }

    // 解密資料
    $Data = $this->string2DecryptedArray($res['Data']);

    // 驗證回應
    if (!isset($Data['RtnCode']) || !isset($Data['Token'])) {
        return null;
    }

    if ($Data['RtnCode'] != 1) {
        $this->errorMsg = $Data['RtnMsg'];
        return null;
    }

    return $Data['Token'];
}
```

#### Token Request Body 結構
**位置**: `ECPay.php:195-228`

```php
private function getBody_TradeToken()
{
    $Data = [
        'MerchantID' => $this->MerchantID,
        'RememberCard' => 1,           // 記憶卡號
        'PaymentUIType' => 2,          // 畫面呈現方式
        'ChoosePaymentList' => "1,3",  // 1=信用卡, 3=ATM

        'OrderInfo' => [
            "MerchantTradeNo" => $this->MerchantTradeNo,
            "MerchantTradeDate" => $this->MerchantTradeDate,
            "TotalAmount" => $this->TotalAmount,
            "ReturnURL" => $this->ReturnURL,      // Webhook URL
            'TradeDesc' => $this->TradeDesc,
            'ItemName' => $this->ItemName
        ],

        'CardInfo' => [
            'OrderResultURL' => $this->OrderResultURL,  // 3D驗證回傳URL
        ],

        'ATMInfo' => [
            'ExpireDate' => 3  // 繳費期限3天
        ],

        'ConsumerInfo' => [
            "MerchantMemberID" => $this->MerchantMemberID,  // USER_{user_id}
            "Email" => $this->Email,
            "Phone" => $this->Phone,
            "Name" => $this->Name,
            "CountryCode" => 'TW',
        ]
    ];

    // AES 加密
    $Data = $this->array2EncryptedString($Data);

    return json_encode([
        'MerchantID' => $this->MerchantID,
        'RqHeader' => [
            'Timestamp' => time(),
            'Revision' => '1.0.0',
        ],
        'Data' => $Data
    ]);
}
```

### 2. 建立付款交易 (payBill)
**位置**: `BillController.php:268-284`

```php
public function payBill(Request $request, $bill_id)
{
    $bill = Bill::where('bill_id', $bill_id)->firstOrFail();

    if (!$request->has('PayToken')) {
        return '錯誤頁面。';
    }

    $ecpay = new ECPay($bill);

    // 建立付款交易
    $resultUrl = $ecpay->createPayment($request->PayToken);

    if (!$resultUrl) {
        return '錯誤頁面';
    }

    // 導向 ECPay 付款頁面或結果頁面
    return redirect($resultUrl);
}
```

#### ECPay::createPayment() 詳解
**位置**: `ECPay.php:300-346`

```php
public function createPayment(string $PayToken)
{
    // 發送 API 請求
    $curl = $this->getCurlRequest(
        $this->endpoint_CreatePayment,
        $this->getBody_CreatePayment($PayToken)
    );

    $res = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        Log::info($err);
        return null;
    }

    $res = json_decode($res, true);

    // 記錄付款日誌
    PaymentLog::insert_row(
        $this->bill->id,
        PaymentLog::TYPE_CREATE_PAYMENT,
        $res['TransCode'],
        $res['TransMsg'],
        $res['Data']
    );

    if ($res['TransCode'] != 1) { return null; }

    $Data = $this->string2DecryptedArray($res['Data']);

    // 如果需要 3D 驗證
    if (!is_null($Data['ThreeDInfo']['ThreeDURL'])) {
        $this->ThreeDURL = $Data['ThreeDInfo']['ThreeDURL'];
        return $this->ThreeDURL;  // 導向 3D 驗證頁面
    }

    // 根據付款方式導向不同頁面
    switch ($Data['OrderInfo']['PaymentType']) {
        case 'Credit':
            return route('billThankyou', ['bill_id' => $this->bill->bill_id]);
        case 'ATM':
            return route('billDetail', ['bill_id' => $this->bill->bill_id]);
        default:
            return null;
    }
}
```

### 3. 加密/解密機制
**位置**: `ECPay.php:141-157`

```php
// AES-128-CBC 加密
private function array2EncryptedString(array $array)
{
    $string = json_encode($array);
    $string = urlencode($string);
    $string = openssl_encrypt($string, "AES-128-CBC",
                              $this->HashKey, 0, $this->HashIV);
    return $string;
}

// AES-128-CBC 解密
function string2DecryptedArray(string $string)
{
    $string = openssl_decrypt($string, "AES-128-CBC",
                              $this->HashKey, 0, $this->HashIV);
    $array = json_decode(urldecode($string), true);
    return $array;
}
```

---

## 付款方式處理

### 1. 信用卡付款流程

```
用戶選擇信用卡
    ↓
view_payBill() → 取得 Token
    ↓
渲染付款頁面 (載入 ECPay SDK)
    ↓
用戶輸入卡號 → ECPay 前端驗證
    ↓
payBill() → createPayment(PayToken)
    ↓
[需要3D驗證] → 導向 ThreeDURL
    ↓
3D驗證完成 → OrderResultURL
    ↓
api_ecpay_pay() ← ECPay Webhook 回呼
    ↓
更新訂單狀態 → 發送紅利 → 開立發票
    ↓
view_billThankyou() → 顯示感謝頁面 + GA4 追蹤
```

### 2. ATM 轉帳流程

```
用戶選擇 ATM
    ↓
view_payBill() → 取得 Token
    ↓
payBill() → createPayment(PayToken)
    ↓
取得虛擬帳號資訊
    ↓
view_billDetail() → 顯示轉帳資訊
  - BankCode (銀行代碼)
  - vAccount (虛擬帳號)
  - ExpireDate (繳費期限)
    ↓
用戶完成轉帳
    ↓
api_ecpay_pay() ← ECPay Webhook 回呼
    ↓
更新訂單狀態 → 發送紅利 → 開立發票 → 發送 GA4 事件
    ↓
sendMail() → 發送 ATM 繳費確認信
```

### 3. 貨到付款/超商代收

```
用戶選擇貨到付款/全家超商
    ↓
store() → 建立訂單
    ↓
直接導向 view_billThankyou()
    ↓
顯示感謝頁面 + GA4 追蹤
```

---

## Webhook 回呼機制

### 付款完成 Webhook (api_ecpay_pay)
**位置**: `BillController.php:286-333`

這是 **ECPay 最重要的回呼端點**，用於接收付款狀態通知。

```php
public function api_ecpay_pay(Request $request, $bill_id)
{
    $bill = Bill::where('bill_id', $bill_id)->firstOrFail();
    $ecpay = new ECPay($bill);

    // 驗證付款請求 (含簽章驗證)
    $isSuccess = $ecpay->handlePayRequest($request);

    if ($isSuccess) {
        // 更新訂單狀態為已付款
        $bill->status = 1;
        $bill->save();

        // 發送紅利點數給買家
        $bill->sendBonusToBuyer();

        // 開立電子發票 (異步處理)
        dispatch(new ECPayInvoice($bill, ECPayInvoice::TYPE_ISSUE));

        // ATM 付款特殊處理：後端發送 GA4 事件
        if ($bill->pay_by == Bill::PAY_BY_ATM) {
            try {
                $gaService = new GoogleAnalyticsService();
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

    // 記錄綠界回傳資料
    Log::info("-----綠界回傳-----");
    Log::info("訂單編號：" . $bill_id);
    Log::info(json_encode($request->all()));
    Log::info("-----------------");

    // 回傳成功確認給 ECPay
    return "1|OK";
}
```

#### handlePayRequest() 驗證機制
**位置**: `ECPay.php:353-372`

```php
public function handlePayRequest(Request $request)
{
    $res = json_decode($request->getContent(), true);

    // 驗證必要欄位
    if (!isset($res['TransCode']) ||
        !isset($res['TransMsg']) ||
        !isset($res['Data'])) {
        return false;
    }

    // 記錄付款請求日誌
    PaymentLog::insert_row(
        $this->bill->id,
        PaymentLog::TYPE_PAY_REQUEST,
        $res['TransCode'],
        $res['TransMsg'],
        $res['Data']
    );

    // 解密資料
    $data = $this->string2DecryptedArray($res['Data']);

    // 驗證回傳碼
    if (!isset($data['RtnCode'])) { return false; }

    if ($data['RtnCode'] == 1) {
        return true;  // 付款成功
    }

    return false;
}
```

### 路由配置
**位置**: `routes/web.php`

```php
// ECPay 付款回呼
Route::post('api/ecpay/pay/{bill_id}', 'BillController@api_ecpay_pay')
    ->name('ecpay_ReturnURL');

// 信用卡 3D 驗證回傳
Route::post('api/ecpay/thankyou/{bill_id}', 'BillController@view_ecpay_thankyouPage')
    ->name('ecpay_OrderResultURL');
```

---

## 發票開立流程

### 1. 發票 Job 觸發
**位置**: `BillController.php:296`

```php
// 異步開立發票
dispatch(new ECPayInvoice($bill, ECPayInvoice::TYPE_ISSUE));
```

### 2. ECPayInvoice Job
**位置**: `app/Jobs/ECPayInvoice.php`

```php
class ECPayInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TYPE_ISSUE = 'issue';    // 開立發票
    const TYPE_INVALID = 'invalid'; // 作廢發票
    const TYPE_ALLOWANCE = 'allowance'; // 折讓

    protected $bill;
    protected $type;

    public function __construct(Bill $bill, $type = self::TYPE_ISSUE)
    {
        $this->bill = $bill;
        $this->type = $type;
    }

    public function handle()
    {
        // 根據類型執行不同發票操作
        switch ($this->type) {
            case self::TYPE_ISSUE:
                $this->issueInvoice();
                break;
            case self::TYPE_INVALID:
                $this->invalidInvoice();
                break;
            // ...
        }
    }

    private function issueInvoice()
    {
        // 呼叫 ECPay 發票 API
        // 使用 storage/invoice/ecpay_invoice_issue.php
    }
}
```

---

## GA4 追蹤整合

### 1. 前端追蹤 (所有付款方式)
**位置**: `BillController.php:354-392`

```php
public function view_billThankyou($bill_id)
{
    $bill = Bill::where('bill_id', $bill_id)->firstOrFail();
    $products = $bill->products();

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

    return view('bill.thankyou', [
        'bill' => $bill,
        'products' => $products,
        'gaData' => $gaData,  // 傳遞給前端執行 gtag()
    ]);
}
```

### 2. 後端追蹤 (ATM 付款)
**位置**: `BillController.php:299-316`

```php
// ATM 交易成功時，透過後端發送 GA 購買轉換事件
if ($bill->pay_by == Bill::PAY_BY_ATM) {
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
```

**原因**: ATM 付款完成時用戶不在網站上，無法透過前端發送事件，因此需要後端主動發送。

### 3. GA4 DataLayer API
**位置**: `BillController.php:439-472`

```php
public function getDataLayerForGA($bill_id)
{
    $bill = Bill::where('bill_id', $bill_id)->firstOrFail();
    $items = json_decode($bill->item, true);

    $products = [];
    foreach ($items as $item) {
        $product = Products::where('slug', $item['slug'])->first();

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
```

---

## 紅利點數系統

### 1. 紅利使用 (結帳時扣除)
**位置**: `BillController.php:162-168`

```php
$bonus = $request->bonus;

// 驗證規則
if ($bonus > $user->bonus) {
    $bonus = $user->bonus;  // 不能超過用戶持有點數
}
if (fmod($bonus, 50) != 0) {
    $bonus = $bonus - fmod($bonus, 50);  // 必須是 50 的倍數
}
if ($bonus / 50 > $total) {
    $bonus = $total * 50;  // 折扣不能超過訂單金額
}
if ($bonus < 0) {
    $bonus = 0;
}

// 計算折扣金額 (50點 = 1元)
$useBonus = $bonus / 50;
$total = $total - $useBonus;
```

### 2. 紅利扣除
**位置**: `BillController.php:203-205`

```php
if ($bonus != 0) {
    $user->updateBonus($bonus);  // 扣除使用的點數
}
```

### 3. 紅利回饋 (付款完成)
**位置**: `BillController.php:295`

```php
// 發送紅利點數給買家
$bill->sendBonusToBuyer();
```

**位置**: `app/Bill.php` (推測)

```php
public function sendBonusToBuyer()
{
    if (!$this->user_id) return;

    $user = User::find($this->user_id);
    if (!$user) return;

    // 將訂單的 get_bonus 加到用戶帳戶
    $user->bonus = $user->bonus + $this->get_bonus;
    $user->save();
}
```

### 4. 取消訂單退還紅利
**位置**: `BillController.php:877-895`

```php
public function cancelBill($id)
{
    $bill = Bill::where('bill_id', $id)->firstOrFail();
    $user = Auth::user();

    // 權限檢查
    if ($bill->user_id != $user->id) {
        return response()->json('error');
    }

    // 狀態檢查
    if ($bill->status == 1 || $bill->shipment != 0) {
        return response()->json('error');
    }

    // 退還紅利點數 (使用點數 * 50)
    $amount = $bill->bonus_use * 50;
    $user->updateBonus($amount, false);

    // 更新訂單為作廢
    $bill->updateShipment(Bill::SHIPMENT_VOID);

    return response()->json('success');
}
```

---

## 環境配置

### 1. 測試環境 vs 正式環境
**位置**: `ECPay.php:89-92`

```php
public function __construct(Bill $bill)
{
    // 根據環境切換 API 端點
    if (config('app.env') == "production") {
        $this->endpoint_GetTokenbyTrade = "https://ecpg.ecpay.com.tw/Merchant/GetTokenbyTrade";
        $this->endpoint_CreatePayment = "https://ecpg.ecpay.com.tw/Merchant/CreatePayment";
    } else {
        // 測試環境 (預設)
        $this->endpoint_GetTokenbyTrade = "https://ecpg-stage.ecpay.com.tw/Merchant/GetTokenbyTrade";
        $this->endpoint_CreatePayment = "https://ecpg-stage.ecpay.com.tw/Merchant/CreatePayment";
    }
}
```

### 2. ECPay SDK URL
**位置**: `ECPay.php:412-417`

```php
public function getEcpaySDKUrl()
{
    if (config('app.env') == "production") {
        return "https://ecpg.ecpay.com.tw/Scripts/sdk-1.0.0.js?t=20210121100116";
    }
    return "https://ecpg-stage.ecpay.com.tw/Scripts/sdk-1.0.0.js?t=20210121100116";
}
```

### 3. 環境變數設定
**位置**: `.env`

```env
APP_ENV=production

# ECPay 設定
ECPAY_MERCHANT_ID=your_merchant_id
ECPAY_HASH_KEY=your_hash_key
ECPAY_HASH_IV=your_hash_iv

# GA4 追蹤
GTM_ID=GTM-XXXXXXX
GA_ID=G-XXXXXXXXXX
```

---

## 錯誤處理

### 1. Token 取得失敗
**位置**: `BillController.php:255-257`

```php
if (!$token = $ecpay->getToken()) {
    return $ecpay->errorMsg;  // 顯示錯誤訊息
}
```

### 2. 付款建立失敗
**位置**: `BillController.php:282-283`

```php
if (!$resultUrl) {
    return '錯誤頁面';
}
```

### 3. Webhook 驗證失敗
**位置**: `ECPay.php:353-372`

```php
public function handlePayRequest(Request $request)
{
    $res = json_decode($request->getContent(), true);

    // 必要欄位檢查
    if (!isset($res['TransCode']) ||
        !isset($res['TransMsg']) ||
        !isset($res['Data'])) {
        return false;
    }

    // 記錄所有請求
    PaymentLog::insert_row(...);

    // 解密並驗證
    $data = $this->string2DecryptedArray($res['Data']);
    if (!isset($data['RtnCode'])) { return false; }

    return ($data['RtnCode'] == 1);
}
```

### 4. 日誌記錄
**位置**: `BillController.php:326-329`

```php
Log::info("-----綠界回傳-----");
Log::info("訂單編號：" . $bill_id);
Log::info(json_encode($request->all()));
Log::info("-----------------");
```

### 5. GA4 錯誤處理
**位置**: `BillController.php:310-315`

```php
try {
    $gaService = new GoogleAnalyticsService();
    $gaService->sendPurchaseEvent($bill, $clientId);
} catch (\Exception $e) {
    Log::error("Failed to send GA Purchase Event for ATM payment", [
        'bill_id' => $bill_id,
        'error' => $e->getMessage()
    ]);
}
```

---

## 時序圖

### 完整付款流程時序圖

```
用戶                前端                BillController          ECPay Helper        ECPay API         Database
 │                  │                       │                      │                   │                 │
 │ 提交結帳表單      │                       │                      │                   │                 │
 ├─────────────────>│                       │                      │                   │                 │
 │                  │ POST /bill/store      │                      │                   │                 │
 │                  ├──────────────────────>│                      │                   │                 │
 │                  │                       │ 驗證資料               │                   │                 │
 │                  │                       │ 計算金額               │                   │                 │
 │                  │                       │ 處理紅利               │                   │                 │
 │                  │                       │                      │                   │                 │
 │                  │                       │ INSERT bill          │                   │                 │
 │                  │                       ├─────────────────────────────────────────>│
 │                  │                       │ INSERT bill_items    │                   │                 │
 │                  │                       ├─────────────────────────────────────────>│
 │                  │                       │ 清除購物車             │                   │                 │
 │                  │                       ├─────────────────────────────────────────>│
 │                  │                       │ 扣除紅利               │                   │                 │
 │                  │                       ├─────────────────────────────────────────>│
 │                  │                       │                      │                   │                 │
 │                  │ redirect /bill/pay    │                      │                   │                 │
 │                  │<──────────────────────┤                      │                   │                 │
 │                  │                       │                      │                   │                 │
 │ 導向付款頁面      │                       │                      │                   │                 │
 │<─────────────────┤                       │                      │                   │                 │
 │                  │ GET /bill/pay/{id}    │                      │                   │                 │
 │                  ├──────────────────────>│                      │                   │                 │
 │                  │                       │ new ECPay($bill)     │                   │                 │
 │                  │                       ├─────────────────────>│                   │                 │
 │                  │                       │ getToken()           │                   │                 │
 │                  │                       ├─────────────────────>│                   │                 │
 │                  │                       │                      │ GetTokenbyTrade   │                 │
 │                  │                       │                      ├──────────────────>│
 │                  │                       │                      │ Token             │                 │
 │                  │                       │                      │<──────────────────┤
 │                  │                       │ return token         │                   │                 │
 │                  │                       │<─────────────────────┤                   │                 │
 │                  │ 渲染付款頁面 + SDK     │                      │                   │                 │
 │                  │<──────────────────────┤                      │                   │                 │
 │ 顯示付款表單      │                       │                      │                   │                 │
 │<─────────────────┤                       │                      │                   │                 │
 │                  │ 載入 ECPay SDK        │                      │                   │                 │
 │                  │<──────────────────────────────────────────────────────────────────┤
 │                  │                       │                      │                   │                 │
 │ 輸入信用卡資料    │                       │                      │                   │                 │
 ├─────────────────>│                       │                      │                   │                 │
 │                  │ ECPay.createToken()   │                      │                   │                 │
 │                  ├────────────────────────────────────────────────────────────────> │
 │                  │                       │                      │   PayToken        │                 │
 │                  │<──────────────────────────────────────────────────────────────────┤
 │                  │                       │                      │                   │                 │
 │                  │ POST /bill/pay/{id}   │                      │                   │                 │
 │                  │ {PayToken}            │                      │                   │                 │
 │                  ├──────────────────────>│                      │                   │                 │
 │                  │                       │ createPayment()      │                   │                 │
 │                  │                       ├─────────────────────>│                   │                 │
 │                  │                       │                      │ CreatePayment     │                 │
 │                  │                       │                      ├──────────────────>│
 │                  │                       │                      │                   │ INSERT log      │
 │                  │                       │                      ├─────────────────────────────────────>│
 │                  │                       │                      │ ThreeDURL/Result  │                 │
 │                  │                       │                      │<──────────────────┤
 │                  │                       │ return URL           │                   │                 │
 │                  │                       │<─────────────────────┤                   │                 │
 │                  │ redirect URL          │                      │                   │                 │
 │                  │<──────────────────────┤                      │                   │                 │
 │                  │                       │                      │                   │                 │
 │ 3D驗證頁面        │                       │                      │                   │                 │
 │<─────────────────┤                       │                      │                   │                 │
 │ 輸入驗證碼        │                       │                      │                   │                 │
 ├─────────────────>│                       │                      │                   │                 │
 │                  │ 驗證成功               │                      │                   │                 │
 │                  │                       │                      │                   │                 │
 │                  │                       │  ┌──────────────────ECPay 異步回呼────────┐│                 │
 │                  │                       │  │                  │                   │                 │
 │                  │                       │<─┤ POST /api/ecpay/pay/{id}              │                 │
 │                  │                       │  │                  │                   │                 │
 │                  │                       │  │ handlePayRequest()│                   │                 │
 │                  │                       ├──┼─────────────────>│                   │                 │
 │                  │                       │  │                  │ 解密驗證           │                 │
 │                  │                       │  │ return true      │                   │                 │
 │                  │                       │<─┼──────────────────┤                   │                 │
 │                  │                       │  │                  │                   │ INSERT log      │
 │                  │                       ├──┼───────────────────────────────────────────────────────>│
 │                  │                       │  │ UPDATE status=1  │                   │                 │
 │                  │                       ├──┼───────────────────────────────────────────────────────>│
 │                  │                       │  │ 發送紅利          │                   │                 │
 │                  │                       ├──┼───────────────────────────────────────────────────────>│
 │                  │                       │  │ dispatch(發票Job) │                   │                 │
 │                  │                       ├──┼───────────────────────────────────────────────────────>│
 │                  │                       │  │                  │                   │                 │
 │                  │                       │  │ [ATM only] 發送GA4│                   │                 │
 │                  │                       │  │                  │                   │                 │
 │                  │                       ├──┤ return "1|OK"    │                   │                 │
 │                  │                       │  │                  │                   │                 │
 │                  │                       │  └──────────────────────────────────────┘│                 │
 │                  │                       │                      │                   │                 │
 │ 導向感謝頁面      │                       │                      │                   │                 │
 │<─────────────────┤                       │                      │                   │                 │
 │                  │ GET /bill/thankyou    │                      │                   │                 │
 │                  ├──────────────────────>│                      │                   │                 │
 │                  │                       │ SELECT bill          │                   │                 │
 │                  │                       ├─────────────────────────────────────────>│
 │                  │                       │ 準備 GA4 DataLayer   │                   │                 │
 │                  │ 渲染感謝頁面 + GA     │                      │                   │                 │
 │                  │<──────────────────────┤                      │                   │                 │
 │ 顯示訂單完成      │                       │                      │                   │                 │
 │<─────────────────┤                       │                      │                   │                 │
 │                  │ gtag('event','purchase')                     │                   │                 │
 │                  ├────────────────────────────────────────────────────────────────> GA4              │
```

### ATM 付款特殊流程

```
用戶                BillController          ECPay              Database           GoogleAnalytics
 │                       │                     │                  │                      │
 │ 選擇 ATM 付款          │                     │                  │                      │
 ├──────────────────────>│                     │                  │                      │
 │                       │ 取得虛擬帳號資訊     │                  │                      │
 │                       ├────────────────────>│                  │                      │
 │                       │ 回傳帳號資訊         │                  │                      │
 │                       │<────────────────────┤                  │                      │
 │ 顯示轉帳資訊          │                     │                  │                      │
 │<──────────────────────┤                     │                  │                      │
 │                       │                     │                  │                      │
 │ [離開網站去轉帳]       │                     │                  │                      │
 │                       │                     │                  │                      │
 │ 完成ATM轉帳            │                     │                  │                      │
 ├───────────────────────────────────────────>│                  │                      │
 │                       │                     │                  │                      │
 │                       │  ← ECPay Webhook ── │                  │                      │
 │                       │<────────────────────┤                  │                      │
 │                       │ 更新訂單狀態         │                  │                      │
 │                       ├─────────────────────────────────────> │                      │
 │                       │ 發送紅利             │                  │                      │
 │                       ├─────────────────────────────────────> │                      │
 │                       │ 開立發票 (Job)       │                  │                      │
 │                       ├─────────────────────────────────────> │                      │
 │                       │                     │                  │                      │
 │                       │ 後端發送 GA4 事件    │                  │                      │
 │                       ├──────────────────────────────────────────────────────────────>│
 │                       │ (因用戶不在網站上)   │                  │                      │
 │                       │                     │                  │                      │
 │                       │ sendMail()          │                  │                      │
 │ ← 收到ATM繳費確認信 ───┤                     │                  │                      │
```

---

## 重要注意事項

### 1. 安全性
- ✅ 所有 ECPay 請求都使用 AES-128-CBC 加密
- ✅ Webhook 需驗證簽章 (handlePayRequest)
- ⚠️ 建議加強 CSRF 保護
- ⚠️ 建議對 webhook 端點進行 IP 白名單限制

### 2. 冪等性
- ⚠️ `api_ecpay_pay()` 可能被重複呼叫，需確保冪等性
- 建議檢查訂單狀態，避免重複處理：
```php
if ($bill->status == 1) {
    return "1|OK";  // 已處理過
}
```

### 3. 日誌記錄
- ✅ 所有 ECPay 請求都記錄在 `PaymentLog`
- ✅ Webhook 回傳資料記錄在 Laravel Log
- 建議: 定期清理舊日誌

### 4. 異步處理
- ✅ 發票開立使用 Laravel Queue (ECPayInvoice Job)
- 需確保 Queue Worker 正常運行：
```bash
php artisan queue:work
```

### 5. 測試建議
- 使用 ECPay 測試環境測試完整流程
- 測試各種付款方式 (信用卡/ATM/3D驗證)
- 測試 Webhook 重複呼叫情況
- 測試紅利點數計算邊界條件

---

## 開發者檢查清單

### 部署前檢查
- [ ] `.env` 設定正確的 ECPay 金鑰
- [ ] 確認環境變數 `APP_ENV` 設定正確
- [ ] ECPay 後台設定正確的 ReturnURL 和 OrderResultURL
- [ ] Queue Worker 服務正常運行
- [ ] 日誌目錄可寫入
- [ ] GA4 追蹤碼設定正確

### 監控要點
- [ ] 監控付款成功率
- [ ] 監控 Webhook 失敗率
- [ ] 監控發票開立成功率
- [ ] 監控 GA4 事件發送成功率
- [ ] 監控紅利點數異常變動

---

## 結帳流程追蹤系統

### 概述

為了精確追蹤消費者在結帳付款流程中的行為，系統整合了完整的流程追蹤機制。這讓您能夠：

- 🔍 **定位流失點** - 找出消費者在哪個步驟離開
- 📊 **量化轉換率** - 計算每個步驟的轉換率和流失率
- ⚠️ **追蹤錯誤** - 記錄系統錯誤和用戶操作問題
- 📈 **優化流程** - 基於數據改善用戶體驗

### 追蹤節點整合位置

系統在以下關鍵位置已整合追蹤點：

| 追蹤節點 | 位置 | 說明 |
|---------|------|------|
| `cart_view` | `kartController.php:109` | 用戶查看購物車 |
| `checkout_form_submit` | `BillController.php:80` | 提交結帳表單 |
| `order_created` | `BillController.php:218` | 訂單建立成功 |
| `payment_page_view` | `BillController.php:287` | 進入付款頁面 |
| `payment_token_requested` | `BillController.php:298` | 請求 ECPay Token |
| `payment_token_received` | `BillController.php:320` | 成功取得 Token |
| `payment_form_submit` | `BillController.php:339` | 提交付款表單 |
| `payment_redirect` | `BillController.php:377` | 導向 ECPay |
| `payment_completed` | `BillController.php:395` | 付款完成 (Webhook) |
| `thankyou_page_view` | `BillController.php:467` | 顯示感謝頁面 |

### 追蹤範例

#### 成功流程追蹤
```php
// 訂單建立成功時
CheckoutFunnelTracker::trackFromBill(
    CheckoutFunnelLog::STEP_ORDER_CREATED,
    $bill,
    $request
);
```

#### 錯誤追蹤
```php
// Token 取得失敗時
CheckoutFunnelTracker::trackError(
    CheckoutFunnelLog::STEP_PAYMENT_TOKEN_REQUESTED,
    'ECPay Token取得失敗: ' . $ecpay->errorMsg,
    request(),
    [
        'bill_id' => $bill->bill_id,
        'payment_method' => $bill->pay_by,
        'amount' => $bill->price
    ]
);
```

### 查看分析報表

管理員可透過以下路徑查看完整的漏斗分析：

```
https://your-domain.com/admin/funnel-analytics
```

報表功能包含：
- 📊 漏斗轉換圖表
- 📉 各步驟流失率分析
- ❌ 錯誤類型統計
- 💳 依付款方式分組分析
- 📅 每日趨勢變化
- 📥 CSV 數據匯出

### 前端整合

在購物車和結帳相關頁面載入追蹤腳本：

```html
<script src="{{ asset('js/checkout-funnel-tracker.js') }}"></script>
```

前端會自動追蹤：
- 頁面瀏覽事件
- 按鈕點擊行為
- 表單驗證錯誤
- 付款方式變更

### 資料表結構

追蹤數據儲存在 `checkout_funnel_logs` 表：

| 欄位 | 類型 | 說明 |
|------|------|------|
| `session_id` | string | 用戶 Session ID |
| `user_id` | int | 用戶 ID (可為空) |
| `bill_id` | string | 訂單編號 |
| `step` | string | 流程步驟 |
| `status` | string | 狀態 (success/error/abandoned) |
| `error_message` | text | 錯誤訊息 |
| `metadata` | json | 額外資料 |
| `ip_address` | string | IP 位址 |
| `user_agent` | text | User Agent |
| `payment_method` | string | 付款方式 |
| `amount` | int | 金額 |
| `created_at` | timestamp | 建立時間 |

### 常見分析使用情境

#### 1. 找出付款頁面問題
```sql
SELECT error_message, COUNT(*) as count
FROM checkout_funnel_logs
WHERE step IN ('payment_token_requested', 'payment_form_submit')
  AND status = 'error'
GROUP BY error_message
ORDER BY count DESC;
```

#### 2. 分析付款方式轉換率
```php
$funnelData = CheckoutFunnelLog::getFunnelByPaymentMethod(
    Carbon::now()->subDays(7),
    Carbon::now()
);
```

#### 3. 找出放棄的訂單
```php
$abandonedSessions = CheckoutFunnelTracker::getAbandonedSessions(30);
```

### 整合效益

1. **精準定位問題**
   - 快速找出流程中斷點
   - 辨識技術錯誤 vs UX 問題

2. **數據驅動優化**
   - A/B 測試效果追蹤
   - 量化改善成效

3. **主動監控預警**
   - 即時發現異常流失
   - 付款成功率監控

4. **商業決策支援**
   - 付款方式策略調整
   - 結帳流程優化依據

### 相關文件

詳細使用說明請參考：[結帳流程追蹤系統使用指南](CHECKOUT_FUNNEL_TRACKING.md)

---

## 版本資訊
- **Laravel**: 5.4
- **ECPay API**: 1.0.0
- **文件版本**: 1.1
- **最後更新**: 2025-10-05
- **新增功能**: 結帳流程追蹤系統
