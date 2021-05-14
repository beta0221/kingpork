<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/billPaied','BillController@billPaied');
Route::post('creditPaied','BillController@creditPaied');

//ecpay 成功付款
Route::post('ecpay/{bill_id}/pay','BillController@api_ecpay_pay')->name('ecpay_ReturnURL');
Route::post('ecpay/{bill_id}/thankyou','BillController@view_ecpay_thankyouPage')->name('ecpay_OrderResultURL');