<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCreditCard extends Model
{
    protected $fillable = [
        'user_id', 'card_alias', 'masked_card_number', 'card_holder_name',
        'expiry_month', 'expiry_year', 'card_brand', 'ecpay_member_id',
        'is_default', 'is_active'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getFormattedExpiryAttribute()
    {
        return sprintf('%02d/%d', $this->expiry_month, $this->expiry_year);
    }

    public function getCardBrandImageAttribute()
    {
        $brandImages = [
            'VISA' => 'visa.png',
            'MASTERCARD' => 'mastercard.png',
            'JCB' => 'jcb.png',
            'UNIONPAY' => 'unionpay.png',
        ];
        
        return isset($brandImages[strtoupper($this->card_brand)]) 
            ? $brandImages[strtoupper($this->card_brand)] 
            : 'card-default.png';
    }

    public static function setUserDefaultCard($userId, $cardId)
    {
        self::where('user_id', $userId)->update(['is_default' => false]);
        self::where('id', $cardId)->where('user_id', $userId)->update(['is_default' => true]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}