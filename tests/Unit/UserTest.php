<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use App\Kart;
use App\Products;
use App\UserCreditCard;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateBonusDecrease()
    {
        $user = factory(User::class)->create(['bonus' => 100]);
        
        $user->updateBonus(30, true);
        
        $this->assertEquals(70, $user->fresh()->bonus);
    }

    public function testUpdateBonusIncrease()
    {
        $user = factory(User::class)->create(['bonus' => 50]);
        
        $user->updateBonus(25, false);
        
        $this->assertEquals(75, $user->fresh()->bonus);
    }

    public function testUpdateBonusWithZeroAmount()
    {
        $user = factory(User::class)->create(['bonus' => 100]);
        $originalBonus = $user->bonus;
        
        $user->updateBonus(0, true);
        
        $this->assertEquals($originalBonus, $user->fresh()->bonus);
    }

    public function testUpdateBonusPreventNegative()
    {
        $user = factory(User::class)->create(['bonus' => 20]);
        
        $user->updateBonus(50, true);
        
        $this->assertEquals(0, $user->fresh()->bonus);
    }

    public function testKartRelationship()
    {
        $user = factory(User::class)->create();
        $product1 = factory(Products::class)->create();
        $product2 = factory(Products::class)->create();
        
        factory(Kart::class)->create(['user_id' => $user->id, 'product_id' => $product1->id]);
        factory(Kart::class)->create(['user_id' => $user->id, 'product_id' => $product2->id]);
        
        $kartItems = $user->kart;
        
        $this->assertCount(2, $kartItems);
        $this->assertEquals($user->id, $kartItems->first()->user_id);
    }

    public function testKartProductsId()
    {
        $user = factory(User::class)->create();
        $product1 = factory(Products::class)->create();
        $product2 = factory(Products::class)->create();
        
        factory(Kart::class)->create(['user_id' => $user->id, 'product_id' => $product1->id]);
        factory(Kart::class)->create(['user_id' => $user->id, 'product_id' => $product2->id]);
        
        $productIds = $user->kartProductsId();
        
        $this->assertCount(2, $productIds);
        $this->assertContains($product1->id, $productIds);
        $this->assertContains($product2->id, $productIds);
    }

    public function testKartProducts()
    {
        $user = factory(User::class)->create();
        $product1 = factory(Products::class)->create(['name' => 'Test Product 1']);
        $product2 = factory(Products::class)->create(['name' => 'Test Product 2']);
        
        factory(Kart::class)->create(['user_id' => $user->id, 'product_id' => $product1->id]);
        factory(Kart::class)->create(['user_id' => $user->id, 'product_id' => $product2->id]);
        
        $products = $user->kartProducts();
        
        $this->assertCount(2, $products);
        $this->assertEquals('Test Product 1', $products->where('id', $product1->id)->first()->name);
        $this->assertEquals('Test Product 2', $products->where('id', $product2->id)->first()->name);
    }

    public function testCreditCardsRelationship()
    {
        $user = factory(User::class)->create();
        $card1 = factory(UserCreditCard::class)->create(['user_id' => $user->id]);
        $card2 = factory(UserCreditCard::class)->create(['user_id' => $user->id]);
        
        $creditCards = $user->creditCards;
        
        $this->assertCount(2, $creditCards);
        $this->assertEquals($user->id, $creditCards->first()->user_id);
    }

    // public function testGetDefaultCreditCard()
    // {
    //     $user = factory(User::class)->create();
    //     $defaultCard = factory(UserCreditCard::class)->create([
    //         'user_id' => $user->id,
    //         'is_default' => true,
    //         'is_active' => true,
    //         'card_alias' => 'Default Card'
    //     ]);
    //     $regularCard = factory(UserCreditCard::class)->create([
    //         'user_id' => $user->id,
    //         'is_default' => false,
    //         'is_active' => true
    //     ]);
        
    //     $defaultCreditCard = $user->getDefaultCreditCardAttribute();
        
    //     $this->assertNotNull($defaultCreditCard);
    //     $this->assertEquals($defaultCard->id, $defaultCreditCard->id);
    //     $this->assertEquals('Default Card', $defaultCreditCard->card_alias);
    // }

    // public function testGetDefaultCreditCardWhenNone()
    // {
    //     $user = factory(User::class)->create();
    //     factory(UserCreditCard::class)->create([
    //         'user_id' => $user->id,
    //         'is_default' => false,
    //         'is_active' => true
    //     ]);
        
    //     $defaultCreditCard = $user->getDefaultCreditCardAttribute();
        
    //     $this->assertNull($defaultCreditCard);
    // }

    // public function testGetDefaultCreditCardWhenInactive()
    // {
    //     $user = factory(User::class)->create();
    //     factory(UserCreditCard::class)->create([
    //         'user_id' => $user->id,
    //         'is_default' => true,
    //         'is_active' => false
    //     ]);
        
    //     $defaultCreditCard = $user->getDefaultCreditCardAttribute();
        
    //     $this->assertNull($defaultCreditCard);
    // }

    public function testAddressesRelationship()
    {
        $user = factory(User::class)->create();
        
        $user->addresses()->create([
            'county' => 'Taipei',
            'district' => 'Xinyi',
            'address' => '123 Main St',
            'isDefault' => 1
        ]);
        
        $addresses = $user->addresses;
        
        $this->assertCount(1, $addresses);
        $this->assertEquals('123 Main St', $addresses->first()->address);
        $this->assertEquals(1, $addresses->first()->isDefault);
    }

    public function testKartProductsIdEmptyWhenNoItems()
    {
        $user = factory(User::class)->create();
        
        $productIds = $user->kartProductsId();
        
        $this->assertCount(0, $productIds);
        $this->assertTrue($productIds->isEmpty());
    }

    public function testKartProductsEmptyWhenNoItems()
    {
        $user = factory(User::class)->create();
        
        $products = $user->kartProducts();
        
        $this->assertCount(0, $products);
        $this->assertTrue($products->isEmpty());
    }
}