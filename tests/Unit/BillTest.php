<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Bill;
use App\User;
use App\Products;
use App\BillItem;
use App\FamilyStore;
use App\PaymentLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;

class BillTest extends TestCase
{
    use DatabaseTransactions;

    public function testShipmentConstants()
    {
        $this->assertEquals(0, Bill::SHIPMENT_READY);
        $this->assertEquals(1, Bill::SHIPMENT_PENDING);
        $this->assertEquals(2, Bill::SHIPMENT_DELIVERED);
        $this->assertEquals(3, Bill::SHIPMENT_VOID);
    }

    public function testPaymentConstants()
    {
        $this->assertEquals('CREDIT', Bill::PAY_BY_CREDIT);
        $this->assertEquals('ATM', Bill::PAY_BY_ATM);
        $this->assertEquals('貨到付款', Bill::PAY_BY_COD);
        $this->assertEquals('FAMILY', Bill::PAY_BY_FAMILY);
        $this->assertEquals('KOL', Bill::PAY_BY_KOL);
    }

    public function testCarrierConstants()
    {
        $this->assertEquals(0, Bill::CARRIER_ID_BLACK_CAT);
        $this->assertEquals('冷凍宅配', Bill::CARRIER_BLACK_CAT);
        $this->assertEquals(1, Bill::CARRIER_ID_FAMILY_MART);
        $this->assertEquals('全家冷凍超取', Bill::CARRIER_FAMILY_MART);
    }

    public function testShipmentName()
    {
        $bill = new Bill();
        
        $bill->shipment = Bill::SHIPMENT_READY;
        $this->assertEquals('可準備', $bill->shipmentName());
        
        $bill->shipment = Bill::SHIPMENT_PENDING;
        $this->assertEquals('準備中', $bill->shipmentName());
        
        $bill->shipment = Bill::SHIPMENT_DELIVERED;
        $this->assertEquals('已出貨', $bill->shipmentName());
        
        $bill->shipment = Bill::SHIPMENT_VOID;
        $this->assertEquals('結案', $bill->shipmentName());
        
        $bill->shipment = 999;
        $this->assertNull($bill->shipmentName());
    }

    public function testGetAllCarriers()
    {
        $carriers = Bill::getAllCarriers();
        
        $this->assertCount(2, $carriers);
        $this->assertEquals('冷凍宅配', $carriers[0]);
        $this->assertEquals('全家冷凍超取', $carriers[1]);
    }

    public function testUpdateShipment()
    {
        $bill = factory(Bill::class)->create(['shipment' => Bill::SHIPMENT_READY]);
        
        $bill->updateShipment(Bill::SHIPMENT_PENDING);
        
        $this->assertEquals(Bill::SHIPMENT_PENDING, $bill->fresh()->shipment);
    }

    public function testGenMerchantTradeNo()
    {
        $tradeNo1 = Bill::genMerchantTradeNo();
        $tradeNo2 = Bill::genMerchantTradeNo();
        
        $this->assertInternalType('string', $tradeNo1);
        $this->assertInternalType('string', $tradeNo2);
        $this->assertNotEquals($tradeNo1, $tradeNo2);
        
        $tradeNoWithIndex = Bill::genMerchantTradeNo(5);
        $this->assertStringEndsWith('5', $tradeNoWithIndex);
    }

    public function testFamilyStoreRelationship()
    {
        $bill = factory(Bill::class)->create();
        $familyStore = factory(FamilyStore::class)->create(['bill_id' => $bill->id]);
        
        $this->assertEquals($familyStore->id, $bill->familyStore->id);
    }

    public function testPaymentLogsRelationship()
    {
        $bill = factory(Bill::class)->create();
        $paymentLog1 = factory(PaymentLog::class)->create(['bill_id' => $bill->id]);
        $paymentLog2 = factory(PaymentLog::class)->create(['bill_id' => $bill->id]);
        
        $paymentLogs = $bill->paymentLogs;
        
        $this->assertCount(2, $paymentLogs);
        $this->assertTrue($paymentLogs->contains('id', $paymentLog1->id));
        $this->assertTrue($paymentLogs->contains('id', $paymentLog2->id));
    }

    public function testBillItemsRelationship()
    {
        $bill = factory(Bill::class)->create();
        $billItem1 = factory(BillItem::class)->create(['bill_id' => $bill->id]);
        $billItem2 = factory(BillItem::class)->create(['bill_id' => $bill->id]);
        
        $billItems = $bill->billItems;
        
        $this->assertCount(2, $billItems);
        $this->assertTrue($billItems->contains('id', $billItem1->id));
        $this->assertTrue($billItems->contains('id', $billItem2->id));
    }

    public function testGetErpCustomerId()
    {
        $bill = new Bill();
        
        $bill->ship_receipt = 3;
        $this->assertNull($bill->getErpCustomerId());
        
        $bill->ship_receipt = 1;
        $bill->pay_by = Bill::PAY_BY_CREDIT;
        $this->assertEquals('0511', $bill->getErpCustomerId());
        
        $bill->pay_by = Bill::PAY_BY_ATM;
        $this->assertEquals('05112', $bill->getErpCustomerId());
        
        $bill->pay_by = Bill::PAY_BY_COD;
        $this->assertEquals('0512', $bill->getErpCustomerId());
        
        // Test kol logic - need to set pay_by to something that doesn't match first switch
        $bill->pay_by = 'OTHER';
        $bill->kol = 'waymay';
        $this->assertEquals('90483457', $bill->getErpCustomerId());
        
        $bill->kol = 'bawmami';
        $this->assertEquals('40980579', $bill->getErpCustomerId());
    }

    public function testGetErpDeparmentName()
    {
        $bill = new Bill();
        
        $bill->kol = 'waymay';
        $this->assertEquals('為美', $bill->getErpDeparmentName());
        
        $bill->kol = 'bawmami';
        $this->assertEquals('寶媽咪', $bill->getErpDeparmentName());
        
        $bill->kol = null;
        $this->assertEquals('官網', $bill->getErpDeparmentName());
    }

    public function testGetVendorName()
    {
        $this->assertEquals('為美', Bill::getVendorName('waymay'));
        $this->assertEquals('寶媽咪', Bill::getVendorName('bawmami'));
        $this->assertEquals('官網', Bill::getVendorName('unknown'));
        $this->assertEquals('官網', Bill::getVendorName(null));
    }

    public function testGetShiptimeId()
    {
        $bill = new Bill();
        
        $bill->ship_time = '13點前';
        $this->assertEquals('1', $bill->getShiptimeId());
        
        $bill->ship_time = '1';
        $this->assertEquals('1', $bill->getShiptimeId());
        
        $bill->ship_time = 1;
        $this->assertEquals('1', $bill->getShiptimeId());
        
        $bill->ship_time = '14-18點';
        $this->assertEquals('2', $bill->getShiptimeId());
        
        $bill->ship_time = '2';
        $this->assertEquals('2', $bill->getShiptimeId());
        
        $bill->ship_time = 2;
        $this->assertEquals('2', $bill->getShiptimeId());
        
        $bill->ship_time = '19點後';
        $this->assertEquals('4', $bill->getShiptimeId());
        
        $bill->ship_time = 'unknown';
        $this->assertEquals('4', $bill->getShiptimeId());
    }

    public function testProductsWithBillItems()
    {
        $bill = factory(Bill::class)->create(['item' => null]);
        $product1 = factory(Products::class)->create();
        $product2 = factory(Products::class)->create();
        
        factory(BillItem::class)->create(['bill_id' => $bill->id, 'product_id' => $product1->id]);
        factory(BillItem::class)->create(['bill_id' => $bill->id, 'product_id' => $product2->id]);
        
        $products = $bill->products();
        
        $this->assertCount(2, $products);
    }

    public function testProductsWithJsonItem()
    {
        $product1 = factory(Products::class)->create(['slug' => 'product-1', 'name' => 'Product 1']);
        $product2 = factory(Products::class)->create(['slug' => 'product-2', 'name' => 'Product 2']);
        
        $itemsJson = json_encode([
            ['slug' => 'product-1', 'quantity' => 2],
            ['slug' => 'product-2', 'quantity' => 3]
        ]);
        
        $bill = factory(Bill::class)->create(['item' => $itemsJson]);
        
        $products = $bill->products();
        
        $this->assertCount(2, $products);
        $this->assertEquals(2, $products->where('slug', 'product-1')->first()->quantity);
        $this->assertEquals(3, $products->where('slug', 'product-2')->first()->quantity);
    }

    public function testSendBonusToBuyer()
    {
        $user = factory(User::class)->create(['bonus' => 100]);
        $bill = factory(Bill::class)->create(['user_id' => $user->id, 'get_bonus' => 50]);
        
        $bill->sendBonusToBuyer();
        
        $this->assertEquals(150, $user->fresh()->bonus);
    }

    public function testSendBonusToBuyerWithoutUserId()
    {
        $bill = factory(Bill::class)->create(['user_id' => null, 'get_bonus' => 50]);
        
        $bill->sendBonusToBuyer();
        
        $this->assertTrue(true); // Should not throw exception
    }

    public function testIsCodGroup()
    {
        $bill = new Bill();
        
        $bill->pay_by = Bill::PAY_BY_COD;
        $this->assertTrue($bill->isCodGroup());
        
        $bill->pay_by = Bill::PAY_BY_FAMILY;
        $this->assertTrue($bill->isCodGroup());
        
        $bill->pay_by = Bill::PAY_BY_CREDIT;
        $this->assertFalse($bill->isCodGroup());
        
        $bill->pay_by = Bill::PAY_BY_ATM;
        $this->assertFalse($bill->isCodGroup());
    }

    public function testNextShipmentPhaseKol()
    {
        $bill = factory(Bill::class)->create([
            'pay_by' => Bill::PAY_BY_KOL,
            'shipment' => Bill::SHIPMENT_READY
        ]);
        
        $bill->nextShipmentPhase();
        
        $this->assertEquals(Bill::SHIPMENT_PENDING, $bill->fresh()->shipment);
    }

    public function testNextShipmentPhaseNonPaidNonCod()
    {
        $bill = factory(Bill::class)->create([
            'pay_by' => Bill::PAY_BY_CREDIT,
            'status' => 0,
            'shipment' => Bill::SHIPMENT_READY
        ]);
        
        $originalShipment = $bill->shipment;
        $bill->nextShipmentPhase();
        
        $this->assertEquals($originalShipment, $bill->fresh()->shipment);
    }

    public function testNextShipmentPhasePaidCredit()
    {
        $bill = factory(Bill::class)->create([
            'pay_by' => Bill::PAY_BY_CREDIT,
            'status' => 1,
            'shipment' => Bill::SHIPMENT_READY
        ]);
        
        $bill->nextShipmentPhase();
        
        $this->assertEquals(Bill::SHIPMENT_PENDING, $bill->fresh()->shipment);
    }

    public function testVoidBill()
    {
        $user = factory(User::class)->create(['bonus' => 100]);
        $bill = factory(Bill::class)->create([
            'user_id' => $user->id,
            'bonus_use' => 10, // 10 * 50 = 500 bonus refund
            'get_bonus' => 20,
            'pay_by' => Bill::PAY_BY_CREDIT,
            'status' => 1,
            'shipment' => Bill::SHIPMENT_PENDING
        ]);
        
        $bill->voidBill();
        
        $this->assertEquals(Bill::SHIPMENT_VOID, $bill->fresh()->shipment);
        $this->assertEquals(580, $user->fresh()->bonus); // 100 + 500 - 20 = 580
    }

    public function testVoidBillCodDelivered()
    {
        $user = factory(User::class)->create(['bonus' => 100]);
        $bill = factory(Bill::class)->create([
            'user_id' => $user->id,
            'bonus_use' => 0,
            'get_bonus' => 30,
            'pay_by' => Bill::PAY_BY_COD,
            'shipment' => Bill::SHIPMENT_DELIVERED
        ]);
        
        $bill->voidBill();
        
        $this->assertEquals(Bill::SHIPMENT_VOID, $bill->fresh()->shipment);
        $this->assertEquals(70, $user->fresh()->bonus); // 100 - 30 = 70
    }
}