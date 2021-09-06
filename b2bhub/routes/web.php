<?php

use Illuminate\Support\Facades\Route;

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

Route::get('downloads/barcodes/statement/{id}','api\v1\pdf\BarcodeController@barcodes');

Route::get('getbarcode/{product_code}','api\v1\ProductarchiveController@getBarcodeNumber');


Route::post('/botman',function(){
    app('botman')->listen();
});