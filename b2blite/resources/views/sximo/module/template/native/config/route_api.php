<?php

            $val_api .= "
// Start Routes for ".$row->module_name." 
Route::resource('services/{$class}','Services\\".$controller."');
// End Routes for ".$row->module_name." 

                    "; 

?>                    