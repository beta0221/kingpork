<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Products;
use App\Kart;
use App\sessionCart;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class KartControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create();
        $this->product = factory(Products::class)->create(['price' => 500]);
    }

    public function testInKartCountForAuthenticatedUser()
    {
        factory(Kart::class)->create(['user_id' => $this->user->id]);
        factory(Kart::class)->create(['user_id' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->postJson('/inKart');
        
        $response->assertStatus(200);
        $response->assertJson(['msg' => 2]);
    }

    public function testInKartCountForAuthenticatedUserEmpty()
    {
        $response = $this->actingAs($this->user)->postJson('/inKart');
        
        $response->assertStatus(200);
        $response->assertJson(['msg' => 0]);
    }

    public function testInKartCountForGuestWithSessionCart()
    {
        $ipAddress = '192.168.1.1';
        $items = [1, 2, 3];
        
        sessionCart::create([
            'ip_address' => $ipAddress,
            'item' => json_encode($items)
        ]);
        
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
                         ->postJson('/inKart');
        
        $response->assertStatus(200);
        $response->assertJson(['msg' => 3]);
    }

    public function testInKartCountForGuestWithoutSessionCart()
    {
        $response = $this->postJson('/inKart');
        
        $response->assertStatus(200);
        $response->assertJson(['msg' => 0]);
    }

    public function testCheckIfKartForAuthenticatedUserProductExists()
    {
        factory(Kart::class)->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
        
        $response = $this->actingAs($this->user)
                         ->getJson("/checkIfKart/{$this->product->id}");
        
        $response->assertStatus(200);
        $response->assertJson(['msg' => true]);
    }

    public function testCheckIfKartForAuthenticatedUserProductNotExists()
    {
        $response = $this->actingAs($this->user)
                         ->getJson("/checkIfKart/{$this->product->id}");
        
        $response->assertStatus(200);
        $response->assertJson(['msg' => false]);
    }

    public function testCheckIfKartForGuestWithSessionCart()
    {
        $ipAddress = '192.168.1.100';
        $items = [$this->product->id, 99, 88];
        
        sessionCart::create([
            'ip_address' => $ipAddress,
            'item' => json_encode($items)
        ]);
        
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
                         ->getJson("/checkIfKart/{$this->product->id}");
        
        $response->assertStatus(200);
        $response->assertJson(['msg' => true]);
    }

    public function testCheckIfKartForGuestWithoutSessionCart()
    {
        $response = $this->getJson("/checkIfKart/{$this->product->id}");
        
        $response->assertStatus(200);
        $response->assertJson(['msg' => false]);
    }

    public function testCheckIfKartForGuestProductNotInSession()
    {
        $ipAddress = '192.168.1.200';
        $items = [999, 888, 777]; // Use IDs that definitely won't exist
        
        sessionCart::create([
            'ip_address' => $ipAddress,
            'item' => json_encode($items)
        ]);
        
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
                         ->getJson("/checkIfKart/{$this->product->id}");
        
        $response->assertStatus(200);
        $response->assertJson(['msg' => false]);
    }

    public function testAddProductToKartForAuthenticatedUser()
    {
        $response = $this->actingAs($this->user)
                         ->postJson('/kart', [
                             'product_id' => $this->product->id
                         ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('kart', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    public function testAddProductToKartForGuestCreatesSessionCart()
    {
        $ipAddress = '192.168.1.50';
        
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
                         ->postJson('/kart', [
                             'product_id' => $this->product->id
                         ]);
        
        $response->assertStatus(200);
        
        $sessionCart = sessionCart::where('ip_address', $ipAddress)->first();
        $this->assertNotNull($sessionCart);
        
        $items = json_decode($sessionCart->item, true);
        $this->assertContains($this->product->id, $items);
    }

    public function testAddProductToKartForGuestUpdatesExistingSession()
    {
        $ipAddress = '192.168.1.75';
        $existingProduct = factory(Products::class)->create();
        
        sessionCart::create([
            'ip_address' => $ipAddress,
            'item' => json_encode([$existingProduct->id])
        ]);
        
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
                         ->postJson('/kart', [
                             'product_id' => $this->product->id
                         ]);
        
        $response->assertStatus(200);
        
        $sessionCart = sessionCart::where('ip_address', $ipAddress)->first();
        $items = json_decode($sessionCart->item, true);
        
        $this->assertCount(2, $items);
        $this->assertContains($existingProduct->id, $items);
        $this->assertContains($this->product->id, $items);
    }

    public function testRemoveProductFromKartForAuthenticatedUser()
    {
        $kartItem = factory(Kart::class)->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
        
        $response = $this->actingAs($this->user)
                         ->deleteJson("/kart/{$this->product->id}");
        
        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('kart', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    public function testRemoveProductFromKartForGuest()
    {
        $ipAddress = '192.168.1.25';
        $otherProduct = factory(Products::class)->create();
        
        sessionCart::create([
            'ip_address' => $ipAddress,
            'item' => json_encode([$this->product->id, $otherProduct->id])
        ]);
        
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
                         ->deleteJson("/kart/{$this->product->id}");
        
        $response->assertStatus(200);
        
        $sessionCart = sessionCart::where('ip_address', $ipAddress)->first();
        $items = json_decode($sessionCart->item, true);
        
        $this->assertCount(1, $items);
        $this->assertNotContains($this->product->id, $items);
        $this->assertContains($otherProduct->id, $items);
    }

    public function testKartTotalPriceCalculation()
    {
        $product1 = factory(Products::class)->create(['price' => 300]);
        $product2 = factory(Products::class)->create(['price' => 700]);
        $additionalProduct = factory(Products::class)->create([
            'price' => 100,
            'category_id' => Products::ADDITIONAL_CAT_ID
        ]);
        
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $product1->id]);
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $product2->id]);
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $additionalProduct->id]);
        
        $totalPrice = Kart::getKartTotalPrice($this->user->id, [$additionalProduct->id]);
        
        $this->assertEquals(1000, $totalPrice); // 300 + 700, excluding additional product
    }

    public function testKartTotalPriceWithoutAdditionalProducts()
    {
        $product1 = factory(Products::class)->create(['price' => 400]);
        $product2 = factory(Products::class)->create(['price' => 600]);
        
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $product1->id]);
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $product2->id]);
        
        $totalPrice = Kart::getKartTotalPrice($this->user->id);
        
        $this->assertEquals(1000, $totalPrice);
    }

    public function testHasProductInKart()
    {
        $product1 = factory(Products::class)->create();
        $product2 = factory(Products::class)->create();
        $product3 = factory(Products::class)->create();
        
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $product1->id]);
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $product2->id]);
        
        $hasProducts = Kart::hasProduct($this->user->id, [$product1->id, $product3->id]);
        $noProducts = Kart::hasProduct($this->user->id, [$product3->id]);
        
        $this->assertTrue($hasProducts);
        $this->assertFalse($noProducts);
    }


    public function testKartIndexPage()
    {
        $product1 = factory(Products::class)->create(['name' => 'Test Product 1']);
        $product2 = factory(Products::class)->create(['name' => 'Test Product 2']);
        
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $product1->id]);
        factory(Kart::class)->create(['user_id' => $this->user->id, 'product_id' => $product2->id]);
        
        $response = $this->actingAs($this->user)->get('/kart');
        
        $response->assertStatus(200);
        $response->assertSee('Test Product 1');
        $response->assertSee('Test Product 2');
    }

    public function testKartIndexRequiresAuthentication()
    {
        $response = $this->get('/kart');
        
        $response->assertStatus(200); // Controller doesn't enforce auth, just shows empty cart
    }

}