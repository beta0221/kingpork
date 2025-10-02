<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Bill;
use App\Products;
use App\Kart;
use App\UserCreditCard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class BillControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        Config::set('ecpay.MerchantId', 'TEST_MERCHANT');
        Config::set('ecpay.HashKey', 'TEST_HASH_KEY_12345678901234567890');
        Config::set('ecpay.HashIV', 'TEST_HASH_IV_123456');

        $this->user = factory(User::class)->create();
    }

    public function testIndexShowsUserBills()
    {
        $bill1 = factory(Bill::class)->create(['user_id' => $this->user->id]);
        $bill2 = factory(Bill::class)->create(['user_id' => $this->user->id]);
        $otherUserBill = factory(Bill::class)->create();
        
        $response = $this->actingAs($this->user)->get('/bill');
        
        $response->assertStatus(200);
        $response->assertSee($bill1->bill_id);
        $response->assertSee($bill2->bill_id);
        $response->assertDontSee($otherUserBill->bill_id);
    }

    public function testIndexRequiresAuthentication()
    {
        $response = $this->get('/bill');
        
        $response->assertRedirect('/login');
    }

    public function testBillDetailAccess()
    {
        $bill = factory(Bill::class)->create(['user_id' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->get("/bill/{$bill->bill_id}/detail");
        
        $response->assertStatus(200);
        $response->assertSee($bill->bill_id);
    }

    public function testBillDetailAuthOnly()
    {
        
        $bill = factory(Bill::class)->create();
        $response = $this->get("/bill/{$bill->bill_id}/detail");
        
        $response->assertStatus(302);
    }

    public function testBillDetailUnauthorizedAccess()
    {
        $otherUser = factory(User::class)->create();
        $bill = factory(Bill::class)->create(['user_id' => $otherUser->id]);
        
        $response = $this->actingAs($this->user)->get("/bill/{$bill->bill_id}/detail");
        
        $response->assertStatus(403);
    }

    public function testCancelBill()
    {
        $bill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'shipment' => Bill::SHIPMENT_READY,
            'status' => 0
        ]);
        
        $response = $this->actingAs($this->user)
            ->delete("/bill/cancel/{$bill->bill_id}");
        
        $response->assertStatus(200);
        $response->assertSeeText('success');
        $this->assertEquals(Bill::SHIPMENT_VOID, $bill->fresh()->shipment);
    }

    public function testCannotCancelShippedBill()
    {
        $bill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'shipment' => Bill::SHIPMENT_DELIVERED,
            'status' => 1
        ]);
        
        $response = $this->actingAs($this->user)->delete("/bill/cancel/{$bill->bill_id}");
        
        $response->assertStatus(200);
        $response->assertSeeText('error');
        $this->assertEquals(Bill::SHIPMENT_DELIVERED, $bill->fresh()->shipment);
    }
}