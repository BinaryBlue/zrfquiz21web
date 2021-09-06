<?php

class FormHelpers {


	public static function render( $id ) 
	{
		$rows = \DB::table('tb_forms')->where('formID',$id)->get();
		if(count($rows) >=1)
		{
			$row = $rows[0];
			$config = json_decode($row->configuration,true);
			
			$form = [] ;

			foreach($config as $key=>$par)
			{
				$par = array_merge(['field' => $key] , $par) ;
				$par = array_merge($par , self::param( $par['param']));
				unset($par['param']);
				$form[$key] = [ 'title'=> $par['title'] , 'form' => self::__input( $par['type'] , '' , $par)];
			}
			$data = array(
				'form_builder_id' 	=> $row->formID ,
				'preview'			=>$form
			);
			return view('sximo.form.preview', $data );
		} else {
			return 'Form does not exists !';
		}
	}

	public static function param( $arguments ){

		$params = array();
		$args = array();
		if($arguments !='') {
			$params = explode("|", $arguments);
			foreach($params as $param)
			{
				$item = explode("=",$param);
				if(isset($item[0]) && isset($item[1]))
				{
					$args[$item[0]] = $item[1];	
				}
				
			}
		}	

		return  $args ;
					
	}

    static function  __input( $type , $value , $param )
    {
	    $validation = '' ;
	    if($param['validation'] !='');
	    	$validation = 'required="true"';
        switch ($type)
        {
            default:
            case 'text':            
            	$form = '<input type="text" name="'.$param['field'].'" value="'. $value .'" class="input-sm form-control" '.$validation.' />';
            	break;
            case 'text':            
            	$form = '<input type="text" name="'.$param['field'].'" value="'. $value .'" class="input-sm form-control CrudEngineColor" '.$validation.' />';
            	break;            	
            case 'password':            
            	$form = '
            	<label> Type Password (*) <small><i> Leave blank if no changes </i></small></label>
            	<input type="password" name="'.$param['field'].'" value="" class="input-sm form-control"  />
            	<label> Confirm Password </label>
            	<input type="password" name="confirm_'.$param['field'].'" value="" class="input-sm form-control"  />
            	';
            	break;

            case 'text':            
            	$form = '<input type="text" name="'.$param['field'].'" value="'. $value .'" class="input-sm form-control" '.$validation.' />';
            	break;
            case 'timestamp': 
            	$form = '<input type="text" name="'.$param['field'].'" value="'. $value .'" class="input-sm form-control CrudEngineDateTime"  '.$validation.'/>';
            	break;
            case 'date': 
            	$form = '
            	<div class="input-group"  style="width:250px !important" >				  
				  <input type="text" class="form-control input-sm CrudEngineDate" name="'.$param['field'].'" value="'. $value .'" '.$validation.'  width="150"/>
				  <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar"></i></span>
				</div>

            	';
            	break;  
            case 'time': 
            	$form = '
            	<div class="input-group"  style="width:250px !important" >				  
				  <input type="text" class="form-control input-sm CrudEngineTime" name="'.$param['field'].'" value="'. $value .'" '.$validation.'  width="150"/>
				  <span class="input-group-addon" id="basic-addon1"><i class="fa fa-clock-o"></i></span>
				</div>
				';
            	break;  
            case 'year': 
            	$form = '
            	<div class="input-group"  style="width:250px !important" >				  
				  <input type="text" class="form-control input-sm CrudEngineYear" name="'.$param['field'].'" value="'. $value .'" '.$validation.'  width="150"/>
				  <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar-check-o"></i></span>
				</div>
				';
            	break;            	          	          		
            case 'datetime': 
            	$form = '
            	<div class="input-group"  style="width:250px !important" >				  
				  <input type="text" class="form-control input-sm CrudEngineDateTime" name="'.$param['field'].'" value="'. $value .'" '.$validation.'  width="150"/>
				  <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar-o"></i></span>
				</div>
				';
            	break;
            case 'editor':
            	$form = '<textarea name="'.$param['field'].'" class=" input-sm form-control CrudEngineEditor" '.$validation.'>'. $value .'</textarea>'; 
            	break;
            case 'textarea':
            	$form = '<textarea name="'.$param['field'].'" class=" input-sm form-control" '.$validation.'>'. $value .'</textarea>'; 
            	break;
            case 'radio':
            	$form ='';
            	$options =array(); 
            	if(isset($param['options']))
            	{
            		$options = explode(',',$param['options']);		
            	}            	         	
            	foreach($options as $opt)
            	{
            		$f = $opt ; $v = $opt ;
            		if (strpos($opt, ':') !== false) {
					    $opt = explode(":",$opt);
					    $f = $opt[0] ; $v = $opt[1];
					} 
            		$checked = ($value == $f ? 'checked' : '');
            		$form .= '<div class="radio"> 
            					<input '.$validation.' type="radio" name="'.$param['field'].'" value="'.$f.'" '.$checked.' > 
            					<label>  '. ucwords($v) .' </label>
            				  </div>  ';	
            	}            	
            	break;
            case 'checkbox':
            	$form ='';
            	$options = explode(',',str_replace("'","",$param['lenght']));
            	$value = explode(',', $value);
            	foreach($options as $opt)
            	{
            		$checked = (in_array($opt , $value ) ? 'checked' : '');
            		$form .= '<label class="checkbox-inline"> <input type="checkbox" '.$validation.' name="'.$param['field'].'[]" value="'.$opt.'" '.$checked.' > </label> '. ucwords($opt) .' ';	
            	}            	
            	break;

            case 'select'; 
            
        		/* End Sximo Bridge */
            	$is_multiple = (isset($param['multiple']) && $param['multiple'] == true ? 'true' : 'false');
            	$value = ($is_multiple =='true' ? explode(',', $value) : $value );
				$mark =''; $is_m ='';
            	$select = '<option value=""> -- Select --</option>';
            	if(isset($param['options']))
            	{
            		$options = explode(',',$param['options']);	
            		foreach($options as $opt)
            		{
            			$opt = explode(":",$opt);
            			$f = $opt[0] ; $v = $opt[1] ;
            			$selected = ($value == $f ? 'selected' : '');
            			if($is_multiple =='true' )
            				$selected = (in_array($f , $value) ? 'selected' : '');
            
						$select .= '<option value="'.$f.'" '.$selected.'>'.$v.'</option>';	
            		}	
            	} 
            	// This is for lookup options
            	if (isset($param['lookup']) )
            	{
	            	$fields = explode(":",$param['lookup']);				
					if(count($fields)>=2)
					{
						$field_table  =  str_replace('-',',',$fields[2]);
						$field_toShow =  explode("-",$fields[2]);
						//echo " SELECT ".$field_table." FROM ".$fields[0]."  "; exit;
						$Q = \DB::select(" SELECT * FROM ".$fields[0]." ");							
						foreach($Q as $row)
						{
							
							$sub_val = '';
							foreach($field_toShow as $fld)
							{
								$sub_val .= $row->{$fld}.' '; 
							}	
							//$sub_val .= substr($value,0,($value-2)
							$selected = ($value == $row->{$fields[1]} ? 'selected' : '');
							if($is_multiple =='true' )
								$selected = (in_array($row->{$fields[1]} , $value) ? 'selected' : '');

							$select .= '<option value="'.$row->{$fields[1]}.'" '.$selected.'>'.$sub_val.'</option>';	
						}						
					}
				}	
				if($is_multiple =='true' )
				{
					$mark = '[]';
					$is_m = 'multiple';
				}			
            	$form = '<select name="'.$param['field']. $mark .'" '.$is_m.' '.$validation.' class="input-sm form-control" >'.$select.'</select>';
            	break;
            case 'upload'; 
            case 'image'; 
            case 'file';  

            	

            	if(isset($param['upload_type']))
            		$type = $param['upload_type'] ;
            	
            	$path = (isset($param['path']) ? $param['path'] : '/uploads/');
				$files = '';
				$values = explode(",",$value);
				$i = 0;
				if(count($values) && $value !='')
				{
					foreach($values as $file) {
						if($type =='image'):
							$show = '<img src="'. asset( $param['path'] . $file ) .'" style="width:100px;" />';
						else :
							$show =  '<i class="fa fa-file"></i><br />'. $file ;
						endif;

						if(file_exists('.'.$path.'/'.$file) && $path !=''){
							$files .= '
							<li id="cr-'.$i.'" class="">							
								<a href="'. asset($param['path'] . $file) .'" target="_blank" >'. $show .'</a> 
								<span class=" removeMultiFiles" rel="#cr-'. $i.'" url="'.$param['path']. $file .'">
								<i class="fa fa-times  btn btn-xs btn-danger"></i></span>
								<input type="hidden" name="curr'.$param['field'].'[]" value="'. $file .'"/>
							</li>';
						}	
						++$i;
					}
				}
				$is_multiple = (isset($param['multiple']) && $param['multiple'] == true ? 'true' : 'false');
            	if(isset($param['image_multiple']) && $param['image_multiple'] =='1')
            	{
            		$is_multiple = 'true' ;	
            	}

            	if($is_multiple =='true')
	            {

				$form = '
					<a href="javascript:void(0)" class="btn btn-xs btn-primary pull-right" onclick="appendFormFiles(\''.$param['field'].'\')"><i class="fa fa-plus"></i></a>
					<div class="'.$param['field'].'Upl">	
					 	<input  type=\'file\' name=\''.$param['field'].'[]\'  />			
					</div>';
				} else {
					$form = '<input type="file" name="'.$param['field'].'" ><br />';
				}

				$form .= '
				<ul class="uploadedLists " >
					'.$files.'	
				</ul>
				';

					
				                 
            	
            	break; 	           	
        }
        return $form;
    
    }		
	 	
}