<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Bill;
use App\User;
use App\UserCreditCard;
use App\PaymentLog;
use App\Helpers\ECPay;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ECPayIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $bill;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 統一的 ECPay 測試配置邏輯
        Config::set('ecpay.MerchantId', 'TEST_MERCHANT');
        Config::set('ecpay.HashKey', 'pwFHCqoQZGmho4w6'); // 16 characters
        Config::set('ecpay.HashIV', 'EkRm7iFT261dpevs'); // 16 characters

        $this->user = factory(User::class)->create();
        $this->bill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'pay_by' => 'CREDIT',
            'price' => 1500,
            'ship_email' => 'test@example.com',
            'ship_phone' => '0912345678',
            'ship_name' => 'Test User'
        ]);
    }

    public function testECPayPaymentCreationFlow()
    {
        $ecpay = new ECPay($this->bill);
        
        $reflection = new \ReflectionClass($ecpay);
        $merchantTradeNo = $reflection->getProperty('MerchantTradeNo');
        $merchantTradeNo->setAccessible(true);
        
        $this->assertEquals($this->bill->bill_id, $merchantTradeNo->getValue($ecpay));
        
        $totalAmount = $reflection->getProperty('TotalAmount');
        $totalAmount->setAccessible(true);
        
        $this->assertEquals(1500, $totalAmount->getValue($ecpay));
    }

    public function testECPayCallbackHandlingSuccess()
    {
        $mockRequest = $this->createMockECPayRequest([
            'TransCode' => 1,
            'TransMsg' => 'Success',
            'Data' => $this->createEncryptedData(['RtnCode' => 1, 'TradeNo' => 'TEST12345'])
        ]);
        
        $ecpay = new ECPay($this->bill);
        $result = $ecpay->handlePayRequest($mockRequest);
        
        $this->assertTrue($result);
        
        $this->assertDatabaseHas('payment_logs', [
            'bill_id' => $this->bill->id,
            'type' => PaymentLog::TYPE_PAY_REQUEST,
            'TransCode' => 1,
            'TransMsg' => 'Success'
        ]);
    }

    public function testECPayCallbackHandlingFailure()
    {
        $mockRequest = $this->createMockECPayRequest([
            'TransCode' => 0,
            'TransMsg' => 'Payment Failed',
            'Data' => $this->createEncryptedData(['RtnCode' => 0, 'RtnMsg' => 'Card declined'])
        ]);
        
        $ecpay = new ECPay($this->bill);
        $result = $ecpay->handlePayRequest($mockRequest);
        
        $this->assertFalse($result);
        
        $this->assertDatabaseHas('payment_logs', [
            'bill_id' => $this->bill->id,
            'type' => PaymentLog::TYPE_PAY_REQUEST,
            'TransCode' => 0,
            'TransMsg' => 'Payment Failed'
        ]);
    }

    public function testECPayCreditCardSaveFunction()
    {
        $cardInfo = [
            'MaskedCreditCard' => '1234-****-****-5678',
            'CardBrand' => 'VISA',
            'CardHolder' => 'TEST USER',
            'CardExpiry' => '12/25',
            'ECPayMemberID' => 'ECM123456789'
        ];
        
        $mockRequest = $this->createMockECPayRequest([
            'Data' => $this->createEncryptedData(['CardInfo' => $cardInfo])
        ]);
        
        $this->bill->update(['save_credit_card' => true]);
        
        $ecpay = new ECPay($this->bill);
        $retrievedCardInfo = $ecpay->getCardInfo($mockRequest);
        
        $this->assertEquals($cardInfo, $retrievedCardInfo);
    }

    public function testSavedCreditCardPayment()
    {
        $creditCard = factory(UserCreditCard::class)->create([
            'user_id' => $this->user->id,
            'card_alias' => 'My VISA Card',
            'masked_card_number' => '4111-****-****-1111',
            'card_brand' => 'VISA',
            'is_default' => true,
            'is_active' => true
        ]);
        
        $bill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'used_credit_card_id' => $creditCard->id,
            'pay_by' => 'CREDIT',
            'price' => 800
        ]);
        
        $ecpay = new ECPay($bill);
        
        $reflection = new \ReflectionClass($ecpay);
        $merchantMemberID = $reflection->getProperty('MerchantMemberID');
        $merchantMemberID->setAccessible(true);
        
        $this->assertEquals('USER_' . $this->user->id, $merchantMemberID->getValue($ecpay));
    }

    public function testECPayErrorHandling()
    {
        $mockRequest = $this->createMockECPayRequest([
            'TransCode' => 999,
            'TransMsg' => 'System Error',
            'Data' => $this->createEncryptedData(['RtnCode' => 999, 'RtnMsg' => 'Internal server error'])
        ]);
        
        $ecpay = new ECPay($this->bill);
        $result = $ecpay->handlePayRequest($mockRequest);
        
        $this->assertFalse($result);
    }

    public function testECPayATMPaymentFlow()
    {
        $atmBill = factory(Bill::class)->create([
            'user_id' => $this->user->id,
            'pay_by' => 'ATM',
            'price' => 2000
        ]);
        
        $ecpay = new ECPay($atmBill);
        
        $reflection = new \ReflectionClass($ecpay);
        $choosePaymentList = $reflection->getProperty('ChoosePaymentList');
        $choosePaymentList->setAccessible(true);
        
        $this->assertEquals("3", $choosePaymentList->getValue($ecpay));
    }

    public function testECPayProductionEnvironment()
    {
        Config::set('app.env', 'production');
        
        $ecpay = new ECPay($this->bill);
        
        $reflection = new \ReflectionClass($ecpay);
        $endpoint = $reflection->getProperty('endpoint_GetTokenbyTrade');
        $endpoint->setAccessible(true);
        
        $this->assertEquals('https://ecpg.ecpay.com.tw/Merchant/GetTokenbyTrade', $endpoint->getValue($ecpay));
        
        $createEndpoint = $reflection->getProperty('endpoint_CreatePayment');
        $createEndpoint->setAccessible(true);
        
        $this->assertEquals('https://ecpg.ecpay.com.tw/Merchant/CreatePayment', $createEndpoint->getValue($ecpay));
    }

    public function testECPayStagingEnvironment()
    {
        Config::set('app.env', 'staging');
        
        $ecpay = new ECPay($this->bill);
        
        $reflection = new \ReflectionClass($ecpay);
        $endpoint = $reflection->getProperty('endpoint_GetTokenbyTrade');
        $endpoint->setAccessible(true);
        
        $this->assertEquals('https://ecpg-stage.ecpay.com.tw/Merchant/GetTokenbyTrade', $endpoint->getValue($ecpay));
    }

    public function testECPayInvalidRequestFormat()
    {
        $mockRequest = new Request();
        $reflection = new \ReflectionProperty($mockRequest, 'content');
        $reflection->setAccessible(true);
        $reflection->setValue($mockRequest, 'invalid json content');
        
        $ecpay = new ECPay($this->bill);
        $result = $ecpay->handlePayRequest($mockRequest);
        
        $this->assertFalse($result);
    }

    public function testECPayMissingDataField()
    {
        $mockRequest = $this->createMockECPayRequest([
            'TransCode' => 1,
            'TransMsg' => 'Success'
            // Missing 'Data' field
        ]);
        
        $ecpay = new ECPay($this->bill);
        $result = $ecpay->handlePayRequest($mockRequest);
        
        $this->assertFalse($result);
    }

    public function testECPayGetPaymentInfoFromLogs()
    {
        $paymentData = [
            'OrderInfo' => [
                'PaymentType' => 'Credit',
                'TradeNo' => 'TEST12345',
                'TotalAmount' => 1500
            ],
            'CardInfo' => [
                'MaskedCreditCard' => '4111-****-****-1111',
                'CardBrand' => 'VISA'
            ]
        ];
        
        factory(PaymentLog::class)->create([
            'bill_id' => $this->bill->id,
            'type' => PaymentLog::TYPE_CREATE_PAYMENT,
            'TransCode' => 1,
            'TransMsg' => 'Success',
            'Data' => $this->createEncryptedData($paymentData)
        ]);
        
        $ecpay = new ECPay($this->bill);
        $result = $ecpay->getPaymentInfo();
        
        $this->assertArrayHasKey('OrderInfo', $result);
        $this->assertArrayHasKey('CardInfo', $result);
        $this->assertEquals('Credit', $result['OrderInfo']['PaymentType']);
        $this->assertEquals('VISA', $result['CardInfo']['CardBrand']);
    }

    public function testECPayThreeDSecureFlow()
    {
        $threeDData = [
            'ThreeDInfo' => [
                'ThreeDURL' => 'https://bank.example.com/3dsecure'
            ],
            'OrderInfo' => [
                'PaymentType' => 'Credit'
            ]
        ];
        
        factory(PaymentLog::class)->create([
            'bill_id' => $this->bill->id,
            'type' => PaymentLog::TYPE_CREATE_PAYMENT,
            'Data' => $this->createEncryptedData($threeDData)
        ]);
        
        $ecpay = new ECPay($this->bill);
        $paymentInfo = $ecpay->getPaymentInfo();
        
        $this->assertArrayHasKey('ThreeDInfo', $paymentInfo);
        $this->assertEquals('https://bank.example.com/3dsecure', $paymentInfo['ThreeDInfo']['ThreeDURL']);
    }

    public function testECPayEncryptionDecryption()
    {
        $originalData = [
            'OrderInfo' => ['TradeNo' => 'TEST123'],
            'CardInfo' => ['Brand' => 'VISA'],
            'Amount' => 1500
        ];
        
        $ecpay = new ECPay($this->bill);
        
        $reflection = new \ReflectionClass($ecpay);
        $encryptMethod = $reflection->getMethod('array2EncryptedString');
        $encryptMethod->setAccessible(true);
        $decryptMethod = $reflection->getMethod('string2DecryptedArray');
        $decryptMethod->setAccessible(true);
        
        $encrypted = $encryptMethod->invokeArgs($ecpay, [$originalData]);
        $decrypted = $decryptMethod->invokeArgs($ecpay, [$encrypted]);
        
        $this->assertEquals($originalData, $decrypted);
    }

    private function createMockECPayRequest(array $data): Request
    {
        $mockRequest = new Request();
        $reflection = new \ReflectionProperty($mockRequest, 'content');
        $reflection->setAccessible(true);
        $reflection->setValue($mockRequest, json_encode($data));
        
        return $mockRequest;
    }

    private function createEncryptedData(array $data): string
    {
        $ecpay = new ECPay($this->bill);
        $reflection = new \ReflectionClass($ecpay);
        $method = $reflection->getMethod('array2EncryptedString');
        $method->setAccessible(true);
        
        return $method->invokeArgs($ecpay, [$data]);
    }
}