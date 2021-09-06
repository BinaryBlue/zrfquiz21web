<?php
                // Do CRUD using Ajax
                $template = base_path().'/resources/views/sximo/module/template/ajax/';
                $controller = file_get_contents(  $template.'controller.tpl' );
                $grid = file_get_contents(  $template.'grid.tpl' );                
                if(isset($config['subgrid']) && count($config['subgrid'])>=1)
                {
                     $view = file_get_contents(  $template.'view_detail.tpl' );
                } else {
                     $view = file_get_contents(  $template.'view.tpl' );
                }               

                $form = file_get_contents(  $template.'form.tpl' );
                $model = file_get_contents(  $template.'model.tpl' );    
                $table = file_get_contents(  $template.'table.tpl' );
                $toolbar = file_get_contents(  $template.'toolbar.tpl' );   
                $front = file_get_contents(  $template.'frontend.tpl' );
                $frontview = file_get_contents(  $template.'frontendview.tpl' ); 
                $frontform = file_get_contents(  $template.'frontform.tpl' );
                     
                if($row->module_db_key =='')
                {
                    $controller = file_get_contents(  $template.'controller_view.tpl' );
                    $grid = file_get_contents(  $template.'grid_view.tpl' );
                    $toolbar = file_get_contents(  $template.'toolbar_view.tpl' );
                    $table = file_get_contents(  $template.'table_view.tpl' );
                    
                } 



                $build_controller       = \SiteHelpers::blend($controller,$codes);    
                $build_view             = \SiteHelpers::blend($view,$codes);    
                $build_form             = \SiteHelpers::blend($form,$codes);    
                $build_grid             = \SiteHelpers::blend($grid,$codes);    
                $build_table            = \SiteHelpers::blend($table,$codes);    
                $build_model            = \SiteHelpers::blend($model,$codes);
                $build_toolbar          = \SiteHelpers::blend($toolbar,$codes);       
                $build_front            = \SiteHelpers::blend($front,$codes);   
                $build_frontview        = \SiteHelpers::blend($frontview,$codes);   
                $build_frontform        = \SiteHelpers::blend($frontform,$codes);                                
                           

                  if(!is_null($request->get('rebuild')))
                {
                     file_put_contents( $dirC."{$ctr}Controller.php" , $build_controller) ;    
                    // rebuild spesific files
                    if($request->input('c') =='y') file_put_contents(  $dirC."{$ctr}Controller.php" , $build_controller) ;    
                    if($request->input('m') =='y') file_put_contents(  $dirM."{$ctr}.php" , $build_model) ;                
                    if($request->input('g') =='y') file_put_contents(  $dir."/index.blade.php" , $build_grid) ;
                    if($request->input('g') =='y') file_put_contents(  $dir."/table.blade.php" , $build_grid) ;
                    if($row->module_db_key !='')
                    {        
                        if($request->input('f') =='y') file_put_contents( $dir."/form.blade.php" , $build_form) ;
                        if($request->input('v') =='y') {
                             if(isset($config['subgrid']) && count($config['subgrid'])>=1)
                             {
                                file_put_contents(  $dir."/view_detail.blade.php" , $build_view) ;
                             } else {
                                file_put_contents(  $dir."/view.blade.php" , $build_view) ;
                             }                            
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
                                       
                    file_put_contents(  $dirC."{$ctr}Controller.php" , $build_controller) ;    
                    file_put_contents(  $dirM."{$ctr}.php" , $build_model) ;
                    file_put_contents(  $dir."/index.blade.php" , $build_grid) ;                    
                    file_put_contents(  $dir."/form.blade.php" , $build_form) ;
                    file_put_contents(  $dir."/view.blade.php" , $build_view) ;
                    file_put_contents(  $dir."/table.blade.php" , $build_table) ;
                    file_put_contents(  $dir."/toolbar.blade.php" , $build_toolbar) ;
                    file_put_contents(  $dir."/public/index.blade.php" , $build_front) ;  
                    file_put_contents(  $dir."/public/view.blade.php" , $build_frontview) ; 
                    file_put_contents(  $dir."/public/form.blade.php" , $build_frontform) ;                                       
                }  
                  
  ?>              