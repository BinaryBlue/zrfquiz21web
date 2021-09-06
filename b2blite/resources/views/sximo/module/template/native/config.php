<?php
$codes = $codes;
$template = base_path().'/resources/views/sximo/module/template/native/';
$controller = file_get_contents(  $template.'controller.tpl' );
$grid = file_get_contents(  $template.'grid.tpl' );               
$view = file_get_contents(  $template.'view.tpl' );
$form = file_get_contents(  $template.'form.tpl' );
$model = file_get_contents(  $template.'model.tpl' );
$front = file_get_contents(  $template.'frontend.tpl' );
$frontview = file_get_contents(  $template.'frontendview.tpl' ); 
$frontform = file_get_contents(  $template.'frontform.tpl' );

if(isset($config['subgrid']) && count($config['subgrid'])>=1)
{
     $view = file_get_contents(  $template.'view_detail.tpl' );
} else {
     $view = file_get_contents(  $template.'view.tpl' );
}


$build_controller       = \SiteHelpers::blend($controller,$codes);    
$build_view             = \SiteHelpers::blend($view,$codes);    
$build_form             = \SiteHelpers::blend($form,$codes);    
$build_grid             = \SiteHelpers::blend($grid,$codes);    
$build_model            = \SiteHelpers::blend($model,$codes);    
$build_front            = \SiteHelpers::blend($front,$codes);   
$build_frontview        = \SiteHelpers::blend($frontview,$codes);   
$build_frontform        = \SiteHelpers::blend($frontform,$codes);                

if(!is_null($request->input('rebuild')))
{
    // rebuild spesific files
    if($request->input('c') =='y'){
        file_put_contents( $dirC."{$ctr}Controller.php" , $build_controller) ;    
    }
    if($request->input('m') =='y'){
        file_put_contents(  $dirM."{$ctr}.php" , $build_model) ;
    }    
    
    if($request->input('g') =='y'){
        file_put_contents(  $dir."/index.blade.php" , $build_grid) ;
    }    
    if($row->module_db_key !='')
    {            
        if($request->input('f') =='y'){
            file_put_contents(  $dir."/form.blade.php" , $build_form) ;
        }    


        if($request->input('v') =='y'){
            file_put_contents(  $dir."/view.blade.php" , $build_view) ;
                                         
        }

        // Frontend Grid
        if($request->input('fg') =='y'){
            file_put_contents(  $dir."/public/index.blade.php" , $build_front) ;
        } 
        // Frontend View
        if($request->input('fv') =='y'){
            file_put_contents(  $dir."/public/view.blade.php" , $build_frontview) ;
        } 
        // Frontend Form
        if($request->input('ff') =='y'){
            file_put_contents(  $dir."/public/form.blade.php" , $build_frontform) ;
        } 

    }  




} else {

    file_put_contents(  $dirC ."{$ctr}Controller.php" , $build_controller) ;    
    file_put_contents(  $dirM ."{$ctr}.php" , $build_model) ;
    file_put_contents(  $dir."/index.blade.php" , $build_grid) ; 
    file_put_contents( $dir."/form.blade.php" , $build_form) ;
    file_put_contents(  $dir."/view.blade.php" , $build_view) ;       
    file_put_contents(  $dir."/public/index.blade.php" , $build_front) ;  
    file_put_contents(  $dir."/public/view.blade.php" , $build_frontview) ; 
    file_put_contents(  $dir."/public/form.blade.php" , $build_frontform) ;                                     

}  

?>             