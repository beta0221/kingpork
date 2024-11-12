<?php

use Illuminate\Http\Request;

//身份驗證
Route::group([
    'prefix' => 'auth',
], function() {
    
    Route::post('login', 'NextAuthController@login');
    Route::post('signup', 'NextAuthController@signup');

    Route::group([
        'middleware' => 'auth:api',
    ], function() {
        Route::get('user', 'NextAuthController@user');
        Route::post('logout', 'NextAuthController@logout');
    });
    
});


//首頁用
Route::group([
    'prefix' => 'landing',
], function() {
    Route::get('categories', 'IndexController@categories');
    Route::get('banners', 'IndexController@banners');
});

//聯絡我們
Route::post('contact', 'IndexController@contact');

//購物頁面
Route::group([
    'prefix' => 'shop',
], function() {
    Route::get('paths', 'ShopController@paths');
    Route::get('{slug}', 'ShopController@category');
});

//購物車
Route::group([
    'prefix' => 'kart',
], function() {
    Route::get('items', '_KartController@items');
    Route::post('add', '_KartController@store');
    Route::post('remove/{id}', '_KartController@destroy');
});

//訂單
Route::group([
    'prefix' => 'bill',
], function() {

    Route::group([
        'middleware' => 'auth:api',
    ], function() {
        Route::post('checkout', '_BillController@checkout');
        Route::get('list', '_BillController@list')->name('billList');
        Route::get('/detail/{bill_id}', '_BillController@detail');
        Route::get('/token/{bill_id}', '_BillController@token');
        Route::post('/pay/{bill_id}', '_BillController@pay');
    });
});