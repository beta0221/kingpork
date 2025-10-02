<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\FavoriteAddress;
use App\Products;
use App\Kart;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class FavoriteAddressShippingInfoTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create();
    }

    public function testFavoriteAddressCanSaveShippingInfo()
    {
        $favoriteAddress = $this->user->addresses()->create([
            'county' => '台北市',
            'district' => '中正區', 
            'address' => '中山南路1號',
            'ship_name' => '測試姓名',
            'ship_phone' => '0912345678',
            'ship_email' => 'test@example.com',
            'ship_receipt' => 3,
            'ship_three_id' => '12345678',
            'ship_three_company' => '測試公司',
            'ship_gender' => 1,
            'isDefault' => 1
        ]);

        $this->assertDatabaseHas('favorite_addresses', [
            'id' => $favoriteAddress->id,
            'user_id' => $this->user->id,
            'ship_name' => '測試姓名',
            'ship_phone' => '0912345678',
            'ship_email' => 'test@example.com',
            'ship_receipt' => 3,
            'ship_three_id' => '12345678',
            'ship_three_company' => '測試公司',
            'ship_gender' => 1
        ]);
    }

    public function testFavoriteAddressCanBeRetrievedWithShippingInfo()
    {
        // Create a favorite address with shipping info
        $favoriteAddress = $this->user->addresses()->create([
            'county' => '台北市',
            'district' => '中正區',
            'address' => '中山南路1號',
            'ship_name' => '測試收件人',
            'ship_phone' => '0987654321',
            'ship_email' => 'recipient@example.com',
            'ship_receipt' => 2,
            'ship_gender' => 2,
            'isDefault' => 1
        ]);

        // Retrieve the address and verify shipping info
        $retrieved = FavoriteAddress::find($favoriteAddress->id);
        
        $this->assertEquals('測試收件人', $retrieved->ship_name);
        $this->assertEquals('0987654321', $retrieved->ship_phone);
        $this->assertEquals('recipient@example.com', $retrieved->ship_email);
        $this->assertEquals(2, $retrieved->ship_receipt);
        $this->assertEquals(2, $retrieved->ship_gender);
    }

    public function testUserAddressesRelationshipIncludesShippingInfo()
    {
        // Create multiple favorite addresses with different shipping info
        $address1 = $this->user->addresses()->create([
            'county' => '台北市',
            'district' => '大安區',
            'address' => '復興南路一段100號',
            'ship_name' => '張三',
            'ship_phone' => '0912345678',
            'ship_email' => 'zhang@example.com',
            'ship_receipt' => 2,
            'ship_gender' => 1,
            'isDefault' => 1
        ]);

        $address2 = $this->user->addresses()->create([
            'county' => '高雄市',
            'district' => '左營區',
            'address' => '博愛二路200號',
            'ship_name' => '李四',
            'ship_phone' => '0987654321',
            'ship_email' => 'li@example.com',
            'ship_receipt' => 3,
            'ship_three_id' => '12345678',
            'ship_three_company' => '測試公司',
            'ship_gender' => 2,
            'isDefault' => 0
        ]);

        // Test relationship retrieval
        $userAddresses = $this->user->addresses;
        $this->assertCount(2, $userAddresses);
        
        $retrievedAddress1 = $userAddresses->find($address1->id);
        $this->assertEquals('張三', $retrievedAddress1->ship_name);
        $this->assertEquals(2, $retrievedAddress1->ship_receipt);
        
        $retrievedAddress2 = $userAddresses->find($address2->id);
        $this->assertEquals('李四', $retrievedAddress2->ship_name);
        $this->assertEquals('測試公司', $retrievedAddress2->ship_three_company);
    }

    public function testFavoriteAddressModelIncludesShippingFieldsInFillable()
    {
        $favoriteAddress = new FavoriteAddress();
        
        $expectedFillable = [
            'county', 'district', 'address', 'isDefault',
            'ship_name', 'ship_phone', 'ship_email', 'ship_receipt',
            'ship_three_id', 'ship_three_company', 'ship_gender'
        ];
        
        $this->assertEquals($expectedFillable, $favoriteAddress->getFillable());
    }
}