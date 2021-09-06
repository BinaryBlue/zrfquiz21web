<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//User
Route::prefix('/stock')->group(function(){
    Route::get('/recalculate','api\v1\StockController@adjust_products_current_stock');
});

Route::prefix('/reload')->group(function(){
    Route::get('/clear','api\v1\ReloadController@clear');
});
Route::prefix('/user')->group(function(){
    Route::post('/login','api\v1\LoginController@login');
    Route::post('/firebaselogin','api\v1\LoginController@firebaselogin');
    Route::middleware('auth:api')->get('/lists','api\v1\LoginController@userList');

    Route::middleware('auth:api')->post('/assignuser','api\v1\LoginController@assigntooutlet');
});

Route::prefix('/cdn/common')->group(function(){
    Route::middleware('auth:api')->get('/count/notification','api\v1\common\NotificationController@countnotif');


    Route::middleware('auth:api')->get('/suppliers','api\v1\common\SupplierController@index');
    Route::middleware('auth:api')->get('/products','api\v1\common\ProductController@index');
    Route::middleware('auth:api')->get('/products/outletwisebarcode/{code}','api\v1\common\ProductController@outletwisebarcode');
    Route::middleware('auth:api')->get('/productfromcode/{code}','api\v1\common\ProductController@productdetailsfrombarcode');
    Route::middleware('auth:api')->get('/combinationfromcode/{code}','api\v1\common\ProductController@combinationFromCode');
   
    Route::middleware('auth:api')->get('/ecomproducts','api\v1\common\ProductController@ecomindex');
    Route::middleware('auth:api')->get('/ecomproductspaginated','api\v1\common\ProductController@paginatedProduct');
    
    Route::middleware('auth:api')->get('/colors','api\v1\common\ProductController@colors');
    

    Route::middleware('auth:api')->get('/outlets','api\v1\common\OutletController@index');
    Route::middleware('auth:api')->get('/alloutlets','api\v1\common\OutletController@all');
    
    Route::middleware('auth:api')->get('/customers','api\v1\common\CustomerController@index');
    Route::middleware('auth:api')->get('/customerfrommobile/{mobile}','api\v1\common\CustomerController@customerfrommobile');
    Route::middleware('auth:api')->post('/createcustomer','api\v1\common\CustomerController@create');
    Route::middleware('auth:api')->post('/customer/sync','api\v1\common\CustomerController@updateCustomer');
    Route::middleware('auth:api')->get('customer/score/{cid}','api\v1\common\CustomerController@getCustomerScore');
    Route::middleware('auth:api')->get('/customer/idfrommobile/{mobile}','api\v1\common\CustomerController@idfrommobile');

    Route::middleware('auth:api')->get('/paymentmethods','api\v1\common\PaymentmethodController@index');
    Route::middleware('auth:api')->get('/barcodes/{pid}','api\v1\common\BarcodeController@getBarcodesofProduct');
    Route::middleware('auth:api')->get('/barcode/{barcode}','api\v1\common\BarcodeController@getBarcodeDetails');
    Route::middleware('auth:api')->get('/barcode/{outlet}/{barcode}/{status}','api\v1\common\BarcodeController@barcodeofoutlet');
    Route::middleware('auth:api')->post('/oldbarcode','api\v1\common\BarcodeController@getavailableOldbarcode');

    Route::middleware('auth:api')->get('/variables/{pid}','api\v1\common\VariableproductController@getVariables');
    
    Route::middleware('auth:api')->get('/dashboard','api\v1\common\DashboardController@initdata');

});

Route::prefix('/cdn/report')->group(function(){
    Route::middleware('auth:api')->post('/currentstock','api\v1\common\ReportController@stockReport');
});

Route::prefix('/management/sell')->group(function(){
    Route::middleware('auth:api')->post('/entry','api\v1\sell\SellController@init');
    Route::middleware('auth:api')->post('/invoice','api\v1\sell\SellController@getInvoiceDetails');

    Route::middleware('auth:api')->post('/return','api\v1\sell\SellController@return');
});
Route::prefix('/management/stock')->group(function(){
    Route::middleware('auth:api')->get('/reset','api\v1\StockController@reset');

    Route::middleware('auth:api')->post('/entry','api\v1\StockController@entry');
    Route::middleware('auth:api')->post('/oldentry','api\v1\StockController@oldentry');
    Route::middleware('auth:api')->post('/transfer','api\v1\StockController@transferinit');
    Route::middleware('auth:api')->post('/receive','api\v1\StockController@transferconfirm');

    Route::middleware('auth:api')->get('/prevdbentry','api\v1\StockController@preventry');

});

Route::prefix('/management/rollback')->group(function(){
    Route::middleware('auth:api')->get('/stockentry/{id}','api\v1\StockController@entryrollback');

});

Route::prefix('/statement')->group(function(){

    Route::middleware('auth:api')->post('product/return/barcode','api\v1\common\BarcodeController@getArchive');

    Route::middleware('auth:api')->get('stock_entry/details/{id}','api\v1\StockController@statementDetails');
    Route::middleware('auth:api')->get('stock_transfer/details/{id}','api\v1\StockController@transferDetails');
});

Route::prefix('/downloads')->group(function(){
    //Route::middleware('auth:api')->get('barcodes/statement/{id}','api\v1\pdf\BarcodeController@barcodes');
    Route::get('barcodes/statement/{id}','api\v1\pdf\BarcodeController@barcodes');
    Route::get('receipt/sell/invoice/threeinch/{id}','api\v1\pdf\InvoiceController@sellinvoiceThreeinch');
    Route::get('receipt/return/invoice/threeinch/{id}','api\v1\pdf\InvoiceController@sellinvoiceThreeinch');
    Route::post('sellreport','api\v1\pdf\DownloadtController@sellreport');
    Route::post('returnreport','api\v1\pdf\DownloadtController@returnreport');
    Route::post('finalreport','api\v1\pdf\DownloadtController@finalreport');
});

Route::prefix('/ecommerce')->group(function(){ 
    Route::middleware('auth:api')->get('product/all','api\v1\ecommerce\EcommerceController@all_products');
    Route::middleware('auth:api')->get('order/all','api\v1\ecommerce\EcommerceController@all_orders');

    Route::middleware('auth:api')->post('product/create','api\v1\ecommerce\EcommerceController@submit_product');
    Route::middleware('auth:api')->post('product/delete_var','api\v1\ecommerce\EcommerceController@sync_delete_var'); 
    Route::middleware('auth:api')->post('product/softsync','api\v1\ecommerce\EcommerceController@soft_sync'); 
    
});

Route::prefix('/notification')->group(function(){
    Route::middleware('auth:api')->get('test','api\v1\notification\TestnotifController@notiftest');

});

Route::prefix('/order')->group(function(){
    Route::middleware('auth:api')->get('getorderno/{outlet}/{customer}','api\v1\order\OrderController@init');
    
    //Route::middleware('auth:api')->post('productpackaged/','api\v1\order\OrderController@productPackaged');
    Route::middleware('auth:api')->post('productshipped/','api\v1\order\OrderController@productShipped');
    Route::middleware('auth:api')->post('makepending/','api\v1\order\OrderController@makePending');
    Route::middleware('auth:api')->post('makeconfirmed','api\v1\order\OrderController@makeConfirmed');
    Route::middleware('auth:api')->post('makecompleted','api\v1\order\OrderController@makeCompleted');
    Route::middleware('auth:api')->post('makecanceled','api\v1\order\OrderController@makeCanceled');

    Route::middleware('auth:api')->post('itemupdate/','api\v1\order\OrderController@itemupdate');
    Route::middleware('auth:api')->post('deleteitem/','api\v1\order\OrderController@deleteitem');
    Route::middleware('auth:api')->post('confirmtransfer/','api\v1\order\OrderController@confirmTransfer');
    Route::middleware('auth:api')->post('receivetransfer/','api\v1\order\OrderController@receiveTransfer');
    Route::middleware('auth:api')->get('receivealltransfers/{outlet}','api\v1\order\OrderController@getAllReceiveTransfers');
    

    Route::middleware('auth:api')->get('pendingtransfers/{outlet}','api\v1\order\OrderController@getPendingTransfers');
    Route::middleware('auth:api')->get('outletorders/{outlet}/{fromDate}/{toDate}','api\v1\order\OrderController@getOutletOrders');
    Route::middleware('auth:api')->get('dueBills/{outlet}/{fromDate}/{toDate}','api\v1\order\OrderController@getDueOrders');
    Route::middleware('auth:api')->get('get/{order}','api\v1\order\OrderController@getOrder');

    Route::middleware('auth:api')->get('incompleteOrders','api\v1\order\OrderController@getAllOrdersOfLastMonth');
    //public function getOrder($order){
    Route::get('print/threeinch/{order}','api\v1\order\OrderController@printThreeInchOrder'); 
    Route::get('print/a4/{order}','api\v1\order\OrderController@printA4Order'); 


});

Route::prefix('/delivery')->group(function(){
    Route::middleware('auth:api')->get('getchannel','api\v1\delivery\DeliveryController@getDeliveryChannel');
    Route::middleware('auth:api')->get('getmypackets','api\v1\delivery\DeliveryController@getMyPackets');
    Route::middleware('auth:api')->get('getmyshipments','api\v1\delivery\DeliveryController@getMyShipments');
    Route::middleware('auth:api')->post('makeshipment','api\v1\delivery\DeliveryController@makeShipment');
    Route::middleware('auth:api')->post('shipmentrequested','api\v1\delivery\DeliveryController@shipmentRequested');
    Route::middleware('auth:api')->post('shipmentcompleted','api\v1\delivery\DeliveryController@shipmentCompleted');

    Route::middleware('auth:api')->post('approve','api\v1\delivery\DeliveryController@approveHandler');
});

Route::prefix('/packaging')->group(function(){
    Route::middleware('auth:api')->post('makepackage','api\v1\packaging\PackagingController@makePacket');
    Route::middleware('auth:api')->post('discard','api\v1\packaging\PackagingController@discardPacket');
    Route::middleware('auth:api')->post('action','api\v1\packaging\PackagingController@actionHandler');
    Route::middleware('auth:api')->post('addremarks','api\v1\packaging\PackagingController@__insertRemarks');
    Route::middleware('auth:api')->post('assignboy','api\v1\packaging\PackagingController@assignBoyToPacket');
    
    //Route::middleware('auth:api')->post('updatepackage','api\v1\packaging\DeliveryController@shipmentCompleted');
});

Route::prefix('/expense')->group(function(){
    Route::middleware('auth:api')->post('entry','api\v1\expense\ExpenseController@entry');
});

Route::prefix('/callcenter')->group(function(){
    Route::middleware('auth:api')->post('product/base/details','api\v1\callcenter\ProductController@base_details');
    Route::middleware('auth:api')->post('product/variation/details','api\v1\callcenter\ProductController@product_details');
    Route::middleware('auth:api')->post('product/base/barcodes','api\v1\callcenter\ProductController@barcodes');
    

});

Route::prefix('/utility')->group(function(){
    Route::middleware('auth:api')->post('barcode/makesellable','api\v1\utility\UtilityController@makesellable');
    Route::middleware('auth:api')->post('barcode/makedamage','api\v1\utility\UtilityController@makedamage');
    Route::middleware('auth:api')->post('getimage','api\v1\utility\UtilityController@getImage');
    
    
});

Route::prefix('/firebase')->group(function(){
    Route::middleware('auth:api')->get('participents','api\v1\firebase\FirebaseController@participents'); 
});

Route::prefix('/payment')->group(function(){
    Route::middleware('auth:api')->post('list','api\v1\payment\PaymentController@getPaymentList');
    Route::middleware('auth:api')->post('deposit','api\v1\payment\PaymentController@makePayment');
});

Route::prefix('/test')->group(function(){
    Route::middleware('auth:api')->get('test/{statement}/{type}','api\v1\sell\SellController@profit_adjust');

});