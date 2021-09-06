<?php

            $val .= "
// Start Routes for ".$row->module_name." 
Route::resource('{$class}','{$controller}');
// End Routes for ".$row->module_name." 

                    "; 

?>                    