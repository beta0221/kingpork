<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\FavoriteAddress;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuickRecipientSelectTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create();
    }

    public function testFavoriteAddressWithShippingInfoIsAccessible()
    {
        // Create a favorite address with complete shipping info
        $favoriteAddress = $this->user->addresses()->create([
            'county' => '台北市',
            'district' => '大安區',
            'address' => '忠孝東路四段100號',
            'ship_name' => '快速測試收件人',
            'ship_phone' => '0912345678',
            'ship_email' => 'quicktest@example.com',
            'ship_receipt' => 3,
            'ship_three_id' => '12345678',
            'ship_three_company' => '快速測試公司',
            'ship_gender' => 1,
            'isDefault' => 1
        ]);

        // Verify data can be retrieved
        $retrieved = $this->user->addresses()->find($favoriteAddress->id);
        
        $this->assertEquals('快速測試收件人', $retrieved->ship_name);
        $this->assertEquals('0912345678', $retrieved->ship_phone);
        $this->assertEquals('quicktest@example.com', $retrieved->ship_email);
        $this->assertEquals(3, $retrieved->ship_receipt);
        $this->assertEquals('12345678', $retrieved->ship_three_id);
        $this->assertEquals('快速測試公司', $retrieved->ship_three_company);
        $this->assertEquals(1, $retrieved->ship_gender);
    }

    public function testQuickSelectUICanDisplayAddresses()
    {
        // Create addresses with different shipping info for UI display
        $address1 = $this->user->addresses()->create([
            'county' => '台北市',
            'district' => '信義區',
            'address' => '信義路五段7號',
            'ship_name' => '張三',
            'ship_phone' => '0912345678',
            'ship_email' => 'zhang@example.com',
            'ship_receipt' => 2,
            'ship_gender' => 1,
            'isDefault' => 1
        ]);

        $address2 = $this->user->addresses()->create([
            'county' => '高雄市',
            'district' => '苓雅區', 
            'address' => '四維三路2號',
            'ship_name' => '李四',
            'ship_phone' => '0987654321',
            'ship_email' => 'li@example.com',
            'ship_receipt' => 3,
            'ship_three_id' => '87654321',
            'ship_three_company' => '測試科技公司',
            'ship_gender' => 2,
            'isDefault' => 0
        ]);

        // Simulate what the UI dropdown would display
        $addresses = $this->user->addresses()->whereNotNull('ship_name')->get();
        
        $this->assertCount(2, $addresses);
        
        $displayText1 = $addresses->first()->ship_name . ' - ' . 
                       $addresses->first()->county . 
                       $addresses->first()->district . 
                       $addresses->first()->address;
        
        $this->assertEquals('張三 - 台北市信義區信義路五段7號', $displayText1);
        
        $displayText2 = $addresses->last()->ship_name . ' - ' . 
                       $addresses->last()->county . 
                       $addresses->last()->district . 
                       $addresses->last()->address;
                       
        $this->assertEquals('李四 - 高雄市苓雅區四維三路2號', $displayText2);
    }

    public function testAddressFilteringForQuickSelect()
    {
        // Create addresses with and without shipping info
        $completeAddress = $this->user->addresses()->create([
            'county' => '台北市',
            'district' => '大安區',
            'address' => '忠孝東路四段1號',
            'ship_name' => '完整資料用戶',
            'ship_phone' => '0912345678',
            'ship_email' => 'complete@example.com',
            'isDefault' => 1
        ]);

        $incompleteAddress = $this->user->addresses()->create([
            'county' => '新北市',
            'district' => '板橋區',
            'address' => '中山路一段100號',
            // No shipping info
            'isDefault' => 0
        ]);

        // Only addresses with ship_name should appear in quick select
        $quickSelectAddresses = $this->user->addresses()->whereNotNull('ship_name')->get();
        $this->assertCount(1, $quickSelectAddresses);
        $this->assertEquals('完整資料用戶', $quickSelectAddresses->first()->ship_name);

        // All addresses should be available for traditional mode
        $allAddresses = $this->user->addresses()->get();
        $this->assertCount(2, $allAddresses);
    }
}