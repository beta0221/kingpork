<?php

use Illuminate\Http\Request;

//身份驗證
Route::group([
    'prefix' => 'auth',
], function() {
    
    Route::post('login', 'NextAuthController@login');

    Route::group([
        'middleware' => 'auth:api',
    ], function() {
        Route::get('user', 'NextAuthController@user')->middleware('auth:api');
    });
    
});


//首頁用
Route::group([
    'prefix' => 'landing',
], function() {
    Route::get('categories', 'IndexController@categories');
    Route::get('banners', 'IndexController@banners');
});

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