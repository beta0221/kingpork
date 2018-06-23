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
Route::get('/contact','PageController@getContact')->name('contact');
Route::get('/productPage','PageController@showProductPage')->name('showProductPage');
Route::get('/guide','PageController@guide')->name('guide');
Route::post('/contactUs','PageController@contactUs');
Route::get('/about-line','PageController@aboutLine');

// product routes
Route::resource('products','ProductController');

// productCategory routes
Route::resource('productCategory','ProductCategoryController');
// kart routes
Route::resource('kart','kartController',['only'=>['index','store','destroy']]);
Route::post('inKart','kartController@inKart')->name('getInKart');//for api
Route::get('checkIfKart/{product}','kartController@checkIfKart');
Route::get('ajaxShowIndex','kartController@ajaxShowIndex');

//Bill routes
Route::resource('bill','BillController');
Route::get('checkBill/{bill}','BillController@checkBill');
Route::post('bill/sendMail','BillController@sendMail')->name('bill.sendMail');
// Route::post('bill/sendMailC','BillController@sendMailC')->name('bill.sendMailC');
Route::get('findMemory','BillController@findMemory')->name('findMemory');


//Banner routes
Route::resource('banner','BannerController');
Route::post('banner/switch','BannerController@switch');

//Runner routes
Route::resource('runner','RunnerController');
Route::post('runner/use','RunnerController@runnerUse');
Route::get('getRunner','RunnerController@getRunner');

//Order Management
Route::resource('order','OrderManagementController',['except'=>['create','store']]);
Route::get('order/showAll/{bill}','OrderManagementController@showAll');
Route::post('order/get_csv','OrderManagementController@csv_download');

//php artisan make:auth
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/users/logout','Auth\LoginController@userLogout')->name('user.logout');
Route::get('memberHash','memberHash@memberHash');

//admin routes
Route::get('admin','AdminController@index')->name('admin.dashboard');
Route::get('/admin/login','Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('/admin/login','Auth\AdminLoginController@login')->name('admin.login.submit');
Route::get('/admin/logout','Auth\AdminLoginController@logout')->name('admin.logout');
Route::get('/admin/password/reset','Auth\AdminForgotPasswordController@showLinkRequestForm')->name('admin.password.request');//1
Route::post('/admin/password/email','Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');//2
Route::get('/admin/password/reset/{token}','Auth\AdminResetPasswordController@showResetForm')->name('admin.password.reset');//3
Route::post('/admin/password/reset','Auth\AdminResetPasswordController@reset');//4

