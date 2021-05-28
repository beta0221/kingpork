<?php

namespace App;

use App\Helpers\ECPay;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{

    const SHIPMENT_READY = 0;
    const SHIPMENT_PENDING = 1;
    const SHIPMENT_DELIVERED = 2;
    const SHIPMENT_VOID = 3;

    public function paymentLogs(){
        return $this->hasMany('App\PaymentLog');
    }
    
    public function products(){
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
    
}
