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
Route::get('productPage','PageController@showProductPage')->name('showProductPage');
Route::get('/ecom-api', 'PageController@ecomApi')->name('ecomApi');

// product routes
Route::resource('products','ProductController');

// productCategory routes
Route::resource('productCategory','ProductCategoryController',['only'=>['index','store','show']]);
// kart routes
Route::resource('kart','kartController',['only'=>['index','store','destroy']]);
Route::post('inKart','kartController@inKart')->name('getInKart');//for api

//Bill routes
Route::resource('bill','BillController');
Route::get('checkBill/{bill}','BillController@checkBill');

//Banner routes
Route::resource('banner','BannerController');

//Order Management
Route::resource('order','OrderManagementController',['except'=>['create','store']]);
Route::post('order','OrderManagementController@search')->name('order.search');

//php artisan make:auth
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/users/logout','Auth\LoginController@userLogout')->name('user.logout');

//admin routes
Route::get('admin','AdminController@index')->name('admin.dashboard');
Route::get('/admin/login','Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('/admin/login','Auth\AdminLoginController@login')->name('admin.login.submit');
Route::get('/admin/logout','Auth\AdminLoginController@logout')->name('admin.logout');
Route::get('/admin/password/reset','Auth\AdminForgotPasswordController@showLinkRequestForm')->name('admin.password.request');//1
Route::post('/admin/password/email','Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');//2
Route::get('/admin/password/reset/{token}','Auth\AdminResetPasswordController@showResetForm')->name('admin.password.reset');//3
Route::post('/admin/password/reset','Auth\AdminResetPasswordController@reset');//4

