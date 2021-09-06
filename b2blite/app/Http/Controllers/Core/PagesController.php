<?php namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Pages;
use App\Models\Core\Groups;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ; 


class PagesController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();	
	public $module = 'pages';
	static $per_page	= '50';

	public function __construct()
	{
		
		parent::__construct();
		$this->model = new Pages();
		$this->info = $this->model->makeInfo( $this->module);
		$this->access = $this->model->validAccess($this->info['id']);
	
		$this->data = array(
			'pageTitle'	=> 	$this->info['title'],
			'pageNote'	=>  $this->info['note'],
			'pageModule'=> 'cms/pages',
			'return'	=> self::returnUrl()
			
		);
	}

	public function index( Request $request )
	{
		// Make Sure users Logged 
		if(!\Auth::check()) 
			return redirect('user/login')->with('status', 'error')->with('message','You are not login');

		$param = [
			'params' => " AND pagetype != 'post' OR pagetype IS NULL "
		];
		$this->grab( $request , $param ) ;
		if($this->access['is_view'] ==0) 
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');				

		return view( 'core.'. $this->module.'.index',$this->data);
	}

	function create( Request $request ) 
	{
		$this->hook( $request  );

		if($this->access['is_add'] ==0) 
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');

		$path = base_path().'/resources/views/layouts/'.$this->config['cnf_theme'].'/info.json';
		$this->data['pagetemplate'] = json_decode(file_get_contents($path),true);
		$groups = Groups::all();
		$group = array();
		foreach($groups as $g) {
			$group_id = $g['group_id'];			
			$a = (isset($access[$group_id]) && $access[$group_id] ==1 ? 1 : 0);		
			$group[] = array('id'=>$g->group_id ,'name'=>$g->name,'access'=> $a); 			
		}		

		$this->data['groups'] = $group;		
		$this->data['row'] = $this->model->getColumnTable( $this->info['table']); 
		$this->data['id'] = '';
		return view( 'core.'. $this->module.'.form',$this->data);

	}
	function edit( Request $request , $id ) 
	{
		$this->hook( $request , $id );
		if(!isset($this->data['row']))
			return redirect($this->module)->with('message','Record Not Found !')->with('status','error');

		if($this->access['is_edit'] ==0 )
			return redirect('dashboard')->with('message',__('core.note_restric'))->with('status','error');

		$path = base_path().'/resources/views/layouts/'.$this->config['cnf_theme'].'/info.json';
		$this->data['pagetemplate'] = json_decode(file_get_contents($path),true);
		$groups = Groups::all();
		$group = array();
		foreach($groups as $g) {
			$group_id = $g['group_id'];			
			$a = (isset($access[$group_id]) && $access[$group_id] ==1 ? 1 : 0);		
			$group[] = array('id'=>$g->group_id ,'name'=>$g->name,'access'=> $a); 			
		}		

		$this->data['groups'] = $group;		
		$this->data['row'] = (array) $this->data['row'];
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
				$rules = array(
					'title'=>'required',
					'alias'=>'required|alpha_dash',
					'filename'=>'required|alpha',
					'status'=>'required'						
				);

				$validator = Validator::make($request->all(), $rules);
				if ($validator->passes()) 
				{
					$data = $this->validatePost( $request );
					 $groups = Groups::all();
					 $access = array();				
					 foreach($groups as $group) {		 	
						$access[$group->group_id]	= (isset($_POST['group_id'][$group->group_id]) ? '1' : '0');
					 }
				 						
					$data['access'] = json_encode($access);
					$data['note'] = $request->note;

					$data['allow_guest'] = $request->input('allow_guest');
					$data['template'] = $request->input('template');
					if($request->input('alias') ==''){
						$data['alias'] = \SiteHelpers::seourl($data['title']);
					} else {
						$data['alias'] = \SiteHelpers::seourl($data['alias']);
					}
					
					
					if(isset($data['default']) && $data['default'] ==1)
					{
						// Update default homepage if checked
						\DB::table('tb_pages')->update(array('default'=>'0'));				
					}
					$id = $this->model->insertRow($data , $request->input( $this->info['key']));

				
					self::createRouters();
					/* Insert logs */
					$this->model->logs($request , $id);
					//if(!is_null($request->input('apply')))
					return redirect( 'cms/'. $this->module .'/'.$id.'/edit?'. $this->returnUrl() )->with('message',__('core.note_success'))->with('status','success');
				} 
				else {
					return redirect('cms/'.$this->module.'/'. $request->input(  $this->info['key']).'/edit' )
							->with('message',__('core.note_error'))->with('status','error')
							->withErrors($validator)->withInput();

				}
				break;
			case 'delete':
				$result = $this->destroy( $request );
				return redirect('cms/'.$this->module.'?'.$this->returnUrl())->with($result);
				break;

			case 'import':
				return $this->PostImport( $request );
				break;

			case 'copy':
				$result = $this->copy( $request );
				return redirect('cms/'.$this->module.'?'.$this->returnUrl())->with($result);
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
		if(!is_null($request->input('ids')) )
		{
			$this->model->destroy($request->input('ids'));			
			\SiteHelpers::auditTrail( $request , "ID : ".implode(",",$request->input('ids'))."  , Has Been Removed Successfull");
			// redirect
        	return ['message'=>__('core.note_success_delete'),'status'=>'success'];	
	
		} else {
			return ['message'=>__('No Item Deleted'),'status'=>'error'];				
		}

	}	
	function createRouters()
	{
		$rows = \DB::table('tb_pages')->where('pageID','!=','1')->get();
		$val  =	"<?php \n"; 
		foreach($rows as $row)
		{
			
			$slug = $row->alias;
			$val .= "Route::get('{$slug}', 'HomeController@index');\n";		
		}
		$val .= 	"?>";
		$filename = base_path().'/routes/pages.php';
		$fp=fopen($filename,"w+"); 
		fwrite($fp,$val); 
		fclose($fp);	
		return true;	
		
	}				
	public function getSearch( $mode = 'native')
	{

		$this->data['tableForm'] 	= $this->info['config']['forms'];	
		$this->data['tableGrid'] 	= $this->info['config']['grid'];
		$this->data['searchMode'] = 'native';
		$this->data['pageUrl']		= url('cms/pages');
		return view('sximo.module.utility.search',$this->data);
	
	}

}