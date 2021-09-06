<?php namespace App\Http\Controllers\core;


use App\Http\Controllers\Controller;
use App\Models\Sximo;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ; 


class CategoriesController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();	
	public $module = 'categories';
	static $per_page	= '10';

	public function __construct()
	{		
		parent::__construct();
		$this->data = array(
			'pageTitle'	=> 	'Category',
			'pageNote'	=>  'Article Post Categories',
			'pageModule'=> 'core/categories',
			'return'	=> self::returnUrl()
			
		);
		
		
	}

	public function index( Request $request )
	{
		// Make Sure users Logged 
		if(!\Auth::check()) 
			return redirect('user/login')->with('status', 'error')->with('message','You are not login');
		
		$this->data['rows'] = Post::categories();
		return view( 'core.categories.index',$this->data);

	}

	function create( Request $request ) 
	{
		//if($this->access['is_add'] ==0) 
		//	return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');

		$this->data['row'] = Sximo::getColumnTable( 'tb_categories'); 
		$this->data['id'] = '';
		return view( 'core.'. $this->module.'.form',$this->data);

	}
	function edit( Request $request , $id ) 
	{
		
		$row = \DB::table('tb_categories')->where('cid', $id)->get();
		$this->data['row'] = (array) $row[0];
		$this->data['id'] = $id;
		return view( 'core.'. $this->module.'.form',$this->data);
	}	
	function show( Request $request , $id ) 
	{
		/* Handle import , export and view */
		$task =$id ;
		switch( $task)
		{
			case 'search':
				return $this->getSearch();
				break;

			case 'comboselect':
				return $this->getComboselect( $request );
				break;
			case 'import':
				return $this->getImport( $request );
				break;
			case 'export':
				return $this->getExport( $request );
				break;
			default:
				$this->hook( $request , $id );
				if(!isset($this->data['row']))
					return redirect($this->module)->with('message','Record Not Found !')->with('status','error');

				if($this->access['is_detail'] ==0) 
					return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');
				return view( 'core.'. $this->module.'.view',$this->data);
				break;		
		}
	}	

	function store( Request $request  )
	{
		$task = $request->input('action_task');
		switch ($task)
		{

			default:	
				$id = $request->input('cid');
				$rules = [
					'name' 		=> 'required' ,
					'desc' 		=> 'required' ,
					'active' 	=> 'required'
				];
				$validator = Validator::make($request->all(), $rules);
				if ($validator->passes()) 
				{			

					$data = [
						'name'	=> $request->input('name'),
						'desc'	=> $request->input('desc'),
						'active'	=> $request->input('active')

					];
					if(!is_null($request->file('image')))
					{
						$updates = array();
						$file = $request->file('image'); 
						$destinationPath = './uploads/images/posts/';
						$filename = $file->getClientOriginalName();
						$extension = $file->getClientOriginalExtension(); //if you need extension of the file
						 $newfilename = $id.'.'.$extension;
						$uploadSuccess = $request->file('image')->move($destinationPath, $newfilename);				 
						if( $uploadSuccess ) {
						    $data['image'] = $newfilename; 
						} 						
					}					
					$res = Sximo::updateDataRow( 'tb_categories' ,'cid' , $data , $id  );
					return redirect( 'cms/'.$this->module )->with('message',__('core.note_success'))->with('status','success');
				} 
				else {
					
					return redirect('cms/'.$this->module )
							->with('message',__('core.note_error'))->with('status','error')
							->withErrors($validator)->withInput();

				}
				break;
			
			case 'delete' :
				$result = $this->destroy( $request );
				return redirect('cms/'.$this->module.'?'.$this->returnUrl())->with($result);
				break;
		}		

	}

	public function destroy( $request)
	{
		// Make Sure users Logged 
		if(!\Auth::check()) 
			return redirect('user/login')->with('status', 'error')->with('message','You are not login');
		

		if(count($request->input('ids')) >=1)
		{
			
			$res = Sximo::deleteDataRow( 'tb_categories' ,'cid' , $request->input('ids') );
			
			\SiteHelpers::auditTrail( $request , "ID : ".implode(",",$request->input('ids'))."  , Has Been Removed Successfull");
			// redirect
        	return ['message'=>__('core.note_success_delete'),'status'=>'success'];	
	
		} else {
			return ['message'=>__('No Item Deleted'),'status'=>'error'];				
		}

	}	
	public function getSearch( $mode = 'native')
	{

		$this->data['tableForm'] 	= $this->info['config']['forms'];	
		$this->data['tableGrid'] 	= $this->info['config']['grid'];
		$this->data['searchMode'] = 'native';
		$this->data['pageUrl']		= url('core/groups');
		return view('sximo.module.utility.search',$this->data);
	
	}	

}