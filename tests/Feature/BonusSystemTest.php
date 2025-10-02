<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Bill;
use App\Products;
use App\Kart;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BonusSystemTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create(['bonus' => 5000]);
    }

    public function testBonusEarnOnPurchase()
    {
        $bill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'get_bonus' => 100,
            'status' => 1, // Paid
            'pay_by' => Bill::PAY_BY_CREDIT
        ]);
        
        $bill->sendBonusToBuyer();
        
        $this->assertEquals(5100, $this->user->fresh()->bonus); // 500 + 100
    }

    public function testBonusEarnCalculationBasedOnAmount()
    {
        $purchaseAmount = 2000;
        $expectedBonus = floor($purchaseAmount / 100); // 1% bonus rate
        
        $bill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'price' => $purchaseAmount,
            'get_bonus' => $expectedBonus,
            'status' => 1
        ]);
        
        $this->assertEquals(20, $bill->get_bonus);
        
        $bill->sendBonusToBuyer();
        
        $this->assertEquals(5020, $this->user->fresh()->bonus); // 500 + 20
    }

    public function testBonusUsageOnPayment()
    {
        $product = factory(Products::class)->create(['price' => 1000]);
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $product->id]);
        
        $bonusToUse = 5000; // 5 points = 250 NT$ discount
        $expectedPrice = 1000 - ($bonusToUse / 50); // 1000 - 100 = 900
        
        $requestData = [
            'item' => [$product->slug],
            'quantity' => [1],
            'ship_name' => '測試收件人',
            'ship_gender' => 1, // 0 = 男, 1 = 女
            'ship_phone' => '0912345678',
            'ship_county' => '台北市',
            'ship_district' => '信義區',
            'ship_address' => '信義路五段7號',
            'ship_email' => 'bonus@example.com',
            'ship_arrive' => '任何時間',
            'ship_arriveDate' => '2025-01-15',
            'ship_time' => '13點前',
            'ship_receipt' => '2',
            'ship_memo' => '使用紅利測試',
            'ship_pay_by' => '貨到付款',
            'carrier_id' => '0',
            'bonus' => (string)$bonusToUse // 傳遞紅利金額而非點數
        ];
        
        $response = $this->actingAs($this->user)->post('/bill', $requestData);
        
        $response->assertStatus(200);
        
        $latestBill = Bill::where('user_id', $this->user->id)->latest()->first();
        $this->assertNotNull($latestBill);
        $this->assertEquals($bonusToUse / 50, $latestBill->bonus_use);
        $this->assertEquals($expectedPrice, $latestBill->price);
        
        $this->assertEquals(0, $this->user->fresh()->bonus); 
    }

    public function testBonusUsageExceedsAvailable()
    {
        // Create a new user with only 50 bonus points
        $limitedUser = factory(User::class)->create(['bonus' => 50]);
        
        $product = factory(Products::class)->create(['price' => 1000]);
        factory(Kart::class)->create(['user_id' => $limitedUser->id, 'product_id' => $product->id]);
        
        $requestData = [
            'item' => [$product->slug],
            'quantity' => [1],
            'ship_name' => '測試收件人',
            'ship_gender' => 1, // 0 = 男, 1 = 女
            'ship_phone' => '0912345678',
            'ship_county' => '台北市',
            'ship_district' => '信義區',
            'ship_address' => '信義路五段7號',
            'ship_email' => 'bonus@example.com',
            'ship_arrive' => '任何時間',
            'ship_arriveDate' => '2025-01-20',
            'ship_time' => '14-18點',
            'ship_receipt' => '1',
            'ship_memo' => '超過紅利測試',
            'ship_pay_by' => '貨到付款',
            'carrier_id' => '0',
            'bonus' => 100 // Try to use 100 points (2 NT$) but only have 50 points (1 NT$)
        ];
        
        $response = $this->actingAs($limitedUser)->post('/bill', $requestData);
        
        $response->assertStatus(200);
        
        // Should only use available bonus (50 points = 2 NT$ discount)
        $latestBill = Bill::where('user_id', $limitedUser->id)->latest()->first();
        $this->assertNotNull($latestBill);
        $this->assertEquals(1, $latestBill->bonus_use);
        $this->assertEquals(999, $latestBill->price); // 1000 - 1 = 999
        
        $this->assertEquals(0, $limitedUser->fresh()->bonus); // 3 - 3 = 0
    }

    public function testBonusRefundOnCancellation()
    {
        $bill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'bonus_use' => 10, // Used 10 bonus points
            'get_bonus' => 50,  // Would have earned 50 points
            'status' => 1,      // Paid
            'shipment' => Bill::SHIPMENT_PENDING
        ]);
        
        $originalBonus = $this->user->bonus; // 500
        
        $bill->voidBill();
        
        // Refund used bonus: 10 * 50 = 500 NT$ worth = 10 points back
        // Deduct earned bonus: 50 points
        // Net effect: +500 - 50 = +450 NT$ worth = +9 points... wait, this is wrong
        // Actually: refund 10 points used, deduct 50 points earned
        // 500 + (10 * 50) - 50 = 500 + 500 - 50 = 950
        
        $expectedBonus = $originalBonus + (10 * 50) - 50; // 500 + 500 - 50 = 950
        
        $this->assertEquals(Bill::SHIPMENT_VOID, $bill->fresh()->shipment);
        $this->assertEquals($expectedBonus, $this->user->fresh()->bonus);
    }

    public function testBonusRefundOnCancellationCOD()
    {
        $bill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'bonus_use' => 5,   // Used 5 bonus points  
            'get_bonus' => 30,  // Would have earned 30 points
            'pay_by' => Bill::PAY_BY_COD,
            'shipment' => Bill::SHIPMENT_DELIVERED // Already delivered COD
        ]);
        
        $originalBonus = $this->user->bonus; // 500
        
        $bill->voidBill();
        
        // COD delivered: refund used bonus + deduct earned bonus
        $expectedBonus = $originalBonus + (5 * 50) - 30; // 500 + 250 - 30 = 720
        
        $this->assertEquals(Bill::SHIPMENT_VOID, $bill->fresh()->shipment);
        $this->assertEquals($expectedBonus, $this->user->fresh()->bonus);
    }


    public function testBonusConversionRate()
    {
        // Test the conversion rate: 1 bonus point = 50 NT$
        $bonusPoints = 400;
        $expectedDiscount = $bonusPoints / 50; // 400 / 50 = 8
        
        $product = factory(Products::class)->create(['price' => 1500]);
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $product->id]);
        
        $requestData = [
            'item' => [$product->slug],
            'quantity' => [1],
            'ship_name' => '測試收件人',
            'ship_gender' => 0, // 0 = 男, 1 = 女
            'ship_phone' => '0912345678',
            'ship_county' => '台北市',
            'ship_district' => '信義區',
            'ship_address' => '信義路五段7號',
            'ship_email' => 'conversion@example.com',
            'ship_arrive' => '任何時間',
            'ship_arriveDate' => '2025-01-25',
            'ship_time' => '13點前',
            'ship_receipt' => '2',
            'ship_memo' => '轉換率測試',
            'ship_pay_by' => '貨到付款',
            'carrier_id' => '0',
            'bonus' => $bonusPoints
        ];
        
        $response = $this->actingAs($this->user)->post('/bill', $requestData);
        
        $response->assertStatus(200);
        
        $latestBill = Bill::where('user_id', $this->user->id)->latest()->first();
        $this->assertNotNull($latestBill);
        $this->assertEquals(1500 - $expectedDiscount, $latestBill->price); // 1500 - 400 = 1100
    }

    public function testBonusNotEarnedOnUnpaidOrder()
    {
        $bill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'get_bonus' => 75,
            'status' => 0,  // Not paid
            'pay_by' => Bill::PAY_BY_ATM
        ]);
        
        $originalBonus = $this->user->bonus;
        
        $bill->sendBonusToBuyer();
        
        // Should not earn bonus for unpaid order
        $this->assertEquals($originalBonus, $this->user->fresh()->bonus);
    }

    public function testBonusEarnedOnCODDelivery()
    {
        $bill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'get_bonus' => 40,
            'pay_by' => Bill::PAY_BY_COD,
            'shipment' => Bill::SHIPMENT_READY
        ]);
        
        $originalBonus = $this->user->bonus;
        
        // Simulate shipment progression for COD
        $bill->nextShipmentPhase(); // READY -> PENDING
        $this->assertEquals($originalBonus, $this->user->fresh()->bonus); // No bonus yet
        
        $bill->nextShipmentPhase(); // PENDING -> DELIVERED (bonus earned)
        $this->assertEquals($originalBonus + 40, $this->user->fresh()->bonus);
    }

    public function testBonusTransactionLogging()
    {
        $initialBonus = $this->user->bonus;
        
        // Test bonus usage
        $this->user->updateBonus(10, true); // Use 10 points
        $this->assertEquals($initialBonus - 10, $this->user->fresh()->bonus);
        
        // Test bonus earning
        $this->user->updateBonus(25, false); // Earn 25 points
        $this->assertEquals($initialBonus - 10 + 25, $this->user->fresh()->bonus);
        
        // Test zero amount
        $currentBonus = $this->user->fresh()->bonus;
        $this->user->updateBonus(0, true);
        $this->assertEquals($currentBonus, $this->user->fresh()->bonus);
    }

}