<?php

use Illuminate\Http\Request;

//首頁用
Route::group([
    'prefix' => 'landing',
], function() {
    Route::get('categories', 'CategoryController@index');
});