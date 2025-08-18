<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Products;
use App\ProductCategory;
use App\Inventory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class ProductsTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdditionalThresholdConstant()
    {
        $this->assertEquals(12, Products::ADDITIONAL_CAT_ID);
        $this->assertEquals(500, Products::ADDITIONAL_THRESHOLD);
    }

    public function testGetAdditionalProducts()
    {
        // Record existing additional products before creating test data
        $existingAdditionalProducts = Products::getAdditionalProducts();
        
        $regularProduct = factory(Products::class)->create(['category_id' => 1]);
        $product1 = factory(Products::class)->create(['category_id' => Products::ADDITIONAL_CAT_ID]);
        $product2 = factory(Products::class)->create(['category_id' => Products::ADDITIONAL_CAT_ID]);
        
        $allAdditionalProducts = Products::getAdditionalProducts();
        
        // Test that we have the existing products plus our 2 new ones
        $this->assertCount(count($existingAdditionalProducts) + 2, $allAdditionalProducts);

        // Test that our new products are included
        $this->assertContains($product1->id, $allAdditionalProducts);
        $this->assertContains($product2->id, $allAdditionalProducts);
        $this->assertNotContains($regularProduct->id, $allAdditionalProducts);
        
        // Test that existing products are still there
        foreach ($existingAdditionalProducts as $existingId) {
            $this->assertContains($existingId, $allAdditionalProducts);
        }
    }

    public function testGetAdditionalProductsWithSpecificColumn()
    {
        // Record existing additional product names before creating test data
        $existingAdditionalProductNames = Products::getAdditionalProducts('name');
        
        $product1 = factory(Products::class)->create(['category_id' => Products::ADDITIONAL_CAT_ID, 'name' => 'Additional 1']);
        $product2 = factory(Products::class)->create(['category_id' => Products::ADDITIONAL_CAT_ID, 'name' => 'Additional 2']);
        
        $allAdditionalProductNames = Products::getAdditionalProducts('name');
        
        // Test that we have the existing products plus our 2 new ones
        $this->assertCount(count($existingAdditionalProductNames) + 2, $allAdditionalProductNames);
        
        // Test that our new product names are included
        $this->assertContains('Additional 1', $allAdditionalProductNames);
        $this->assertContains('Additional 2', $allAdditionalProductNames);
        
        // Test that existing product names are still there
        foreach ($existingAdditionalProductNames as $existingName) {
            $this->assertContains($existingName, $allAdditionalProductNames);
        }
    }

    public function testProductCategoryRelationship()
    {
        $category = factory(ProductCategory::class)->create(['name' => 'Test Category']);
        $product = factory(Products::class)->create(['category_id' => $category->id]);
        
        $this->assertEquals('Test Category', $product->productCategory->name);
    }

    public function testInventoryRelationship()
    {
        $product = factory(Products::class)->create();
        $inventory1 = factory(Inventory::class)->create();
        $inventory2 = factory(Inventory::class)->create();
        
        DB::table('inventory_product')->insert([
            ['product_id' => $product->id, 'inventory_id' => $inventory1->id, 'quantity' => 10],
            ['product_id' => $product->id, 'inventory_id' => $inventory2->id, 'quantity' => 5]
        ]);
        
        $inventories = $product->inventory;
        
        $this->assertCount(2, $inventories);
        $this->assertEquals(10, $inventories->where('id', $inventory1->id)->first()->pivot->quantity);
        $this->assertEquals(5, $inventories->where('id', $inventory2->id)->first()->pivot->quantity);
    }

    public function testCarrierRestriction()
    {
        $product = factory(Products::class)->create();
        
        DB::table('product_carrier')->insert([
            ['product_id' => $product->id, 'carrier_id' => 0],
            ['product_id' => $product->id, 'carrier_id' => 1]
        ]);
        
        $restrictions = $product->carrierRestriction();
        
        $this->assertCount(2, $restrictions);
        $this->assertContains(0, $restrictions);
        $this->assertContains(1, $restrictions);
    }

    public function testUpdateCarrierRestriction()
    {
        $product = factory(Products::class)->create();
        
        DB::table('product_carrier')->insert([
            ['product_id' => $product->id, 'carrier_id' => 0]
        ]);
        
        $product->updateCarrierRestriction([1, 2]);
        
        $restrictions = $product->carrierRestriction();
        $this->assertCount(2, $restrictions);
        $this->assertContains(1, $restrictions);
        $this->assertContains(2, $restrictions);
        $this->assertNotContains(0, $restrictions);
    }

    public function testSumInventoryAmount()
    {
        $product = factory(Products::class)->create();
        $inventoryA = factory(Inventory::class)->create(['slug' => 'A']);
        $inventoryB = factory(Inventory::class)->create(['slug' => 'B']);
        
        DB::table('inventory_product')->insert([
            ['product_id' => $product->id, 'inventory_id' => $inventoryA->id, 'quantity' => 3],
            ['product_id' => $product->id, 'inventory_id' => $inventoryB->id, 'quantity' => 2],
            ['product_id' => $product->id, 'inventory_id' => $inventoryA->id, 'quantity' => 1]
        ]);
        
        $sum = $product->sumInventoryAmount(5); // quantity = 5
        
        $this->assertEquals(20, $sum['A']); // (3 + 1) * 5 = 20
        $this->assertEquals(10, $sum['B']); // 2 * 5 = 10
    }

    public function testGetAllBindedProducts()
    {
        $product1 = factory(Products::class)->create();
        $product2 = factory(Products::class)->create();
        $product3 = factory(Products::class)->create();
        
        DB::table('product_bind')->insert([
            ['product_id' => $product1->id, 'bind_product_id' => $product2->id],
            ['product_id' => $product1->id, 'bind_product_id' => $product3->id]
        ]);
        
        $result = Products::getAllBindedProducts();
        
        $this->assertArrayHasKey('relation', $result);
        $this->assertArrayHasKey('reverseRelation', $result);
        
        $this->assertArrayHasKey($product1->id, $result['relation']);
        $this->assertContains($product2->id, $result['relation'][$product1->id]);
        $this->assertContains($product3->id, $result['relation'][$product1->id]);
        
        $this->assertArrayHasKey($product2->id, $result['reverseRelation']);
        $this->assertContains($product1->id, $result['reverseRelation'][$product2->id]);
    }

    public function testGetBindedProducts()
    {
        $mainProduct = factory(Products::class)->create();
        $bindProduct1 = factory(Products::class)->create(['name' => 'Bind Product 1']);
        $bindProduct2 = factory(Products::class)->create(['name' => 'Bind Product 2']);
        $otherProduct = factory(Products::class)->create();
        
        DB::table('product_bind')->insert([
            ['product_id' => $mainProduct->id, 'bind_product_id' => $bindProduct1->id],
            ['product_id' => $mainProduct->id, 'bind_product_id' => $bindProduct2->id]
        ]);
        
        $bindedProducts = Products::getBindedProducts([$mainProduct->id]);
        
        $this->assertCount(2, $bindedProducts);
        $this->assertTrue($bindedProducts->contains('name', 'Bind Product 1'));
        $this->assertTrue($bindedProducts->contains('name', 'Bind Product 2'));
    }

    public function testGetViolationProductIdArray()
    {
        $mainProduct = factory(Products::class)->create();
        $bindProduct = factory(Products::class)->create();
        $violationProduct = factory(Products::class)->create();
        
        DB::table('product_bind')->insert([
            ['product_id' => $mainProduct->id, 'bind_product_id' => $bindProduct->id],
            ['product_id' => $mainProduct->id, 'bind_product_id' => $violationProduct->id]
        ]);
        
        $violations = Products::getViolationProductIdArray([$bindProduct->id]);
        
        $this->assertContains($bindProduct->id, $violations);
    }

    public function testGetViolationProductIdArrayWithoutViolation()
    {
        $mainProduct = factory(Products::class)->create();
        $bindProduct = factory(Products::class)->create();
        
        DB::table('product_bind')->insert([
            ['product_id' => $mainProduct->id, 'bind_product_id' => $bindProduct->id]
        ]);
        
        $violations = Products::getViolationProductIdArray([$mainProduct->id, $bindProduct->id]);
        
        $this->assertEmpty($violations);
    }

    public function testTotalPrice()
    {
        $product1 = factory(Products::class)->create(['price' => 100]);
        $product2 = factory(Products::class)->create(['price' => 200]);
        $additionalProduct = factory(Products::class)->create(['price' => 50]);
        
        $totalPrice = Products::totalPrice(
            [$product1->id, $product2->id, $additionalProduct->id],
            [$additionalProduct->id]
        );
        
        $this->assertEquals(300, $totalPrice); // 100 + 200, excluding additional product
    }

    public function testTotalPriceWithoutAdditionalProducts()
    {
        $product1 = factory(Products::class)->create(['price' => 150]);
        $product2 = factory(Products::class)->create(['price' => 250]);
        
        $totalPrice = Products::totalPrice([$product1->id, $product2->id]);
        
        $this->assertEquals(400, $totalPrice);
    }

    public function testTotalPriceBySlug()
    {
        $product1 = factory(Products::class)->create(['price' => 100, 'slug' => 'product-1']);
        $product2 = factory(Products::class)->create(['price' => 200, 'slug' => 'product-2']);
        $additionalProduct = factory(Products::class)->create(['price' => 50, 'slug' => 'additional-1']);
        
        $totalPrice = Products::totalPriceBySlug(
            ['product-1', 'product-2', 'additional-1'],
            ['additional-1']
        );
        
        $this->assertEquals(300, $totalPrice);
    }

    public function testHasCategory()
    {
        $product1 = factory(Products::class)->create(['category_id' => 5, 'slug' => 'product-1']);
        $product2 = factory(Products::class)->create(['category_id' => 10, 'slug' => 'product-2']);
        
        $hasCategory5 = Products::hasCategory(['product-1', 'product-2'], 5);
        $hasCategory15 = Products::hasCategory(['product-1', 'product-2'], 15);
        
        $this->assertTrue($hasCategory5);
        $this->assertFalse($hasCategory15);
    }
}