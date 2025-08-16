<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;





class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function kart()
    {
        return $this->hasMany('App\Kart', 'user_id');
    }

    public function groups()
    {
        return $this->hasMany('App\Group', 'dealer_id', 'id');
    }

    public function addresses()
    {
        return $this->hasMany('App\FavoriteAddress', 'user_id');
    }

    public function creditCards()
    {
        return $this->hasMany('App\UserCreditCard', 'user_id');
    }

    public function getDefaultCreditCardAttribute()
    {
        return $this->creditCards()->where('is_default', true)->where('is_active', true)->first();
    }

    /**使用者購物車中的商品id */
    public function kartProductsId()
    {
        $product_id_array = Kart::where('user_id', $this->id)->pluck('product_id');
        return $product_id_array;
    }

    /**使用者購物車中的商品 */
    public function kartProducts()
    {
        $product_id_array = $this->kartProductsId();
        $products = Products::whereIn('id', $product_id_array)->get();
        return $products;
    }

    /**
     * 更新使用者的紅利點數 
     * @param int $amount
     * @param bool $decrease true減少 false增加
     * */
    public function updateBonus($amount, $decrease = true)
    {
        if ($decrease) {
            $this->bonus -= $amount;
        } else {
            $this->bonus += $amount;
        }
        $this->save();
    }
}
