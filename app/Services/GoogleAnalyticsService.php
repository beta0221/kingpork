<?php

namespace App\Services;

use App\Bill;
use App\Products;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsService
{
    private $measurementId;
    private $apiSecret;
    private $endpoint;

    public function __construct()
    {
        $this->measurementId = config('app.ga_id');
        // GA4 Measurement Protocol 需要 API Secret，需要在 .env 中設定
        $this->apiSecret = config('app.ga_api_secret');
        $this->endpoint = 'https://www.google-analytics.com/mp/collect';
    }

    /**
     * 發送購買轉換事件到 GA4
     *
     * @param Bill $bill 訂單資料
     * @param string $clientId 用戶識別ID
     * @return bool 是否成功發送
     */
    public function sendPurchaseEvent(Bill $bill, $clientId = null)
    {
        // 只在正式環境發送
        if (config('app.env') !== 'production' || !$this->measurementId || !$this->apiSecret) {
            Log::info('GA Measurement Protocol: Not in production or missing config');
            return false;
        }

        // 如果沒有提供 clientId，生成一個
        if (!$clientId) {
            $clientId = $this->generateClientId();
        }

        // 準備商品資料
        $items = $this->preparePurchaseItems($bill);

        // 準備事件資料
        $eventData = [
            'client_id' => $clientId,
            'events' => [
                [
                    'name' => 'purchase',
                    'params' => [
                        'transaction_id' => $bill->bill_id,
                        'value' => (float) $bill->price,
                        'currency' => 'TWD',
                        'items' => $items,
                        'payment_type' => $this->getPaymentType($bill->pay_by)
                    ]
                ]
            ]
        ];

        return $this->sendToGA($eventData);
    }

    /**
     * 準備購買商品項目資料
     *
     * @param Bill $bill
     * @return array
     */
    private function preparePurchaseItems(Bill $bill)
    {
        $items = json_decode($bill->item, true);
        $gaItems = [];

        foreach ($items as $item) {
            $product = Products::where('slug', $item['slug'])->first();
            if ($product) {
                $category = $product->productCategory()->first();
                $gaItems[] = [
                    'item_name' => $product->name,
                    'item_id' => (string) $product->id,
                    'price' => (float) $product->price,
                    'item_category' => $category ? $category->name : '未分類',
                    'quantity' => (int) $item['quantity'],
                ];
            }
        }

        return $gaItems;
    }

    /**
     * 發送資料到 GA4 Measurement Protocol
     *
     * @param array $eventData
     * @return bool
     */
    private function sendToGA(array $eventData)
    {
        try {
            $response = Http::post($this->endpoint, [
                'measurement_id' => $this->measurementId,
                'api_secret' => $this->apiSecret,
            ])->withBody(json_encode($eventData), 'application/json');

            if ($response->successful()) {
                Log::info('GA Measurement Protocol: Purchase event sent successfully', [
                    'transaction_id' => $eventData['events'][0]['params']['transaction_id']
                ]);
                return true;
            } else {
                Log::error('GA Measurement Protocol: Failed to send purchase event', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('GA Measurement Protocol: Exception occurred', [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 生成客戶端 ID
     *
     * @return string
     */
    private function generateClientId()
    {
        return sprintf('%d.%d', 
            mt_rand(100000000, 999999999), 
            time()
        );
    }

    /**
     * 取得付款方式名稱
     *
     * @param mixed $payBy
     * @return string
     */
    private function getPaymentType($payBy)
    {
        switch ($payBy) {
            case \App\Bill::PAY_BY_CREDIT:
                return 'credit_card';
            case \App\Bill::PAY_BY_ATM:
                return 'atm';
            default:
                return 'unknown';
        }
    }
}