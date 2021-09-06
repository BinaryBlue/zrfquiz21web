<?php



//-------------------------------------------------------------------------
/* Start Users Routes */
// -- Get Method --
Route::resource('core/users','UsersController');
Route::get('core/users/blast','UsersController@getBlast');
// -- Post Method --
Route::post('core/users/doblast','UsersController@postDoblast');
/* End Users Routes */

//-------------------------------------------------------------------------
/* Start Groups Routes */
// -- Get Method --
Route::resource('core/groups','GroupsController');
/* End Groups Routes */


//-------------------------------------------------------------------------
/* Start Posts & Pages Routes */
// -- Get Method --
Route::resource('cms/pages','PagesController');
Route::resource('cms/posts','PostsController');
Route::post('cms/posts/config','PostsController@postConfig');
Route::resource('cms/categories','CategoriesController');
/* End Posts Routes */

//-------------------------------------------------------------------------
/* Start Logs Routes */
// -- Get Method --
Route::resource('core/logs','LogsController');
/* End Logs Routes */


//-------------------------------------------------------------------------
/* Start Logs Routes */
// -- Get Method --
Route::resource('core/forms','FormsController');

//-------------------------------------------------------------------------
/* Start Elfinder Routes */
// -- Get Method --
Route::get('core/elfinder','ElfinderController@getIndex');
/* End  Elfinder Routes */

