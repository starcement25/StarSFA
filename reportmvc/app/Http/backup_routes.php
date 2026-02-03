<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|


Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', 'LoginController@showLogin');
Route::post('login','LoginController@doLogin');

Route::group(['middleware' => 'usersession'], function () {
  Route::get('dashboard', 'DashboardController@showDashboard');
  Route::resource('branch', 'BranchController');
  Route::resource('employee', 'EmployeeController');
  Route::resource('vartical', 'VarticalController');
  Route::resource('route', 'RouteController');
  Route::resource('productgroup', 'ProductGroupController');
  Route::resource('productsubgroup', 'ProductSubGroupController');
  Route::resource('productbrand', 'ProductBrandController');
  Route::resource('product', 'ProductController');
  Route::resource('mrp', 'MrpController');
  Route::resource('customer', 'CustomerController');
  Route::resource('customertype', 'CustomerTypeController');
  Route::get('logout', 'LoginController@getLogout');
});
