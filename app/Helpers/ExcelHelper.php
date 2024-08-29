<?php
namespace App\Helpers;

use App\Bill;
use App\BillItem;
use App\Products;
use Illuminate\Support\Facades\DB;

class ExcelHelper {

    
    public $erpIdList = [];
    public $products = [];
    public $orderList = [];
    public $undefinedErpIdList = [];

    private $data;
    private $dumpNum;
    

    public function __construct(Array $data) {

        $this->data = $data;
        $this->dumpNum = $this->genDumpNum();
        $this->arrangeData();
    }

    /** 檢查 ErpId 是否存在 */
    public function validateErpId() {
        if (empty($this->undefinedErpIdList)) {
            return null;
        }
        return array_unique($this->undefinedErpIdList);
    }

    /** 檢查訂單號碼是否已存在 */
    public function validateOrderNum($kol) {
        $orderNumList = array_keys($this->orderList);

        $kolOrderNumList = Bill::where('kol', $kol)
            ->whereIn('kolOrderNum', $orderNumList)
            ->groupBy('kolOrderNum')
            ->pluck('kolOrderNum');

        return empty($kolOrderNumList) ? null : $kolOrderNumList;
    }

    /** 存進資料庫 */
    public function save($kol) {

        $orderNumList = array_keys($this->orderList);
        foreach ($this->orderList as $orderNum => $order) {

            $index = array_search($orderNum, $orderNumList);
            DB::transaction(function() use ($kol, $order, $index){

                $total = array_reduce($order->products,function($carry, $product) {
                    $sumPrice = $product->price * (int)$product->quantity;
                    return $carry + $sumPrice;
                }, 0);                

                $bill = new Bill();
                $bill->bill_id = Bill::genMerchantTradeNo($index);
                $bill->user_name = $order->name;
                $bill->bonus_use = 0;
                $bill->price = $total;
                $bill->get_bonus = 0;
                $bill->ship_name = $order->name;
                $bill->ship_phone = $order->phone;
                $bill->ship_address = $order->address;
                $bill->ship_time = (is_null($order->shipTime) ? 'no' : $order->shipTime);
                $bill->ship_receipt = 2;
                $bill->ship_memo = $order->memo;
                $bill->pay_by = 'KOL';
                $bill->carrier_id = 0;
                $bill->kol = $kol;
                $bill->kolOrderNum = $order->orderNum;
                $bill->dumpNum = $this->dumpNum;
                $bill->save();

                $billItems = array_map(function($product){
                    return new BillItem([
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $product->quantity,
                        'short' => $product->short,
                        'description' => $product->discription
                    ]);
                }, $order->products);

                $bill->billItems()->saveMany($billItems);

            });    
        }

    }

    /** 產生批號代碼 */
    private function genDumpNum() {
        $timestamp = microtime(true) * 10000; // 使用微秒級時間戳
        $randomNumber = mt_rand(10000, 99999); // 生成隨機數
        return $timestamp . $randomNumber;
    }

    /**整理陣列 */
    private function arrangeData() {

        $_erpId = array_map(function($row){
            return $row[ExcelOrderModel::KEY_ERP_ID];
        }, $this->data);
        $this->erpIdList = array_values(array_unique($_erpId));

        $_products = Products::whereIn('erp_id', $this->erpIdList)->get();
        foreach ($_products as $_product) {
            $this->products[$_product->erp_id] = $_product;
        }

        $orderNum = null;
        foreach ($this->data as $row) {
            
            $_orderNum = $row[ExcelOrderModel::KEY_ORDER_NUM];

            if ($orderNum != $_orderNum) {
                $orderNum = $_orderNum;
                $this->orderList[$orderNum] = new ExcelOrderModel($row);
            }

            $erpId = $row[ExcelOrderModel::KEY_ERP_ID];
            $quantity = $row[ExcelOrderModel::KEY_QUANTITY];

            if (!isset($this->products[$erpId])) {
                $this->undefinedErpIdList[] = $erpId;
                continue;
            }

            $product = clone $this->products[$erpId];
            $product->quantity = $quantity;
            
            $this->orderList[$orderNum]->setItem($product);
        }
    }
}