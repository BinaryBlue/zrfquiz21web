<?php
//-------------------------------------------------------------------------
/* Start Module Routes */


Route::get('sximo/module','ModuleController@index');
Route::get('sximo/module/create','ModuleController@getCreate');
Route::get('sximo/module/rebuild/{any}','ModuleController@getRebuild');
Route::get('sximo/module/build/{any}','ModuleController@getBuild');
Route::get('sximo/module/config/{any}','ModuleController@getConfig');
Route::get('sximo/module/sql/{any}','ModuleController@getSql');
Route::get('sximo/module/table/{any}','ModuleController@getTable');
Route::get('sximo/module/form/{any}','ModuleController@getForm');
Route::get('sximo/module/formdesign/{any}','ModuleController@getFormdesign');
Route::get('sximo/module/subform/{any}','ModuleController@getSubform');
Route::get('sximo/module/subformremove/{any}','ModuleController@getSubformremove');
Route::get('sximo/module/sub/{any}','ModuleController@getSub');
Route::get('sximo/module/removesub','ModuleController@getRemovesub');
Route::get('sximo/module/permission/{any}','ModuleController@getPermission');
Route::get('sximo/module/source/{any}','ModuleController@getSource');
Route::get('sximo/module/combotable','ModuleController@getCombotable');
Route::get('sximo/module/combotablefield','ModuleController@getCombotablefield');
Route::get('sximo/module/editform/{any?}','ModuleController@getEditform');
Route::get('sximo/module/destroy/{any?}','ModuleController@getDestroy');
Route::get('sximo/module/conn/{any?}','ModuleController@getConn');
Route::get('sximo/module/code/{any?}','ModuleController@getCode');
Route::get('sximo/module/duplicate/{any?}','ModuleController@getDuplicate');

/* POST METHODE */
Route::post('sximo/module/create','ModuleController@postCreate');
Route::post('sximo/module/saveconfig/{any}','ModuleController@postSaveconfig');
Route::post('sximo/module/savesetting/{any}','ModuleController@postSavesetting');
Route::post('sximo/module/savesql/{any}','ModuleController@postSavesql');
Route::post('sximo/module/savetable/{any}','ModuleController@postSavetable');
Route::post('sximo/module/saveform/{any}','ModuleController@postSaveForm');
Route::post('sximo/module/savesubform/{any}','ModuleController@postSavesubform');
Route::post('sximo/module/formdesign/{any}','ModuleController@postFormdesign');
Route::post('sximo/module/savepermission/{any}','ModuleController@postSavePermission');
Route::post('sximo/module/savesub/{any}','ModuleController@postSaveSub');
Route::post('sximo/module/dobuild/{any}','ModuleController@postDobuild');
Route::post('sximo/module/source/{any}','ModuleController@postSource');
Route::post('sximo/module/install','ModuleController@postInstall');
Route::post('sximo/module/package','ModuleController@postPackage');
Route::post('sximo/module/dopackage','ModuleController@postDopackage');
Route::post('sximo/module/saveformfield/{any?}','ModuleController@postSaveformfield');
Route::post('sximo/module/conn/{any?}','ModuleController@postConn');
Route::post('sximo/module/code/{any?}','ModuleController@postCode');
Route::post('sximo/module/duplicate/{any?}','ModuleController@postDuplicate');



/* End  Module Routes */
//-------------------------------------------------------------------------

/* Start  Code Routes */
Route::get('sximo/code','CodeController@index');
Route::get('sximo/code/edit','CodeController@getEdit');
Route::post('sximo/code/source','CodeController@PostSource');
Route::post('sximo/code/save','CodeController@PostSave');

Route::get('sximo/config/email','ConfigController@getEmail');
Route::get('sximo/config/security','ConfigController@getSecurity');
Route::post('sximo/code/source/:any','ConfigController@postSource');
/* End  Code Routes */

//-------------------------------------------------------------------------
/* Start  Config Routes */
Route::get('sximo/config','ConfigController@getIndex');
Route::get('sximo/config/email','ConfigController@getEmail');
Route::get('sximo/config/security','ConfigController@getSecurity');
Route::get('sximo/config/translation','ConfigController@getTranslation');
Route::get('sximo/config/log','ConfigController@getLog');
Route::get('sximo/config/clearlog','ConfigController@getClearlog');
Route::get('sximo/config/addtranslation','ConfigController@getAddtranslation');
Route::get('sximo/config/removetranslation/{any}','ConfigController@getRemovetranslation');
// POST METHOD
Route::post('sximo/config/save','ConfigController@postSave');
Route::post('sximo/config/email','ConfigController@postEmail');
Route::post('sximo/config/login','ConfigController@postLogin');
Route::post('sximo/config/email','ConfigController@postEmail');
Route::post('sximo/config/addtranslation','ConfigController@postAddtranslation');
Route::post('sximo/config/savetranslation','ConfigController@postSavetranslation');
/* End  Config Routes */

//-------------------------------------------------------------------------
/* Start  Menu Routes */
Route::get('sximo/menu/','MenuController@getIndex');
Route::get('sximo/menu/index/{any?}','MenuController@getIndex');
Route::get('sximo/menu/destroy/{any?}','MenuController@getDestroy');
Route::get('sximo/menu/icon','MenuController@getIcons');

Route::post('sximo/menu/save','MenuController@postSave');
Route::post('sximo/menu/saveorder','MenuController@postSaveorder');
/* End  Config Routes */

//-------------------------------------------------------------------------
/* Start  Tables Routes */
Route::get('sximo/tables','TablesController@index');
Route::get('sximo/tables/tableconfig/{any}','TablesController@getTableconfig');
Route::get('sximo/tables/mysqleditor','TablesController@getMysqleditor');
Route::get('sximo/tables/tableconfig','TablesController@getTableconfig');
Route::get('sximo/tables/tablefieldedit/{any}','TablesController@getTablefieldedit');
Route::get('sximo/tables/tablefieldremove/{id?}/{id2?}','TablesController@getTablefieldremove');
// POST METHOD
Route::post('sximo/tables/tableremove','TablesController@postTableremove');
Route::post('sximo/tables/tableinfo/{any}','TablesController@postTableinfo');
Route::post('sximo/tables/mysqleditor','TablesController@postMysqleditor');
Route::post('sximo/tables/tablefieldsave/{any?}','TablesController@postTablefieldsave');
Route::post('sximo/tables/tables','TablesController@postTables');
/* End  Tables Routes */


//-------------------------------------------------------------------------
/* Start Logs Routes */
// -- Get Method --
Route::resource('sximo/rac','RacController');
Route::get('sximo/rac/show/{any}','RacController@getShow');
Route::get('sximo/rac/update/{any?}','RacController@getUpdate');
Route::get('sximo/rac/comboselect','RacController@getComboselect');
Route::get('sximo/rac/download','RacController@getDownload');
Route::get('sximo/rac/search','RacController@getSearch');

// -- Post Method --
Route::post('sximo/rac/save','RacController@postSave');
Route::post('sximo/rac/filter','RacController@postFilter');
Route::post('sximo/rac/delete/{any?}','RacController@postDelete');
/* End  Tables Routes */

Route::resource('sximo/form','FormController');
Route::resource('sximo/server','ServerController');