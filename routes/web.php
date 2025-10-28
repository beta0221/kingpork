<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

//page routes
Route::get('/','PageController@getLanding');

//Kol用連結暫時放這
Route::get('/menustudy','SingleController@showMenustudy')->name('menustudy');

Route::get('/contact','PageController@getContact')->name('contact');
Route::get('/guide','PageController@guide')->name('guide');
Route::post('/contactUs','PageController@contactUs');
Route::get('/about-line','PageController@aboutLine');


// product routes
Route::resource('products','ProductController');
Route::group(['prefix'=>'product'],function(){
    Route::patch('/public/{id}','ProductController@publicProduct');
    Route::get('/{id}/inventory','ProductController@inventory');
    Route::put('/{id}/updateInventory','ProductController@updateInventory');
});

// productCategory routes
Route::resource('productCategory','ProductCategoryController');
Route::get('category/{id}','ProductCategoryController@show');//簡化url
Route::get('/vip/products/{code}','ProductCategoryController@view_vipProducts');
// singleCategory routes
// Route::resource('/buynow','SingleController');
Route::get('/buynow/menustudy/form','SingleController@showToBuyMenuStudy');
Route::post('/buynow/form','SingleController@store');
Route::get('/thankYou/{id}','SingleController@thankYou')->name('thankYou');
Route::get('/searchOrder','SingleController@searchOrder');


// kart routes
Route::resource('kart','kartController',['only'=>['index','store','destroy']]);
Route::post('inKart','kartController@inKart')->name('getInKart');//for api
Route::get('checkIfKart/{product}','kartController@checkIfKart');
Route::group(['prefix' => 'kart'],function(){
    Route::get('getProducts','kartController@getProducts');
});
Route::get('ajaxShowIndex','kartController@ajaxShowIndex');//

//Inventory routes
Route::resource('inventory','InventoryController');

//Retailer routes
Route::resource('retailer','RetailerController');

//PSI routes
Route::group(['prefix'=>'psi'],function(){
    Route::get('/','PSIController@index')->name('psi.index');
    Route::post('/','PSIController@store')->name('psi.store');
    Route::get('/show/{id}','PSIController@show')->name('psi.show');
    Route::get('/report','PSIController@report');
    Route::delete('/reverse/{id}','PSIController@reverseInventoryLog');
});

//Bill routes
Route::resource('bill','BillController');
Route::get('purchaseComplete/{bill_id}','BillController@purchaseComplete')->name('bill.purchaseComplete');
Route::post('bill/sendMail','BillController@sendMail')->name('bill.sendMail');
// Route::post('bill/sendMailC','BillController@sendMailC')->name('bill.sendMailC');
Route::get('findMemory','BillController@findMemory')->name('findMemory');
Route::delete('/bill/cancel/{bill}','BillController@cancelBill');
Route::get('/bill/getDataLayerForGA/{bill_id}','BillController@getDataLayerForGA');  
Route::get('bill/{bill_id}/detail','BillController@view_billDetail')->name('billDetail');
Route::get('bill/{bill_id}/thankyou','BillController@view_billThankyou')->name('billThankyou');
Route::get('bill/{bill_id}/pay','BillController@view_payBill')->name('payBill');
Route::post('bill/{bill_id}/pay','BillController@payBill');

//Banner routes
Route::resource('banner','BannerController');
Route::patch('banner/public/{id}','BannerController@publicBanner');
Route::patch('banner/sort/{id}','BannerController@sortBanner');

//Contact routes
Route::post('toggleStatus/{id}','ContactController@toggleStatus');
Route::get('sumPendingContact','ContactController@sumPendingContact');
Route::resource('contactManage','ContactController');

//Runner routes
Route::resource('runner','RunnerController');
Route::post('runner/use','RunnerController@runnerUse');
Route::get('getRunner','RunnerController@getRunner');

//Order Management
Route::resource('order','OrderManagementController',['except'=>['create','store']]);
Route::post('order/updateShipment','OrderManagementController@updateShipment');
Route::get('order/showAll/{bill}','OrderManagementController@showAll');
Route::post('order/void/{bill_id}','OrderManagementController@voidBill');
Route::post('order/get_csv','OrderManagementController@csv_download');
Route::patch('order/marking/{id}','OrderManagementController@marking');
Route::post('order/ExportExcelForAccountant','OrderManagementController@ExportExcelForAccountant');
Route::post('order/ExportExcelForShipmentNum','OrderManagementController@ExportExcelForShipmentNum');
// Route::post('order/ExportExcelForFamily','OrderManagementController@ExportExcelForFamily');
// Route::post('order/ExportExcelForHCT','OrderManagementController@ExportExcelForHCT');
Route::get('order/export/MonthlyReport/{vendor}/{date}','OrderManagementController@MonthlyReport');
Route::get('order/export/DailyReport/{vendor}/{date}','OrderManagementController@DailyReport');
Route::get('order/stats/bestSeller/{vendor}/{from}/{to}','OrderManagementController@bestSeller');
Route::post('order/uploadKolOrder','OrderManagementController@uploadKolOrder');
Route::post('order/uploadShipmentNum','OrderManagementController@uploadShipmentNum');
Route::delete('order/cancel/{bill_id}','OrderManagementController@cancelBill');
Route::get('order/history/{user_id}','OrderManagementController@orderHistory');
Route::post('regulate/bonus/{user_id}','OrderManagementController@regulateUserBonus');

//php artisan make:auth
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/users/logout','Auth\LoginController@userLogout')->name('user.logout');
Route::get('memberHash','memberHash@memberHash');

//Credit Card Management routes
// Route::group(['middleware' => 'auth'], function() {
//     Route::resource('creditCard', 'CreditCardController', ['except' => ['show']]);
//     Route::post('creditCard/{id}/setDefault', 'CreditCardController@setDefault')->name('creditCard.setDefault');
// });

//admin routes
Route::get('admin','AdminController@index')->name('admin.dashboard');
Route::get('/admin/login','Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('/admin/login','Auth\AdminLoginController@login')->name('admin.login.submit');
Route::get('/admin/logout','Auth\AdminLoginController@logout')->name('admin.logout');
Route::get('/admin/password/reset','Auth\AdminForgotPasswordController@showLinkRequestForm')->name('admin.password.request');//1
Route::post('/admin/password/email','Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');//2
Route::get('/admin/password/reset/{token}','Auth\AdminResetPasswordController@showResetForm')->name('admin.password.reset');//3
Route::post('/admin/password/reset','Auth\AdminResetPasswordController@reset');//4

Route::get('/admin-kingblog','PageController@kingblog');

//Funnel Analytics routes (admin only)
Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function() {
    Route::get('/funnel-analytics', 'FunnelAnalyticsController@index')->name('admin.funnel.index');
    Route::get('/funnel-analytics/export', 'FunnelAnalyticsController@export')->name('admin.funnel.export');
    Route::get('/funnel-analytics/stats', 'FunnelAnalyticsController@stats')->name('admin.funnel.stats');
    Route::get('/funnel-analytics/abandoned-sessions', 'FunnelAnalyticsController@abandonedSessions')->name('admin.funnel.abandoned');

    // Bonus Promotions routes
    Route::resource('bonus-promotions', 'BonusPromotionController', [
        'as' => 'admin',
        'except' => ['show']
    ]);
    Route::patch('bonus-promotions/{id}/toggle', 'BonusPromotionController@toggle')->name('admin.bonus-promotions.toggle');
});


//vip routes
Route::get('/group-buy','GroupBuyController@index');
//private mail server
// Route::get('/asdfzxcv','PageController@skyScanner');

//send gift routes
Route::get('/send-gift','SendGiftController@index');

Route::resource('/group','GroupController');
Route::patch('/group-detail/{group_code}','GroupController@get_group');
Route::post('/join-group','GroupController@join');
Route::get('/group-excel/{group_code}','GroupController@export');





Route::get('/facebook/product/csv','PageController@productFeed');
//Member Excel use
Route::get('/getUserExcel','PageController@getUserExcel');
