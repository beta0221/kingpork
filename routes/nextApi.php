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
});