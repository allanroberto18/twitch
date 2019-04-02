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

Route::group(['prefix' => 'token'], function() {
    Route::get('/access', 'TokenController@accessToken');
    Route::get('/user', 'TokenController@userAccessToken');
});

Route::group(['prefix' => 'streams'], function() {
    Route::get('/popular', 'StreamsController@getMostPopular');
    Route::get('/user', 'StreamsController@getChannel');
});

Route::group(['prefix' => 'auth'], function() {
    Route::get('/url', 'AuthController@getAuthUrl');
});
