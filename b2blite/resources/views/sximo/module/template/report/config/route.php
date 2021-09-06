<?php

            $val .= "
// Start Routes for ".$row->module_name." 
Route::resource('{$class}','{$controller}');
// -- Post Method --

// End Routes for ".$row->module_name." 

                    "; 

?>                    