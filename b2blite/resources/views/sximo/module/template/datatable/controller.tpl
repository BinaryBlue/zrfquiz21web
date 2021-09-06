<?php namespace App\Http\Controllers;

use App\Models\{controller};
use App\Library\DataHelpers;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ; 

class {controller}Controller extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();	
	public $module = '{class}';
	static $per_page	= '10';
	
	public function __construct() 
	{
		parent::__construct();
		$this->model = new {controller}();
		{masterdetailmodel}
		$this->info = $this->model->makeInfo( $this->module);
		$this->access = $this->model->validAccess($this->info['id']);
	
		$this->data = array(
			'pageTitle'			=> 	$this->info['title'],
			'pageNote'			=>  $this->info['note'],
			'pageModule'		=> '{class}',
			'pageUrl'			=>  url('{class}'),
			'return' 			=> 	self::returnUrl()	
		);	
		{masterdetailinfo}	
				
	} 
	public function index()
	{
		if(!\Auth::check()) 
			return redirect('user/login')->with('status', 'error')->with('message','You are not login');

		$this->access = $this->model->validAccess($this->info['id'] , session('gid'));
		if($this->access['is_view'] ==0) 
			return redirect('dashboard')->with('message',__('core.note_restric'))->with('status','error');
		
		$this->data['setting'] 		= $this->info['setting'];
		$this->data['tableForm'] 	= $this->info['config']['forms'];
		$this->data['tableGrid'] 	= $this->info['config']['grid'];
		usort($this->data['tableGrid'], "SiteHelpers::_sort");
			$cols = '{"data":"rowId"},';
			if($this->data['setting']['view-method'] =='expand'){
				$cols .= '{"className": "details-control","orderable": false,"data": null,"defaultContent": ""},';
			}		
				
			foreach($this->data['tableGrid'] as $field)
			{				
				
				if($field['view'] =='1')
				{
					$cols .= '{"data":"'.$field['field'].'"},';
				}						
			}
			$cols .= '{"data":"action"},';
		$this->data['column'] = $cols ;	
		$this->data['access']		= $this->access;	
		$this->data['setting'] 		= $this->info['setting'];
		return view('{class}.index',$this->data);
	}
	function create( Request $request ) 
	{
		$this->hook( $request  );
		if($this->access['is_add'] ==0) 
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');

		$this->data['row'] = $this->model->getColumnTable( $this->info['table']); 
		{masterdetailsubform}
		$this->data['setting'] 		= $this->info['setting'];
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
		{masterdetailsubform}
		$this->data['setting'] 		= $this->info['setting'];
		$this->data['id'] = $id;
		return view($this->module.'.form',$this->data);
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
			case 'removefiles':
				return $this->getRemovefiles( $request );	
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

				return view($this->module.'.view',$this->data);	
				break;		
		}
	}	
	public function store( Request $request){

		$task = $request->input('action_task');
		switch ($task)
		{
			default:
				return $this->postData( $request);
				break;
			case 'save':
				$rules = $this->validateForm();
				$validator = Validator::make($request->all(), $rules);
				if ($validator->passes()) 
				{
					$data = $this->validatePost( $request );
					$id = $this->model->insertRow($data , $request->input( $this->info['key']));
					{masterdetailsave}
					/* Insert logs */
					$this->model->logs($request , $id);
					return response()->json(array('status'=>'success','message'=> __('core.note_success')));	
					
				} 
				else {
					$message = $this->validateListError(  $validator->getMessageBag()->toArray() );
					return response()->json(array(
						'message'	=> $message,
						'status'	=> 'error'
					));	
				}
				break;
			case 'delete':
				return $result = $this->destroy( $request );
				break;

			case 'import':
				return $this->PostImport( $request );
				break;

			case 'copy':
				$result = $this->copy( $request );
				return response()->json($result);
				break;		

		}
	}
	public function postData( $request)
	{ 
		
		$this->access = $this->model->validAccess($this->info['id'] , session('gid'));

		$tables 	= $this->info['config']['grid'];
		$cols = array();
		foreach($tables as $field)
		{				
			//$cols[$this->info['key']]	= array('db'=> $field['alias'] ,$this->info['key'] );
			if($field['view'] =='1')
			{
				$cols[] = array('db'=> $field['alias'],'dt'=> $field['field'] );
			}					
		}
		$conf = DataHelpers::simple($request->all(),$cols);
		$filter =  '';
		if(!is_null($request->input('search')))
		{
			$filter .= $this->liveSearch( $request);
		}

		$params = array(
			'page'		=> $conf['page'] ,
			'limit'		=> $conf['limit'] ,
			'sort'		=> ($conf['order']['sort'] !='' ? $conf['order']['sort'] : $this->info['setting']['orderby']) ,
			'order'		=> ($conf['order']['by'] !='' ? $conf['order']['by'] : $this->info['setting']['ordertype']),
			'params'	=> $filter,
			'global'	=> (isset($this->access['is_global']) ? $this->access['is_global'] : 0 )
		);


		$results = $this->model->getRows($params);
		
		$array = array();
		foreach($results['rows'] as $row)
		{
			$values = array();
			$values['rowId'] = $row->{$this->info['key']};
			foreach($tables as $field)
			{			
				
				if($field['view'] =='1')
				{
					$values[$field['field']] = \SiteHelpers::formatRows($row->{$field['field']}, $field , $row);
				}	
			}
			$values['action'] =  \SiteHelpers::buttonAction($this->module,$this->access ,$row->{$this->info['key']} ,$this->info['setting']) ;
			$array[] = $values; 	
		}
		
		$data = array(
			'draw'			=> (!is_null($request->input('draw')) ? intval($request->input('draw') ) : 0), 
			'recordsTotal'	=> $results['total'] ,
			'recordsFiltered'	=> $results['total'],
			'data'				=> $array

		);
		return response()->json($data);
	}

	public function destroy( Request $request)
	{

		if(!\Auth::check()) 
			return response()->json(array(
				'status'=>'error',
				'message'=> 'You are not login'
			));

		$this->access = $this->model->validAccess($this->info['id'] , session('gid'));
		if($this->access['is_remove'] ==0) 
			return response()->json(array('status'=>'error','message'=> __('core.note_restric')));	


		// delete multipe rows 
		if(count($request->input('ids')) >=1)
		{
			$this->model->destroy($request->input('ids'));
			{masterdetaildelete}
			return response()->json(array(
				'status'=>'success',
				'message'=> __('core.note_success_delete')
			));
		} else {
			return response()->json(array(
				'status'=>'error',
				'message'=> __('core.note_error')
			));

		} 		

	}
}