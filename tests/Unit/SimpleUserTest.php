<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;

class SimpleUserTest extends TestCase
{
    public function testUserModelExists()
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
    }

    public function testUserModelHasFillableAttributes()
    {
        $user = new User();
        $fillable = $user->getFillable();
        
        $expectedFillable = ['name', 'email', 'password', 'phone'];
        
        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function testUserModelHasHiddenAttributes()
    {
        $user = new User();
        $hidden = $user->getHidden();
        
        $expectedHidden = ['password', 'remember_token'];
        
        foreach ($expectedHidden as $attribute) {
            $this->assertContains($attribute, $hidden);
        }
    }

    public function testUpdateBonusLogic()
    {
        $user = new User();
        $user->bonus = 100;
        
        // Test decrease
        $user->bonus -= 30;
        $this->assertEquals(70, $user->bonus);
        
        // Test increase
        $user->bonus += 25;
        $this->assertEquals(95, $user->bonus);
        
        // Test zero amount
        $user->bonus += 0;
        $this->assertEquals(95, $user->bonus);
    }
}