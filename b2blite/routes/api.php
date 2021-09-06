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
    //return $request->user();

});
Route::get('services/info', 'Services\SiteController@info');

Route::get('services/cruds', 'Services\SiteController@cruds');
Route::group(['middleware' => 'sximoauth'], function () {
	Route::get('services/profile', 'Services\SiteController@profile');
	Route::post('services/saveprofile', 'Services\SiteController@Saveprofile');
	Route::get('services/notification', 'Services\SiteController@notification');
	include('services.php');
});

