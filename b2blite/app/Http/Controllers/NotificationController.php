<?php namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ; 


class NotificationController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();	
	public $module = 'notification';
	static $per_page	= '10';

	public function __construct()
	{		
		parent::__construct();
		$this->model = new Notification();	
		$this->info = $this->model->makeInfo( $this->module);	
		$this->data = array(
			'pageTitle'	=> 	$this->info['title'],
			'pageNote'	=>  $this->info['note'],
			'pageModule'=> 'notification',
			'return'	=> self::returnUrl()
			
		);
		
	}

	public function index( Request $request )
	{
		// Make Sure users Logged 
		if(!\Auth::check()) 
			return redirect('user/login')->with('status', 'error')->with('message','You are not login');
		$this->grab( $request) ;
		if($this->access['is_view'] ==0) 
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');				
		// Render into template
		\DB::table('tb_notification')->where('userid',session('uid'))->update(['is_read'=>'1']);	
		return view( 'user.'.$this->module.'.index',$this->data);
	}	

	function create( Request $request ) 
	{
		$this->hook( $request  );
		if($this->access['is_add'] ==0) 
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');

		$this->data['row'] = $this->model->getColumnTable( $this->info['table']); 
		$this->data['id'] = '';
		return view($this->module.'.form',$this->data);
	}
	function edit( Request $request , $id ) 
	{
		$this->hook( $request , $id );
		if(!isset($this->data['row']))
			return redirect($this->module)->with('message','Record Not Found !')->with('status','error');
		if($this->access['is_edit'] ==0 )
			return redirect('dashboard')->with('message',__('core.note_restric'))->with('status','error');
		$this->data['row'] = (array) $this->data['row'];
		$this->data['id'] = $id;
		return view('user.'.$this->module.'.form',$this->data);
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

				return view('user.'.$this->module.'.view',$this->data);	
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

					/* Insert logs */
					$this->model->logs($request , $id);
					if(!is_null($request->input('apply')))
						return redirect( $this->module .'/'.$id.'/edit?'. $this->returnUrl() )->with('message',__('core.note_success'))->with('status','success');

					return redirect( $this->module .'?'. $this->returnUrl() )->with('message',__('core.note_success'))->with('status','success');
				} 
				else {
					return redirect($this->module.'/'. $request->input(  $this->info['key'] ))
							->with('message',__('core.note_error'))->with('status','error')
							->withErrors($validator)->withInput();

				}
				break;
			case 'delete':
				$result = $this->destroy( $request );
				return redirect($this->module.'?'.$this->returnUrl())->with($result);
				break;

			case 'import':
				return $this->PostImport( $request );
				break;

			case 'copy':
				$result = $this->copy( $request );
				return redirect($this->module.'?'.$this->returnUrl())->with($result);
				break;		
		}	
	
	}	

	public function destroy( $request)
	{
		// Make Sure users Logged 
		if(!\Auth::check()) 
			return redirect('user/login')->with('status', 'error')->with('message','You are not login');

		$this->access = $this->model->validAccess($this->info['id'] , session('gid'));
		if($this->access['is_remove'] ==0) 
			return redirect('dashboard')
				->with('message', __('core.note_restric'))->with('status','error');
		// delete multipe rows 
		if(is_array($request->input('ids')) )
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
