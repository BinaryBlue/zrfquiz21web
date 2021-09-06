<?php namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\{controller};
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ; 


class {controller}Controller extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();	
	public $module = '{class}';
	static $per_page	= '50';

	public function __construct()
	{		
		parent::__construct();
		$this->model = new {controller}();	
		{masterdetailmodel}
		$this->info = $this->model->makeInfo( $this->module);	
		$this->data = array(
			'pageTitle'	=> 	$this->info['title'],
			'pageNote'	=>  $this->info['note'],
			'pageModule'=> '{class}',
			'return'	=> self::returnUrl()
			
		);
		{masterdetailinfo}
	}

	public function index( Request $request )
	{
		// Make Sure users Logged 			
		$this->grabApi( $request , 'api') ;		
		if($this->access['is_view'] ==0) 
			 return response()->json(array('status'=>'error' , 'data' =>  __('core.note_restric') ));
		// Render into json 
		 return response()->json(array('status'=>'success' , 'data' => (array) $this->data ));
	}	

	function create( Request $request , $id =0 ) 
	{
		$this->hook( $request  );
		if($this->access['is_add'] ==0) 
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');

		$this->data['row'] = $this->model->getColumnTable( $this->info['table']); 
		{masterdetailsubform}
		$this->data['id'] = '';
		return response()->json(array('status'=>'success' , 'data' => $this->data ));
	}
	function edit( Request $request , $id ) 
	{
		$this->hook( $request , $id );
		if(!isset($this->data['row']))
			return response()->json(array('status'=>'error' , 'message' => 'Record Not Found !' ));

		if($this->access['is_edit'] ==0 )
			return response()->json(array('status'=>'error' , 'message' => __('core.note_restric') ));

		$this->data['row'] = (array) $this->data['row'];
		{masterdetailsubform}
		$this->data['id'] = $id;
		return response()->json(array('status'=>'success' , 'data' => $this->data ));
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
			case 'lookup':
				return $this->getLookup($request );
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
					return response()->json(array('status'=>'error' , 'message' => 'Record Not Found !' ));

				if($this->access['is_detail'] ==0) 
					return response()->json(array('status'=>'error' , 'message' => __('core.note_restric') ));

				return response()->json(array('status'=>'success' , 'data' => $this->data ));
				break;		
		}
	}
	function store( Request $request  )
	{
		$task = $request->input('action_task');
		switch ($task)
		{
			default:
				$rules = $this->validateForm();
				$validator = Validator::make($request->all(), $rules);
				if ($validator->passes()) 
				{
					$data = $this->validatePost( $request );
					$id = $this->model->insertRow($data , $request->input( $this->info['key']));
					{masterdetailsave}
					/* Insert logs */
					$this->model->logs($request , $id);
					return response()->json( ['status'=> 'success','message' => __('core.note_success') ] );
				} 
				else {
					return response()->json( ['status'=> 'error','message' => __('core.note_error') ] );

				}
				break;

			case 'delete':
				$result = $this->destroy( $request );
				return response()->json( $result  );
				break;

			case 'import':
				return $this->PostImport( $request );
				break;

			case 'copy':
				$result = $this->copy( $request );
				return response()->json(array('status'=>'success'  ));
				break;		
		}	
	
	}	

	public function destroy( $request)
	{
		// Make Sure users Logged 
		if(!\Auth::check()) 
			return ['message'=>__('core.note_restric'),'status'=>'error'];
			

		$this->access = $this->model->validAccess($this->info['id'] , session('gid'));
		if($this->access['is_remove'] ==0)
			return ['message'=>__('core.note_restric'),'status'=>'error']; 
			
		// delete multipe rows 
		if(count($request->input('ids')) >=1)
		{
			$this->model->destroy($request->input('ids'));
			
			\SiteHelpers::auditTrail( $request , "ID : ".implode(",",$request->input('ids'))."  , Has Been Removed Successfull");
			// redirect
        	return ['message'=>__('core.note_success_delete'),'status'=>'success'];	
	
		} else {
			return ['message'=>__('No Item Deleted'),'status'=>'error'];				
		}

	}		
}
