<?php

namespace App;

use App\Helpers\ECPay;
use App\Jobs\ECPayInvoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Bill extends Model
{

    /**可準備 */
    const SHIPMENT_READY = 0;
    /**準備中 */
    const SHIPMENT_PENDING = 1;
    /**已出貨 */
    const SHIPMENT_DELIVERED = 2;
    /**結案 */
    const SHIPMENT_VOID = 3;

    /**信用卡 */
    const PAY_BY_CREDIT = 'CREDIT';
    /**ATM轉帳 */
    const PAY_BY_ATM = 'ATM';
    /**貨到付款 */
    const PAY_BY_COD = '貨到付款';
    /**全家代收 */
    const PAY_BY_FAMILY = 'FAMILY';

    /**黑貓 */
    const CARRIER_ID_BLACK_CAT = 0;
    const CARRIER_BLACK_CAT = '黑貓宅配';
    /**全家 */
    const CARRIER_ID_FAMILY_MART = 1;
    const CARRIER_FAMILY_MART = '全家冷凍超取';

    public function shipmentName() {
        switch ($this->shipment) {
            case static::SHIPMENT_READY:
                return "可準備";
            case static::SHIPMENT_PENDING:
                return "準備中";
            case static::SHIPMENT_DELIVERED:
                return "已出貨";
            case static::SHIPMENT_VOID:
                return "結案";
            default:
                return null;
        }
    }

    public static function getAllCarriers(){
        $carriers = [
            static::CARRIER_ID_BLACK_CAT => static::CARRIER_BLACK_CAT,
            static::CARRIER_ID_FAMILY_MART => static::CARRIER_FAMILY_MART,
        ];
        return $carriers;
    }

    public function familyStore(){
        return $this->hasOne('App\FamilyStore','bill_id','id');
    }

    public function paymentLogs(){
        return $this->hasMany('App\PaymentLog');
    }

    public function billItems(){
        return $this->hasMany('App\BillItem','bill_id','id');
    }
    
    public static function insert_row($user_id,$user_name,$bill_id,$useBonus,$total,$getBonus,Request $request){
        $bill = new Bill;
        $bill->user_id = $user_id;
        $bill->bill_id = $bill_id;
        $bill->user_name = $user_name;
        $bill->item = null;
        $bill->bonus_use = $useBonus;
        $bill->price = $total;
        //----------現時紅利加碼-----------
        $bill->get_bonus = $getBonus * 3;
        // $bill->get_bonus = $getBonus;
        //----------現時紅利加碼-----------

        $bill->ship_name = $request->ship_name;
        $bill->ship_gender = $request->ship_gender;
        $bill->ship_phone = $request->ship_phone;

        $bill->ship_county = $request->ship_county;
        $bill->ship_district = $request->ship_district;
        $bill->ship_address = $request->ship_address;
        if($request->carrier_id == Bill::CARRIER_ID_FAMILY_MART){
            $bill->ship_county = null;
            $bill->ship_district = null;
            $bill->ship_address = $request->store_name;
        }
        $bill->ship_email = $request->ship_email;
        $bill->ship_arrive = $request->ship_arrive;
        $bill->ship_arriveDate = $request->ship_arriveDate;
        $bill->ship_time = $request->ship_time;
        $bill->ship_receipt = $request->ship_receipt;
        $bill->ship_three_id = $request->ship_three_id;
        $bill->ship_three_company = $request->ship_three_company;
        $bill->ship_memo = $request->ship_memo;
        $bill->pay_by = $request->ship_pay_by;
        $bill->carrier_id = $request->carrier_id;
        if($request->ship_pay_by == 'cod'){
            $bill->pay_by = '貨到付款';
        }
        $bill->save();
        return $bill;
    }

    public function products(){
        if(is_null($this->item)){ return $this->billItems()->get(); }

        $items = json_decode($this->item,true);
        $slugArray = [];
        $quantityDict = [];
        foreach ($items as $item) {
            $quantityDict[$item['slug']] = $item['quantity'];
            $slugArray[] = $item['slug'];
        }
        $products = Products::whereIn('slug',$slugArray)->get();
        foreach ($products as $product) {
            if(!isset($quantityDict[$product->slug])){ continue; }
            $product->quantity = $quantityDict[$product->slug];
        }
        return $products;
    }

    /**發送這筆訂單可獲得的紅利點數給購買人 */
    public function sendBonusToBuyer(){
        if(empty($this->user_id)){ return; }
        $user = User::find($this->user_id);
        $user->updateBonus($this->get_bonus,false);
    }

    /**更新出貨狀態 */
    public function updateShipment(int $shipment){
        $this->shipment = $shipment;
        $this->save();
    }

    /**取得綠界金流的付款資訊 */
    public function getPaymentInfo($type = null){
        $ecpay = new ECPay($this);
        if(!$data = $ecpay->getPaymentInfo()){ return null; }
        if(!$type){ return $data; }
        if(!isset($data[$type])){ return null; }

        return (object)$data[$type];
    }

    /**是否為貨到收款的訂單 */
    public function isCodGroup(){
        if($this->pay_by == Bill::PAY_BY_COD || $this->pay_by == Bill::PAY_BY_FAMILY){
            return true;
        }
        return false;
    }

    /**下階段出貨狀態 */
    public function nextShipmentPhase(){

        //不是貨到收款 又還沒付錢的話
        if(!$this->isCodGroup() AND $this->status != 1){
            return;
        }

        if ($this->shipment == 0) {
            $this->shipment = 1;
            if ($this->isCodGroup()) {//如果是貨到付款
                dispatch(new ECPayInvoice($this,ECPayInvoice::TYPE_ISSUE)); //開立發票
            }  
        }elseif ($this->shipment == 1) {
            $this->shipment = 2;
            if ($this->isCodGroup()) {//如果是貨到付款->累計紅利    
                if($user = User::find($this->user_id)){
                    $user->updateBonus((int)$this->get_bonus,false);
                }
            }
        }
        // elseif ($this->shipment == 2) { //由於出貨即時開立發票所以這段先註解
        //     $this->shipment = 0;
        //     if ($this->isCodGroup()) {//如果是貨到付款->扣除紅利
        //         if($user = User::find($this->user_id)){
        //             $user->updateBonus((int)$this->get_bonus);
        //         }
        //     }
        // }
        $this->save();
    }

    /**訂單作廢（結案） */
    public function voidBill(){        
        
        //回補紅利
        if($this->bonus_use != 0){
            $amount = $this->bonus_use * 50;
            if($user = User::find($this->user_id)){
                $user->updateBonus($amount,false);
            }
        }

        //扣除紅利
        if($this->get_bonus != 0){
            if(($this->isCodGroup() && $this->shipment == 2) || $this->status == 1){
                if($user = User::find($this->user_id)){
                    $user->updateBonus((int)$this->get_bonus);
                }
            }
        }

        $this->updateShipment(Bill::SHIPMENT_VOID);
    }

    
}
