<?php namespace App\Http\Controllers;
use Mail;
use Carbon\Carbon;
use PDF ;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Validator, Input, Redirect ;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

abstract class Controller extends BaseController {

	use DispatchesJobs, ValidatesRequests;

	protected $user_id ;
	protected $users ;

	public function __construct()
	{
		
		$this->middleware('ipblocked');
		$driver             = config('database.default');
        $database           = config('database.connections');
       
        $this->db           = $database[$driver]['database'];
        $this->dbuser       = $database[$driver]['username'];
        $this->dbpass       = $database[$driver]['password'];
        $this->dbhost       = $database[$driver]['host']; 

        // Load Sximo Config
        $sximo 	= config('sximo');
        $this->config = $sximo ;
        $this->data['sximoconfig'] = $sximo ;  
	} 	


	function getComboselect( Request $request)
	{

		if($request->ajax() == true && \Auth::check() == true)
		{
			$param = explode(':',$request->input('filter'));
			$parent = (!is_null($request->input('parent')) ? $request->input('parent') : null);
			$limit = (!is_null($request->input('limit')) ? $request->input('limit') : null);
			$rows = $this->model->getComboselect($param,$limit,$parent);
			$items = array();
		
			$fields = explode("|",$param[2]);
			
			foreach($rows as $row) 
			{
				$value = "";
				foreach($fields as $item=>$val)
				{
					if($val != "") $value .= $row->{$val}." ";
				}
				$items[] = array($row->{$param['1']} , $value); 	
	
			}
			
			return json_encode($items); 	
		} else {
			return json_encode(array('OMG'=>" Ops .. Cant access the page !"));
		}	
	}

	public function getCombotable( Request $request)
	{
		if(Request::ajax() == true && Auth::check() == true)
		{				
			$rows = $this->model->getTableList($this->db);
			$items = array();
			foreach($rows as $row) $items[] = array($row , $row); 	
			return json_encode($items); 	
		} else {
			return json_encode(array('OMG'=>"  Ops .. Cant access the page !"));
		}				
	}		
	
	public function getCombotablefield( Request $request)
	{
		if($request->input('table') =='') return json_encode(array());	
		if(Request::ajax() == true && Auth::check() == true)
		{	

				
			$items = array();
			$table = $request->input('table');
			if($table !='')
			{
				$rows = $this->model->getTableField($request->input('table'));			
				foreach($rows as $row) 
					$items[] = array($row , $row); 				 	
			} 
			return json_encode($items);	
		} else {
			return json_encode(array('OMG'=>"  Ops .. Cant access the page !"));
		}					
	}

	function postMultisearch( Request $request)
	{
		$post = $_POST;
		$items ='';
		foreach($post as $item=>$val):
			if($_POST[$item] !='' and $item !='_token' and $item !='md' && $item !='id'):
				$items .= $item.':'.trim($val).'|';
			endif;	
		
		endforeach;
		
		return Redirect::to($this->module.'?search='.substr($items,0,strlen($items)-1).'&md='.$request->inpuy('md'));
	}

	function buildSearch( $map = false)
	{

			$keywords = ''; $fields = '';	$param ='';
			$allowsearch = $this->info['config']['forms'];
			foreach($allowsearch as $as) $arr[$as['field']] = $as ;		
			$mapping = '';
			if($_GET['search'] !='')
			{
				$type = explode("|",$_GET['search'] );
				if(count($type) >= 1)
				{
					foreach($type as $t)
					{
						$keys = explode(":",$t);
						if(in_array($keys[0],array_keys($arr))):
							if($arr[$keys[0]]['type'] == 'select' || $arr[$keys[0]]['type'] == 'radio' )
							{
								$param .= " AND ".$arr[$keys[0]]['alias'].".".$keys[0]." ".self::searchOperation($keys[1])." '".$keys[2]."' ";	
								$mapping .= $keys[0].' '.self::searchOperation($keys[1]).' '.$keys[2]. '<br />';

							} else {
								$operate = self::searchOperation($keys[1]);
								if($operate == 'like')
								{
									$param .= " AND ".$arr[$keys[0]]['alias'].".".$keys[0]." REGEXP '".$keys[2]."' ";	
									$mapping .= $keys[0].' LIKE '.$keys[2]. '<br />';	
								} else if( $operate =='is_null') {
									$param .= " AND ".$arr[$keys[0]]['alias'].".".$keys[0]." IS NULL ";
									$mapping .= $keys[0].' IS NULL <br />';

								} else if( $operate =='not_null') {
									$param .= " AND ".$arr[$keys[0]]['alias'].".".$keys[0]." IS NOT NULL ";
									$mapping .= $keys[0].' IS NOT NULL <br />';

								} else if( $operate =='between') {
									$param .= " AND (".$arr[$keys[0]]['alias'].".".$keys[0]." BETWEEN '".$keys[2]."' AND '".$keys[3]."' ) ";								
									$mapping .= $keys[0].' BETWEEN '.$keys[2]. ' - '. $keys[3] .'<br />';
								} else {
									$param .= " AND ".$arr[$keys[0]]['alias'].".".$keys[0]." ".self::searchOperation($keys[1])." '".$keys[2]."' ";
									$mapping .= $keys[0].' '.self::searchOperation($keys[1]).' '.$keys[2]. '<br />';	
								}												
							}						
						endif;	
					}
				} 
			}

		if($map == true)
		{
			return $param = array(
					'param'	=> $param,
					'maps'	=> '
					<div class="infobox infobox-info fade in" style="font-size:10px;">
					  <button data-dismiss="alert" class="close" type="button"> x </button>  
					 <b class="text-danger"> Search Result </b> :  <br /> '.$mapping.'
					</div>
					'
				);			

		} else {
			return $param;
		}		
		
	
	}


	function onSearch( $params )
	{
		// Used for extracting URL GET search 
		$psearch = explode('|',$params);
		$currentSearch = array();
		foreach($psearch as $ps)
		{
			$tosearch = explode(':',$ps);
			if(count($tosearch) >=2)
			$currentSearch[$tosearch[0]] = $tosearch[2]; 
		}
		return $currentSearch;		
	}
	
	function searchOperation( $operate)
	{
		$val = '';
		switch ($operate) {
			case 'equal':
				$val = '=' ;
				break;
			case 'bigger_equal':
				$val = '>=' ;
				break;
			case 'smaller_equal':
				$val = '<=' ;
				break;				
			case 'smaller':
				$val = '<' ;
				break;
			case 'bigger':
				$val = '>' ;
				break;
			case 'not_null':
				$val = 'not_null' ;
				break;								

			case 'is_null':
				$val = 'is_null' ;
				break;	

			case 'like':
				$val = 'like' ;
				break;	

			case 'between':
				$val = 'between' ;
				break;					

			default:
				$val = '=' ;
				break;
		}
		return $val;
	}		

	function inputLogs(Request $request, $note = NULL)
	{
		$data = array(
			'module'	=> $request->segment(1),
			'task'		=> $request->segment(2),
			'user_id'	=> Session::get('uid'),
			'ipaddress'	=> $request->getClientIp(),
			'note'		=> $note
		);
		\DB::table( 'tb_logs')->insert($data);		;
	
	}

	function validateForm( $forms = array() )
	{
		if(count($forms) <= 0)
			$forms = $this->info['config']['forms'];
		
		//print_r($forms);
		$rules = array();
		foreach($forms as $form)
		{
			
			if($form['required'] =='0') {
				$form['required'] = '';
			}
			if($form['required'] != '' )
			{
			//	

				if( $form['type'] != 'file') {			
					$rules[$form['field']] = $form['required'];
				} 
				else {

					if($form['required'] =='required')
					{
						$validation = 'required';
						if($form['option']['upload_type'] =='image')
						{
							$validation .= '|mimes:jpg,jpeg,png,gif,bmp';

						} else {
							$validation .= '|mimes:zip,csv,xls,doc,docx,xlsx,pdf,rtf';						
						}

						if($form['option']['image_multiple'] != '1')
						{
							// IF SINGLE UPLOAD FILE OR IMAGE 	
							$rules[$form['field']] = $validation;

						}  else {
							// IF MULTIPLE UPLOAD FILE OR IMAGE 	
							$FilesArray = [];
							if(isset($_FILES[$form['field']])) {
								if(count($_FILES[$form['field']]) >=1 )
								{
									$nbr = count($_FILES[$form['field']]) - 1;
									foreach(range(0, $nbr) as $index) {
									   // $imagesArray['images.' . $index] = 'required|image';
									    $rules[$form['field'].'.'.$index] = $validation;
									}
								}
							}	
						}
						
					
					} else {
						
						if($form['option']['upload_type'] =='image')
						{
							$validation = 'mimes:jpg,jpeg,png,gif,bmp';
						} 
						else {
							$validation = 'mimes:zip,csv,xls,doc,docx,xlsx';						
						}

						if($form['option']['image_multiple'] != '1')
						{
							// IF SINGLE UPLOAD FILE OR IMAGE 	
							$rules[$form['field']] = $validation;

						}  else {
							// IF MULTIPLE UPLOAD FILE OR IMAGE 	
							$FilesArray = [];
							if(isset($_FILES[$form['field']])) {
								if(count($_FILES[$form['field']]) >=1 )
								{
									$nbr = count($_FILES[$form['field']]) - 1;
									foreach(range(0, $nbr) as $index) {
									   // $imagesArray['images.' . $index] = 'required|image';
									    $rules[$form['field'].'.'.$index] = $validation;
									}
								}
							}	

						}
					} 
				}
			}						
		}	
		
		return $rules ;


	}	

	function validateListError( $rules )
    {
        $errMsg = __('core.note_error') ;
        $errMsg .= '<hr /> <ul>';
        foreach($rules as $key=>$val)
        {
            $errMsg .= '<li>'.$key.' : '.$val[0].'</li>';
        }
        $errMsg .= '</li>'; 
        return $errMsg;
    }

	function validatePost(  $request )
	{	
		$str = $this->info['config']['forms'];
		$data = array();
		foreach($str as $f){
			$field = $f['field'];
			// Update for V5.1.5 issue on Autofilled createOn and updatedOn fields
			if($field == 'createdOn') $data['createdOn'] = date('Y-m-d H:i:s');
            if($field == 'updatedOn') $data['updatedOn'] = date('Y-m-d H:i:s');			
			if($f['view'] ==1) 
			{
				

				if($f['type'] =='textarea_editor' || $f['type'] =='textarea')
				{
					 $data[$field] = $request->input( $field );
				} else {

					if(!is_null( $request->input( $field ) )) $data[$field] = $request->input( $field ) ;

					// if post is file or image		


					if($f['type'] =='file')
					{	
						
						if(!is_dir(public_path().$f['option']['path_to_upload']))
							mkdir( public_path().$f['option']['path_to_upload'],0777 );   

						$files ='';	
						if($f['option']['upload_type'] =='file')
						{

							if(isset($f['option']['image_multiple']) && $f['option']['image_multiple'] ==1)
							{								
								if(isset($_POST['curr'.$field]))
								{
									$curr =  '';
									for($i=0; $i<count($_POST['curr'.$field]);$i++)
									{
										$files .= $_POST['curr'.$field][$i].',';
									}
								}	

								if(!is_null($request->file($field)))
								{

									$destinationPath = '.'. $f['option']['path_to_upload']; 	
									foreach($_FILES[$field]['tmp_name'] as $key => $tmp_name ){
									 	$file_name = $_FILES[$field]['name'][$key];
										$file_tmp =$_FILES[$field]['tmp_name'][$key];
										if($file_name !='')
										{
											move_uploaded_file($file_tmp,$destinationPath.'/'.$file_name);
											$files .= $file_name.',';

										}
										
									}
									
									if($files !='')	$files = substr($files,0,strlen($files)-1);	
									$data[$field] = $files;
								} else {
									unset($data[$field]);	
								}	
																					

							} else {
							
								if(!is_null($request->file($field)))
								{								

									// check if folder exists 
									
										$file = $request->file($field); 
									 	$destinationPath = '.'. $f['option']['path_to_upload']; 
										$filename = $file->getClientOriginalName();
										$extension =$file->getClientOriginalExtension(); //if you need extension of the file
										$rand = rand(1000,100000000);
										$newfilename = strtotime(date('Y-m-d H:i:s')).'-'.$rand.'.'.$extension;
										$uploadSuccess = $file->move($destinationPath, $newfilename);
										if( $uploadSuccess ) {
										   $data[$field] = $newfilename;
										}
								
								}	 
							}	

						} else {

							if(!is_dir(public_path().$f['option']['path_to_upload']))
								mkdir( public_path().$f['option']['path_to_upload'],0777 );   

							if(isset($f['option']['image_multiple']) && $f['option']['image_multiple'] ==1)
							{
								$files = '';
								if(isset($_POST['curr'.$field]))
								{
									$curr =  '';
									for($i=0; $i<count($_POST['curr'.$field]);$i++)
									{
										$files .= $_POST['curr'.$field][$i].',';
									}
								}

								$destinationPath = '.'. $f['option']['path_to_upload']; 
								if(count($request->file($f['field'])) >=1 )
								{
									
									$destinationPath = '.'. $f['option']['path_to_upload']; 
									foreach($_FILES[$field]['tmp_name'] as $key => $tmp_name ){
									 	$file_name = $_FILES[$field]['name'][$key];
										$file_tmp =$_FILES[$field]['tmp_name'][$key];
										if($file_name !='')
										{
											//move_uploaded_file($file_tmp,$destinationPath.'/'.$file_name);
											//echo  $file_name.'<br />';
											$file = $request->file($field)[$key];
											$filename = $file->getClientOriginalName();
											$extension =$file->getClientOriginalExtension(); //if you need extension of the file
											$rand = rand(1000,100000000);
											$newfilename = strtotime(date('Y-m-d H:i:s')).'-'.$rand.'.'.$extension;
											$files .= $newfilename.',';

											$uploadSuccess = $file->move($destinationPath, $newfilename);


											 if($f['option']['resize_width'] != '0' && $f['option']['resize_width'] !='')
											 {
											 	if( $f['option']['resize_height'] ==0 )
												{
													$f['option']['resize_height']	= $f['option']['resize_width'];
												}
											 	$orgFile = $destinationPath.'/'.$newfilename;
												 \SiteHelpers::cropImage($f['option']['resize_width'] , $f['option']['resize_height'] , $orgFile ,  $extension,	 $orgFile)	;						 
											 }
										}										
									}

																	
								} 
								if($files !='')	$files = substr($files,0,strlen($files)-1);	
									$data[$field] = $files;	

								

							} else {

								if(!is_null($request->file($field)))
								{
									$file = $request->file($field); 
								 	$destinationPath = '.'. $f['option']['path_to_upload'];  
									$filename = $file->getClientOriginalName();
									$extension =$file->getClientOriginalExtension(); //if you need extension of the file
									$rand = rand(1000,100000000);
									$newfilename = strtotime(date('Y-m-d H:i:s')).'-'.$rand.'.'.$extension;


									$uploadSuccess = $file->move($destinationPath, $newfilename);


									 if($f['option']['resize_width'] != '0' && $f['option']['resize_width'] !='')
									 {
									 	if( $f['option']['resize_height'] ==0 )
										{
											$f['option']['resize_height']	= $f['option']['resize_width'];
										}
									 	$orgFile = $destinationPath.'/'.$newfilename;
										 \SiteHelpers::cropImage($f['option']['resize_width'] , $f['option']['resize_height'] , $orgFile ,  $extension,	 $orgFile)	;						 
									 }
									 
									if( $uploadSuccess ) {
									   $data[$field] = $newfilename;
									}
								}	 
							}		

						}						
					}	

					// Handle Checkbox input 
					if($f['type'] =='checkbox')
					{
						if(!is_null( $request->{$field} ))
						{
							$data[$field] = implode(",", $request->input( $field ));
						} else {
							$data[$field] = '0';	
						}
					}
					// if post is date						
					if($f['type'] =='date')
					{
						$data[$field] = date("Y-m-d",strtotime($request->input($field)));
					}
					
					// if post is seelct multiple						
					if($f['type'] =='select')
					{
						//echo '<pre>'; print_r( $_POST[$field] ); echo '</pre>'; 
						if( isset($f['option']['select_multiple']) &&  $f['option']['select_multiple'] ==1 )
						{
							if(isset($_POST[$field]))
							{
								$multival = implode(",",$request->input( $field )); 
								$data[$field] = $multival;
							}
						} else {
							$data[$field] = $request->input( $field );
						}	
					}									
					
				}	 						

			}	
		}
		$this->access = $this->model->validAccess($this->info['id'] , session('gid'));
		$global	= (isset($this->access['is_global']) ? $this->access['is_global'] : 0 );
		
		if($global == 0 )
			$data['entry_by'] = \Session::get('uid');
		/* Added for Compatibility laravel 5.2 */
		$values = array();
		foreach($data as $key=>$val)
		{
			if($val !='') $values[$key] = $val;
		}			
		return $values;

	}


	function postFilter( Request $request)
	{
		$module = $this->module;
		$sort 	= (!is_null($request->input('sort')) ? $request->input('sort') : '');
		$order 	= (!is_null($request->input('order')) ? $request->input('order') : '');
		$rows 	= (!is_null($request->input('rows')) ? $request->input('rows') : '');
		$md 	= (!is_null($request->input('md')) ? $request->input('md') : '');
		
		$filter = '?';
		if($sort!='') $filter .= '&sort='.$sort; 
		if($order!='') $filter .= '&order='.$order; 
		if($rows!='') $filter .= '&rows='.$rows; 
		if($md !='') $filter .= '&md='.$md;
		 
		 

		return Redirect::to($this->data['pageModule'] . $filter);
	
	}	

	function injectPaginate()
	{

		$sort 	= (isset($_GET['sort']) 	? $_GET['sort'] : '');
		$order 	= (isset($_GET['order']) 	? $_GET['order'] : '');
		$rows 	= (isset($_GET['rows']) 	? $_GET['rows'] : '');
		$search = (isset($_GET['search']) ? $_GET['search'] : '');
		$s 		= (isset($_GET['s']) ? $_GET['s'] : '');

		$appends = array();
		if($sort!='') 	$appends['sort'] = $sort; 
		if($order!='') 	$appends['order'] = $order; 
		if($rows!='') 	$appends['rows'] = $rows; 
		if($search!='') $appends['search'] = $search; 
		if($s!='') $appends['s'] = $s; 
		
		return $appends;
			
	}	

	function returnUrl()
	{
		$pages 	= (isset($_GET['page']) ? $_GET['page'] : '');
		$sort 	= (isset($_GET['sort']) ? $_GET['sort'] : '');
		$order 	= (isset($_GET['order']) ? $_GET['order'] : '');
		$rows 	= (isset($_GET['rows']) ? $_GET['rows'] : '');
		$search 	= (isset($_GET['search']) ? $_GET['search'] : '');
		
		$appends = array();
		if($pages!='') 	$appends['page'] = $pages; 
		if($sort!='') 	$appends['sort'] = $sort; 
		if($order!='') 	$appends['order'] = $order; 
		if($rows!='') 	$appends['rows'] = $rows; 
		if($search!='') $appends['search'] = $search; 
		
		$url = "";
		foreach($appends as $key=>$val)
		{
			$url .= "&$key=$val";
		}
		return $url;
			
	}	

	public function getRemovecurrentfiles( Request $request)
	{
		$id 	= $request->input('id');
		$field 	= $request->input('field');
		$file 	= $request->input('file');
		if(file_exists('./'.$file) && $file !='')
		{
			if(unlink('.'.$file))
			{
				\DB::table($this->info['table'])->where($this->info['key'],$id)->update(array($field=>''));
			}
			return response()->json(array('status'=>'success'));
		} else {
			return response()->json(array('status'=>'error'));
		}
	}	

	public function getRemovefiles( $request)
	{
		$files = '.'.$request->input('file');
		if(file_exists($files) && $files !='')
		{
			unlink( $files);
		}
		return response()->json(array('status'=>'success'));
	}


	public function getSearch( $mode = 'native' )
	{
		if(isset($_GET['type']))
			$mode = $_GET['type'];

		$this->data['tableForm'] 	= $this->info['config']['forms'];	
		$this->data['tableGrid'] 	= $this->info['config']['grid'];
		$this->data['searchMode'] 	= $mode ;
		$this->data['pageUrl']		= url($this->module);
		return view('sximo.module.utility.search',$this->data);
	
	}

	function getImport( Request $request)
	{
		$task = $request->input('template');
		if($task !='')
		{
			$fields =  $this->info['config']['grid'];
			$output = fopen('php://output', 'w');
			$head= array();
			foreach($fields as $f )
			{
				$head[] = $f['field'];
			}

			fputcsv($output, $head);
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename='.$this->module.'.csv');	

		} else {
			$this->data['url'] = url($this->module) ;
			$this->data['module'] = $this->module ;
			return view('sximo.module.utility.import', $this->data);
		}	

	}

	function postImport( $request)
	{

		if(!is_null($request->file('fileimport')))
		{
			$file = 	$request->file('fileimport');
			$filename = $file->getClientOriginalName();
			$uploadSuccess = $file->move('./uploads/' , $filename );
			if( $uploadSuccess ) {
				$csv = array_map('str_getcsv', file('./uploads/'.$filename));
				$table = $this->info['config']['grid'];
				$fields = array();
				foreach($table as $f )
				{
					$fields[] = $f['field'];
				}
				//print_r($fields);
				foreach($csv as $row) {
					$data = array();
					foreach($fields as $key=>$val)
					{
						if($key != 0 )
							$data[$val] = (isset($row[$key]) ? $row[$key] : '' ) ;	
					}
					//print_r($data);
					//echo $row[0];
					$this->model->insertRow($data ,$row[0]);	
											
				}
				
				return response()->json(array('status'	=> 'success','message'=>'Csv Imported Successful !'));			  
			} else {
				return response()->json(array('status'	=> 'error','message'=>'Upload Failed!'));	
			}
		} else {			
			return response()->json(array('status'	=> 'error','message'=>'Please select file to Upload!'));
		}	

	}

	public function getExpotion()
	{
		$this->data['pageUrl'] = url( $this->data['pageModule']);
		return view('sximo.module.utility.export', $this->data);
	}
	public function getExport( Request $request, $t = 'excel')
	{
		if($request->has('filter'))
			return $this->getExpotion();

		$t = $request->input('do');
		$this->access = $this->model->validAccess($this->info['id'] , session('gid'));

		if(isset($this->access['is_'.$t]) ){
			if($this->access['is_'.$t] == 0 )
				return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');
		} else {
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');
		}

		$info 		= $this->model->makeInfo( $this->module);
		$filter = '';	
		if(!is_null($request->input('search')))
		{
			$search = 	$this->buildSearch('maps');
			$filter = $search['param'];
			$this->data['search_map'] = $search['maps'];
		} 

		$params 	= array(
					'params'	=> $filter ,
					'fstart'	=> $request->input('fstart'),
					'flimit'	=> $request->input('flimit')	
		);		

		$results 	= $this->model->getRows( $params );
		$fields		= $info['config']['grid'];
		$rows		= $results['rows'];
		$content 	= array(
						'fields' => $fields,
						'rows' => $rows,
						'title' => $this->data['pageTitle'],
					);
		
		if($t == 'word')
		{			
			 return view('sximo.module.utility.word',$content);
			 
		} else if($t == 'pdf') {	

			$pdf = PDF::loadView('sximo.module.utility.pdf', $content)->setPaper('a4', 'landscape');
			return $pdf->download('invoice.pdf');
			
		} else if($t == 'csv') {		
		 
			return view('sximo.module.utility.csv',$content);
			
		} else if ($t == 'print') {
		
			//return view('sximo.module.utility.print',$content);
			$data['html'] = view('sximo.module.utility.print', $content)->render();	
			return view('layouts.blank',$data);			 
			 				 
		} else  {			
		
			 return view('sximo.module.utility.excel',$content);
		}	
	}		

	function getLookup( $request)
	{
		$id = $request->input('param');
		$args = explode("-",$id);
		if(count($args)>=2) 
		{

			$model = '\\App\\Models\\'.ucwords($args['3']);
			$model = new $model();
			$info = $model->makeInfo( $args['3'] );
			$data['pageTitle'] = $info['title'];
			$data['pageNote'] = $info['note'];			
			$params = array(
				'params'	=> " And ".$args['4'].".".$args['5']." ='". $args['6'] ."'",
				//'global'	=> (isset($this->access['is_global']) ? $this->access['is_global'] : 0 )
			);
			$results = $model->getRows( $params );	
			$data['access']		=$model->validAccess($info['id']);
			$data['rowData']		= $results['rows'];
			$data['tableGrid'] 	= $info['config']['grid'];
			$data['tableForm'] 	= $info['config']['forms'];	
			$data['colspan']		= \SiteHelpers::viewColSpan($info['config']['grid']);
			$data['nested_subgrid']	= (isset($info['config']['subgrid']) ? $info['config']['subgrid'] : array());
			//print_r($data['nested_subgrid']);exit;
			$data['id'] 		= $args[6];
			$data['key']		= $info['key'];
			//$data['ids']		= 'md'-$info['id'];
			return view('sximo.module.utility.masterdetail',$data);

		} else {
			return 'Invalid Argument';
		}
	}


	function detailview( $model , $detail , $id )
	{
		
		$info = $model->makeInfo( $detail['module'] );
		$params = array(
			'params'	=> " And `".$detail['key']."` ='". $id ."'",
			'global'	=> (isset($this->access['is_global']) ? $this->access['is_global'] : 0 )
		);
		$results = $model->getRows( $params );	
		$data['rowData']		= $results['rows'];
		$data['tableGrid'] 		= $info['config']['grid'];
		$data['tableForm'] 		= $info['config']['forms'];	
		
		return $data;

		
			
	}


function detailviewsave( $model ,$request , $detail , $id )

	{
		//\DB::table($detail['table'])->where($detail['key'],$request[$detail['key']])->delete();

		$info = $model->makeInfo( $detail['module'] );
		$relation_key = $info['key'];
		$access = $model->validAccess($info['id'] , session('gid'));

		if($access['is_add'] == '1' && $access['is_edit'] == '1' )
		{
			$str = $info['config']['forms'];
			$global	= (isset($access['is_global']) ? $access['is_global'] : 0 );
			$total = count($request['counter']);
			$mkeys = array();
			$getAllCurrentData = \DB::table($detail['table'])->where($detail['master_key'] , $id  )->get();

			$pkeys = array();
			for($i=0; $i<$total;$i++) 
					$pkeys[] = $request['bulk_'.$relation_key][$i];

			foreach ($getAllCurrentData as $keys) {	
				if(!in_array($keys->{$relation_key} , $pkeys))		
				{	
					// Remove If items is not resubmited
					\DB::table($detail['table'])->where($relation_key,$keys->{$relation_key})->delete();
				}
			}

			for($i=0; $i<$total;$i++)
			{
				$data =array();
				foreach($str as $f){
					$field = $f['field'];
					if($f['view'] ==1)
					{
						if(isset($request['bulk_'.$field][$i]) && $request['bulk_'.$field][$i] !='')

						{
							$data[$f['field']] = $request['bulk_'.$field][$i];
						}
					}			
				}

				$rules = self::validateForm($str);
				$validator = Validator::make($data, $rules);
				if($validator->passes()) {	
					$data[$detail['key']] =  $id ;
					if($global == 0 )
						$data['entry_by'] = \Session::get('uid');
					
					// Check if data currentry exist
					$check = \DB::table($detail['table'])->where($relation_key , $request['bulk_'.$relation_key][$i]  )->get();
					if(count($check) >=1)
					{
						\DB::table($detail['table'])->where($relation_key ,  $request['bulk_'.$relation_key][$i] )->update($data);
					} else {
						unset($data[$relation_key]);
						\DB::table($detail['table'])->insert($data);	
					}
				}			

			}	
		}	

		

	}

	public function liveSearch( $request )
	{
		$query = '';
		$forms = $this->info['config']['forms'];
		$keyword = trim($request->input('s'));
		if(!is_null($request->input('search')))
			$keyword = trim($request->input('search')['value']);
		
		$i = 0;
		foreach($forms as $form)
		{
			if($form['search'] == '1'){
				if($i == 0){
					$query .= " AND ( ".$form['alias'].".".$form['field']." REGEXP '".$keyword."'  ";
				}
				else {
					$query .= " OR  ".$form['alias'].".".$form['field']." REGEXP '".$keyword."'  ";	
				}
				$i++;
			}

		}
		if($query !='' ) {
			$query .= ' ) ';
		}	
		return $query ;
	}

	public function grab( $request , $args = array())
	{
		
		if(  isset( $request->get('profile')->group_id)) {
			$this->access = $this->access($this->info['id'] , $request->get('profile')->group_id );
		} else {
			$this->access = $this->access($this->info['id']  );
		}
		

		$sort = (!is_null($request->input('sort')) ? $request->input('sort') : $this->info['setting']['orderby']); 
		$order = (!is_null($request->input('order')) ? $request->input('order') :  $this->info['setting']['ordertype']);
		// End Filter sort and order for query 
		// Filter Search for query		
		$filter ='';	
		$filter .= (isset($args['params']) ? $args['params'] : '');
		if(!is_null($request->input('search')))
		{
			$search = 	$this->buildSearch('maps');
			$filter = $search['param'];
			$this->data['search_map'] = $search['maps'];
		} 
		if(!is_null($request->input('s')))
		{
			$filter .= $this->liveSearch( $request);
		}

		
		$page = $request->input('page', 1);
		$params = array(
			'page'		=> $page ,
			'limit'		=> (!is_null($request->input('rows')) ? filter_var($request->input('rows'),FILTER_VALIDATE_INT) : static::$per_page ) ,
			'sort'		=> $sort ,
			'order'		=> $order,
			'params'	=> $filter,
			'global'	=> (isset($this->access['is_global']) ? $this->access['is_global'] : 0 )
		);
		// Get Query 
		$results = $this->model->getRows( $params , session('uid') );				
		
		// Build pagination setting
		$pagination = new Paginator($results['rows'], $results['total'], $params['limit']);	
		if($this->info['type'] =='ajax'){
			$pagination->setPath( $this->module.'/data' );
		} else {
			$pagination->setPath( $this->module );	
		}
		$this->data['param']		= $params;
		$this->data['rowData']		= $results['rows'];
		// Build Pagination 
		$this->data['pagination']	= $pagination;
		// Build pager number and append current param GET
		$this->data['pager'] 		= $this->injectPaginate();	
		// Row grid Number 
		$this->data['i']			= ($page * $params['limit'])- $params['limit']; 
		// Grid Configuration 
		usort($this->info['config']['grid'], "SiteHelpers::_sort");
		$this->data['tableGrid'] 	= $this->info['config']['grid'];
		$this->data['tableForm'] 	= $this->info['config']['forms'];
		$this->data['colspan'] 		= \SiteHelpers::viewColSpan($this->info['config']['grid']);		
		// Group users permission
		$this->data['access']		= $this->access;
		// Detail from master if any
		$this->data['fields'] =  \SiteHelpers::fieldLang($this->info['config']['grid']);
		// Master detail link if any 
		$this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
		$this->data['setting'] 		= $this->info['setting']; 

		$this->data['insort']	= $sort ;
		$this->data['inorder']	= $order ;
				
		return  $this->data;
	}
	public function grabApi( $request , $args = array())
	{
		
		if(  isset( $request->get('profile')->group_id)) {
			$this->access = $this->access($this->info['id'] , $request->get('profile')->group_id );
		} else {
			$this->access = $this->access($this->info['id']  );
		}
		

		$sort = (!is_null($request->input('sort')) ? $request->input('sort') : $this->info['setting']['orderby']); 
		$order = (!is_null($request->input('order')) ? $request->input('order') :  $this->info['setting']['ordertype']);
		// End Filter sort and order for query 
		// Filter Search for query		
		$filter ='';	
		$filter .= (isset($args['params']) ? $args['params'] : '');
		if(!is_null($request->input('search')))
		{
			$search = 	$this->buildSearch('maps');
			$filter = $search['param'];
			$this->data['search_map'] = $search['maps'];
		} 
		if(!is_null($request->input('s')))
		{
			$filter .= $this->liveSearch( $request);
		}

		
		$page = $request->input('page', 1);
		$params = array(
			'page'		=> $page ,
			'limit'		=> (!is_null($request->input('rows')) ? filter_var($request->input('rows'),FILTER_VALIDATE_INT) : static::$per_page ) ,
			'sort'		=> $sort ,
			'order'		=> $order,
			'params'	=> $filter,
			'global'	=> (isset($this->access['is_global']) ? $this->access['is_global'] : 0 )
		);
		// Get Query 
		$results = $this->model->getRows( $params , session('uid') );				
		// Build pagination setting
		$pagination = new Paginator($results['rows'], $results['total'], $params['limit']);	
		$this->data['pagination']	= $pagination;
		
		$this->data['rowData']		= $results['rows'];
				
		return  $this->data;
	}

	public function hook( $request , $id = null  ) 
	{
		$this->access = $this->access($this->info['id']);
		$row = $this->model->getRow($id);
		if($row)
		{
			$this->data['row'] =  $row;
			$this->data['fields'] 		=  \SiteHelpers::fieldLang($this->info['config']['grid']);
			$this->data['id'] = $id;
			$this->data['access']		= $this->access;
			$this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array()); 
			$this->data['fields'] =  \SiteHelpers::fieldLang($this->info['config']['grid']);
			$this->data['prevnext'] = $this->model->prevNext($id);
			$this->data['setting'] 		= $this->info['setting']; 
			
		}  
		return $this->data;
	}

	function copy( $request)
	{
	    foreach(\DB::select("SHOW COLUMNS FROM ".  $this->info['table'] ) as $column)
        {
			if( $column->Field != $this->info['key'])
				$columns[] = $column->Field;
        }
		
		if(count($request->input('ids')) >=1)
		{
			$toCopy = implode(",",$request->input('ids'));
			$sql = "INSERT INTO ".  $this->info['table'] ." (".implode(",", $columns).") ";
			$sql .= " SELECT ".implode(",", $columns)." FROM ". $this->info['table']." WHERE ".$this->info['key']." IN (".$toCopy.")";
			\DB::select($sql);
			return ['message'=>__('core.note_success'),'status'=>'success'];
		} else {
			return ['message'=>__('Please select row to copy'),'status'=>'error'];
		}	
		
	}

	function access( $id , $gid = null )
	{
		if($gid == null) 
			$gid = session('gid');

		$row = \DB::table('tb_groups_access')->where('module_id', $id)->where('group_id', $gid )->get();
		
		if(count($row) >= 1)
		{
			$row = $row[0];
			if($row->access_data !='')
			{
				$data = json_decode($row->access_data,true);
			} else {
				$data = array();
			}	

			
			return $data;		
			
		} else {
			return false;
		}			
	
	}		

}

