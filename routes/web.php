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

Route::get('/', function () {
    return view('welcome');
});
Route::get('index',[
    'as'=>'trang-chu',
    'uses'=>'PageController@getIndex'
]);
Route::get('loai-san-pham/{type}',[
    'as'=>'loaisanpham',
    'uses'=>'PageController@getLoaiSP'
]);
Route::get('chi-tiet-san-pham/{id}',[
    'as'=>'chitietsanpham',
    'uses'=>'PageController@getChiTiet'
]);
Route::get('lien-he',[
    'as' =>'lienhe',
    'uses'=>'PageController@getLienHe'
]);
Route::get('gioi-thieu',[
    'as' =>'gioithieu',
    'uses'=>'PageController@getGioiThieu'
]);
Route::get('add-to-cart/{id}',[
    'as'=>'themgiohang',
    'uses'=>'PageController@getAddToCart'
]);
Route::get('delete-cart/{id}',[
    'as'=>'xoagiohang',
    'uses'=>'PageController@getDelItemCart'
]);
Route::get('dat-hang',[
    'as'=>'dat-hang',
    'uses'=>'PageController@getCheckout'
]);
Route::post('dat-hang',[
    'as'=>'dat-hang',
    'uses'=>'PageController@postCheckout'
]);

Route::get('dang-nhap',[
    'as'=>'login',
    'uses'=>'PageController@getLogin'
]);
Route::get('dang-ky',[
    'as'=>'register',
    'uses'=>'PageController@getRegister'
]);
Route::post('dang-ky',[
    'as'=>'register',
    'uses'=>'PageController@postRegister'
]);
Route::post('dang-nhap',[
    'as'=>'login',
    'uses'=>'PageController@postLogin'
]);

// Auth::routes();

//  Route::get('/home', 'PageController@getIndex')->name('home');
Route::get('dang-xuat',[
    'as'=>'logout',
    'uses'=>'PageController@getLogout'
]);

Route::get('search',[
    'as'=>'search',
    'uses'=>'PageController@getSearch'
]);
