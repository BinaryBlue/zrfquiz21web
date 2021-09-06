<?php namespace App\Http\Controllers\Sximo;
use App\Http\Controllers\Controller;
use App\Models\Sximo;
use App\Library\AppHelper;
use App\Library\FormHelper;
use Illuminate\Http\Request;
use Validator, Input, Redirect; 

class FormController extends Controller {

	public function __construct()
	{       
        parent::__construct();
        $this->middleware(function ($request, $next) {           
            if(session('gid') !='1')
                return redirect('dashboard')
                ->with('message','You Dont Have Access to Page !')->with('status','error');            
            return $next($request);
        });

        $driver             = config('database.default');
        $database           = config('database.connections');
       
        $this->db           = $database[$driver]['database'];
        $this->dbuser       = $database[$driver]['username'];
        $this->dbpass       = $database[$driver]['password'];
        $this->dbhost       = $database[$driver]['host']; 
       
        $this->data = array_merge(array(
            'title' =>  'Form Builder',
            'note'  =>  'Manage Form Builder ',
            
        ),$this->data)  ;         
	}
   	public function index( Request $request )
	{
        $this->data['forms']    = \DB::table('tb_forms')->get();
        return view('sximo.form.index',$this->data);
	}

    public function show( Request $request , $task )
    {
        switch($task)
        {
            default:
                return view('sximo.form.form',$this->data);
                break;
            case 'field':
                $this->data['field_option'] = array(
                    'text'                  => ['name'=>'Text','placeholder'=>''] ,
                    'date'                  => ['name'=>'Date','placeholder'=>''] ,
                    'time'                  => ['name'=>'Time','placeholder'=>''] ,
                    'year'                  => ['name'=>'Year','placeholder'=>''] ,
                    'datetime'              => ['name'=>'Date & Time','placeholder'=>''] ,
                    'textarea'              => ['name'=>'Textarea','placeholder'=>''] ,
                    'editor'                => ['name'=>'Editor ','placeholder'=>''] ,
                    'select'                => ['name'=>'Select','placeholder'=>'table:key:field1-field2'] ,
                    'radio'                 => ['name'=>'Radio' ,'placeholder'=>'value2,display, etc ...'] ,
                    'checkbox'              => ['name'=>'Checkbox','placeholder'=>'value2,display, etc ...'] ,
                    'image'                 => ['name'=>'Upload Image','placeholder'=>' /uploads/path_to_upload/'],
                    'file'                  => ['name'=>'Upload File','placeholder'=>' /uploads/path_to_upload/']
                );
                return view('sximo.form.field',$this->data);
                break;

            case 'remove':
                $id = $request->input('id');
                \DB::table('tb_forms')->where('formID',$id)->delete();
                return redirect('sximo/form')->with(['status'=>'success','message'=>'Form has been deleted !']);
                break;    

            case 'update':
                $id = $request->input('id');

                if($id =='')
                {
                    $rows = Sximo::getTableField('tb_forms');
                    $row = array();
                    foreach($rows as $key=>$value)                  
                        $row[$key] ='';
                   
                    $this->data['row'] = (object) $row;
                    $this->data['configuration'   ] = [];
                   // echo '<pre>'; print_r( $this->data); echo '</pre>'; exit;

                }
                else {
                    $row    = \DB::table('tb_forms')->where('formID',$id)->get();
                    $row    = $row[0];
                    $this->data['row'] = $row;
                    $this->data['configuration'   ]    =  json_decode($row->configuration,true); 

                }
                $this->data['field_option'] = array(
                    'text'                  => ['name'=>'Text','placeholder'=>''] ,
                    'date'                  => ['name'=>'Date','placeholder'=>''] ,
                    'time'                  => ['name'=>'Time','placeholder'=>''] ,
                    'year'                  => ['name'=>'Year','placeholder'=>''] ,
                    'datetime'              => ['name'=>'Date & Time','placeholder'=>''] ,
                    'textarea'              => ['name'=>'Textarea','placeholder'=>''] ,
                    'editor'                => ['name'=>'Editor ','placeholder'=>''] ,
                    'select'                => ['name'=>'Select Options','placeholder'=>'table:key:field1-field2'] ,
                    'radio'                 => ['name'=>'Radio' ,'placeholder'=>'value2,display, etc ...'] ,
                    'checkbox'              => ['name'=>'Checkbox','placeholder'=>'value2,display, etc ...'] ,
                    'image'                 => ['name'=>'Upload Image','placeholder'=>' /uploads/path_to_upload/'],
                    'file'                  => ['name'=>'Upload File','placeholder'=>' /uploads/path_to_upload/']
                );

                return view('sximo.form.manage',$this->data);
                break;    
            case 'preview':
                $id = $request->input('id');
                return \FormHelpers::render($id); 
                break;  
        }
        
    }  
    public function store( Request $request )
    {
        $task = $request->input('task');
        switch($task){
            default:
                break;
            case 'field':
                return $this->store_field( $request );
                break;  

        }
       
    }	

    public function store_form( $request ) {

        $field = $request->input('field');
        $data = array(
            'title'             => $request->input('title') ,
            'method'            => $request->input('method') ,
            'email'             => $request->input('email') ,
            'tablename'          => $request->input('database') ,
            'success'           => $request->input('success') ,
            'failed'           => $request->input('failed') ,
            'redirect'          => $request->input('redirect') ,
        );

    }    

    public function store_field( $request ) {

        $id = $request->input('id');
       
        $data = array(
            'name'             => $request->input('name') ,
            'method'            => $request->input('method') ,
            'email'             => $request->input('email') ,
            'tablename'          => $request->input('tablename') ,
            'success'           => $request->input('success') ,
            'failed'           => $request->input('failed') ,
            'redirect'          => $request->input('redirect') ,
        );

        $forms = array();
        for($i = 0; $i<count($request->input('counter')); $i++ )
        {
            $field      = $request->input('f_field')[$i];


            if($field !='')
            {         
                $forms[$field] = array(
                    'title'         => $request->input('f_title')[$i] ,
                    'validation'    =>  $request->input('f_validation')[$i] ,
                    'type'          => $request->input('f_type')[$i] ,
                    'param'        => $request->input('f_param')[$i] ,
                );
            }               
        }
        $forms = json_encode($forms);
        $data['configuration'] = $forms ;
        if($id == ''):
            \DB::table('tb_forms')->insert( $data);
        else :
            \DB::table('tb_forms')->where('formID',$id)->update( $data);
        endif;    

        return response()->json(['status'=>'success','message'=>'Form has been updated !']);
    }
}	