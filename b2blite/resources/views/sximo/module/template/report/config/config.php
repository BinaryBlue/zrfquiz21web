<?php

    $template   = base_path().'/resources/views/sximo/module/template/report/';
    $controller = file_get_contents( $template.'controller.tpl' );
    $grid       = file_get_contents(  $template.'grid.tpl' );
    $model       = file_get_contents(  $template.'model.tpl' );  

    $build_controller       = \SiteHelpers::blend($controller,$codes);       
    $build_grid             = \SiteHelpers::blend($grid,$codes);    
    $build_model            = \SiteHelpers::blend($model,$codes);    

    file_put_contents(  $dirC ."{$ctr}Controller.php" , $build_controller) ;    
    file_put_contents(  $dirM ."{$ctr}.php" , $build_model) ;     
    file_put_contents(  $dir."/index.blade.php" , $build_grid) ;  

?>                