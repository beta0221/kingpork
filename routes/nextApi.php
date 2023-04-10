<?php

use Illuminate\Http\Request;

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
    Route::get('items', '_KartController@getProducts');
    Route::post('add', '_KartController@store');
    Route::post('remove', '_KartController@destroy');
});