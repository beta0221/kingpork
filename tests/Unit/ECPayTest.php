<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\ECPay;
use App\Bill;
use App\User;
use App\Products;
use App\PaymentLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ECPayTest extends TestCase
{
    use DatabaseTransactions;

    protected $bill;
    protected $ecpay;

    protected function setUp(): void
    {
        parent::setUp();
        
        Config::set('ecpay.MerchantId', 'TEST_MERCHANT');
        Config::set('ecpay.HashKey', 'pwFHCqoQZGmho4w6'); // 16 characters
        Config::set('ecpay.HashIV', 'EkRm7iFT261dpevs'); // 16 characters

        $this->bill = factory(Bill::class)->create([
            'bill_id' => 'TEST' . time(),
            'price' => 1000,
            'pay_by' => 'CREDIT',
            'ship_email' => 'test@example.com',
            'ship_phone' => '0912345678',
            'ship_name' => 'Test User'
        ]);
        
        $this->ecpay = new ECPay($this->bill);
    }

    public function testConstructorWithCreditPayment()
    {
        $bill = factory(Bill::class)->create(['pay_by' => 'CREDIT']);
        $ecpay = new ECPay($bill);
        
        $reflection = new \ReflectionClass($ecpay);
        $choosePaymentList = $reflection->getProperty('ChoosePaymentList');
        $choosePaymentList->setAccessible(true);
        
        $this->assertEquals("1", $choosePaymentList->getValue($ecpay));
    }

    public function testConstructorWithATMPayment()
    {
        $bill = factory(Bill::class)->create(['pay_by' => 'ATM']);
        $ecpay = new ECPay($bill);
        
        $reflection = new \ReflectionClass($ecpay);
        $choosePaymentList = $reflection->getProperty('ChoosePaymentList');
        $choosePaymentList->setAccessible(true);
        
        $this->assertEquals("3", $choosePaymentList->getValue($ecpay));
    }

    public function testConstructorWithUserInfo()
    {
        $user = factory(User::class)->create();
        $bill = factory(Bill::class)->create([
            'user_id' => $user->id,
            'ship_email' => 'user@example.com',
            'ship_phone' => '0987654321',
            'ship_name' => 'User Name'
        ]);
        
        $ecpay = new ECPay($bill);
        
        $reflection = new \ReflectionClass($ecpay);
        
        $email = $reflection->getProperty('Email');
        $email->setAccessible(true);
        $this->assertEquals('user@example.com', $email->getValue($ecpay));
        
        $phone = $reflection->getProperty('Phone');
        $phone->setAccessible(true);
        $this->assertEquals('0987654321', $phone->getValue($ecpay));
        
        $name = $reflection->getProperty('Name');
        $name->setAccessible(true);
        $this->assertEquals('User Name', $name->getValue($ecpay));
    }

    public function testEnvironmentEndpoints()
    {
        Config::set('app.env', 'production');
        $bill = factory(Bill::class)->create();
        $ecpay = new ECPay($bill);
        
        $reflection = new \ReflectionClass($ecpay);
        $endpoint = $reflection->getProperty('endpoint_GetTokenbyTrade');
        $endpoint->setAccessible(true);
        
        $this->assertEquals('https://ecpg.ecpay.com.tw/Merchant/GetTokenbyTrade', $endpoint->getValue($ecpay));
    }

    public function testArray2EncryptedString()
    {
        $reflection = new \ReflectionClass($this->ecpay);
        $method = $reflection->getMethod('array2EncryptedString');
        $method->setAccessible(true);
        
        $testArray = ['test' => 'data', 'number' => 123];
        $encrypted = $method->invokeArgs($this->ecpay, [$testArray]);
        
        $this->assertInternalType('string', $encrypted);
        $this->assertNotEmpty($encrypted);
    }

    public function testString2DecryptedArray()
    {
        $reflection = new \ReflectionClass($this->ecpay);
        $encryptMethod = $reflection->getMethod('array2EncryptedString');
        $encryptMethod->setAccessible(true);
        $decryptMethod = $reflection->getMethod('string2DecryptedArray');
        $decryptMethod->setAccessible(true);
        
        $originalArray = ['test' => 'data', 'number' => 123];
        $encrypted = $encryptMethod->invokeArgs($this->ecpay, [$originalArray]);
        $decrypted = $decryptMethod->invokeArgs($this->ecpay, [$encrypted]);
        
        $this->assertEquals($originalArray, $decrypted);
    }

    public function testSetItemName()
    {
        $product1 = factory(Products::class)->create(['name' => 'Product One']);
        $product2 = factory(Products::class)->create(['name' => 'Product Two']);
        
        $this->bill->item = json_encode([
            ['slug' => $product1->slug, 'quantity' => 1],
            ['slug' => $product2->slug, 'quantity' => 2]
        ]);
        $this->bill->save();
        
        $reflection = new \ReflectionClass($this->ecpay);
        $method = $reflection->getMethod('setItemName');
        $method->setAccessible(true);
        $method->invoke($this->ecpay);
        
        $itemName = $reflection->getProperty('ItemName');
        $itemName->setAccessible(true);
        $result = $itemName->getValue($this->ecpay);
        
        $this->assertContains('Product One', $result);
        $this->assertContains('Product Two', $result);
        $this->assertContains('#', $result);
    }

    public function testGetBody()
    {
        $reflection = new \ReflectionClass($this->ecpay);
        $method = $reflection->getMethod('getBody');
        $method->setAccessible(true);
        
        $body = $method->invoke($this->ecpay);
        
        $this->assertArrayHasKey('MerchantID', $body);
        $this->assertArrayHasKey('RqHeader', $body);
        $this->assertArrayHasKey('Timestamp', $body['RqHeader']);
        $this->assertArrayHasKey('Revision', $body['RqHeader']);
        // 根據測試環境選擇期望的 MerchantID
        $expectedMerchantId = (env('APP_ENV') !== 'testing' && env('ECPAY_MERCHANT_ID')) 
            ? env('ECPAY_MERCHANT_ID') 
            : 'TEST_MERCHANT';
        $this->assertEquals($expectedMerchantId, $body['MerchantID']);
        $this->assertEquals('1.0.0', $body['RqHeader']['Revision']);
    }

    public function testGetBodyTradeToken()
    {
        $reflection = new \ReflectionClass($this->ecpay);
        $method = $reflection->getMethod('getBody_TradeToken');
        $method->setAccessible(true);
        
        $jsonBody = $method->invoke($this->ecpay);
        $body = json_decode($jsonBody, true);
        
        $this->assertArrayHasKey('MerchantID', $body);
        $this->assertArrayHasKey('Data', $body);
        $this->assertInternalType('string', $body['Data']);
        $this->assertNotEmpty($body['Data']);
    }

    public function testGetBodyCreatePayment()
    {
        $reflection = new \ReflectionClass($this->ecpay);
        $method = $reflection->getMethod('getBody_CreatePayment');
        $method->setAccessible(true);
        
        $testToken = 'TEST_PAY_TOKEN_123456';
        $jsonBody = $method->invokeArgs($this->ecpay, [$testToken]);
        $body = json_decode($jsonBody, true);
        
        $this->assertArrayHasKey('MerchantID', $body);
        $this->assertArrayHasKey('Data', $body);
        $this->assertInternalType('string', $body['Data']);
        $this->assertNotEmpty($body['Data']);
    }

    public function testHandlePayRequestSuccess()
    {
        $mockRequest = new Request();
        $responseData = [
            'TransCode' => 1,
            'TransMsg' => 'Success',
            'Data' => $this->createMockEncryptedData(['RtnCode' => 1])
        ];
        
        $mockRequest->headers->set('content-type', 'application/json');
        $reflection = new \ReflectionProperty($mockRequest, 'content');
        $reflection->setAccessible(true);
        $reflection->setValue($mockRequest, json_encode($responseData));
        
        $result = $this->ecpay->handlePayRequest($mockRequest);
        
        $this->assertTrue($result);
        
        $this->assertDatabaseHas('payment_logs', [
            'bill_id' => $this->bill->id,
            'type' => PaymentLog::TYPE_PAY_REQUEST,
            'TransCode' => 1,
            'TransMsg' => 'Success'
        ]);
    }

    public function testHandlePayRequestFailure()
    {
        $mockRequest = new Request();
        $responseData = [
            'TransCode' => 0,
            'TransMsg' => 'Failed',
            'Data' => $this->createMockEncryptedData(['RtnCode' => 0])
        ];
        
        $reflection = new \ReflectionProperty($mockRequest, 'content');
        $reflection->setAccessible(true);
        $reflection->setValue($mockRequest, json_encode($responseData));
        
        $result = $this->ecpay->handlePayRequest($mockRequest);
        
        $this->assertFalse($result);
    }

    public function testGetCardInfo()
    {
        $mockRequest = new Request();
        $cardInfo = [
            'MaskedCreditCard' => '1234-****-****-5678',
            'PayMethod' => 'Credit',
            'CardBrand' => 'VISA'
        ];
        $responseData = [
            'Data' => $this->createMockEncryptedData(['CardInfo' => $cardInfo])
        ];
        
        $reflection = new \ReflectionProperty($mockRequest, 'content');
        $reflection->setAccessible(true);
        $reflection->setValue($mockRequest, json_encode($responseData));
        
        $result = $this->ecpay->getCardInfo($mockRequest);
        
        $this->assertEquals($cardInfo, $result);
    }

    public function testGetPaymentInfo()
    {
        $paymentData = [
            'OrderInfo' => ['PaymentType' => 'Credit'],
            'CardInfo' => ['MaskedCreditCard' => '1234-****-****-5678']
        ];
        
        factory(PaymentLog::class)->create([
            'bill_id' => $this->bill->id,
            'type' => PaymentLog::TYPE_CREATE_PAYMENT,
            'Data' => $this->createMockEncryptedData($paymentData)
        ]);
        
        $result = $this->ecpay->getPaymentInfo();
        
        $this->assertArrayHasKey('OrderInfo', $result);
        $this->assertArrayHasKey('CardInfo', $result);
        $this->assertEquals('Credit', $result['OrderInfo']['PaymentType']);
    }

    public function testGetPaymentInfoWhenNoLog()
    {
        $result = $this->ecpay->getPaymentInfo();
        
        $this->assertNull($result);
    }

    private function createMockEncryptedData(array $data)
    {
        $reflection = new \ReflectionClass($this->ecpay);
        $method = $reflection->getMethod('array2EncryptedString');
        $method->setAccessible(true);
        
        return $method->invokeArgs($this->ecpay, [$data]);
    }
}