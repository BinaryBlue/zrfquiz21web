<?php namespace App\Http\Controllers\sximo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Input, Redirect; 



class ServerController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {           
            if(session('gid') !='1')
                return redirect('dashboard')
                ->with('messagetext','You Dont Have Access to Page !')->with('msgstatus','error');            
            return $next($request);
        });        
        $driver             = config('database.default');
        $database           = config('database.connections');
       
        $this->db           = $database[$driver]['database'];
        $this->dbuser       = $database[$driver]['username'];
        $this->dbpass       = $database[$driver]['password'];
        $this->dbhost       = $database[$driver]['host']; 
        $this->sximo_server = config('version')['Repository']  ;    
    }

    function index()
    {
        $this->data['version'] = config('version');
        return view('sximo.server.index', $this->data);
    }
    function show( Request $request , $task )
    {
        switch ($task) {
            case 'repository':
                 return view('sximo.server.repository', $this->data);
                break;
            case 'install':
                $this->data['id'] = $request->input('id');
                $this->data['type'] = $request->input('t');
                 return view('sximo.server.authen', $this->data);
                break;    
             case 'load':
                $page = $request->input('page') ;
                $page = ( $page =='' ? '' : '&page='.$page);
                $getRows = file_get_contents($this->sximo_server.'repos' ) or die ('ERROR');
                $rows = json_decode( $getRows   );
               // echo '<pre>'; print_r($rows); echo '</pre>'; exit;
                $this->data['rows'] =  $rows->data ;
               
                return view('sximo.server.item', $this->data);
                break;           
            default:
                return $this->version( $request );
                break;
        }

    }
    public function store( Request $request ) {

        $do = $request->input('do');
        switch ($do) {
            case 'install':
                return $this->doInstall($request);
                break;
            
            default:
                return $this->doUpdate($request);
                break;
        }
    }

    public function doInstall( $request) 
    {
        $id     = $request->input('ProductID');
        $type     = $request->input('type');
        $email = $request->input('email');
        $password = $request->input('password');
        $uname = $email.':'.$password ; 
 
        $temp = public_path()."/uploads/zip/". $id.'.zip';
        $message =  '';
      //  return 'http://sximo5.net/api/download?id='.$id.'&uname='.$uname;
        $auth = base64_encode("$uname");
        $context = stream_context_create([
            "http" => [
                "header" => "Authorization: Basic $auth"
            ]
        ]);
        $newInstall = file_get_contents( $this->sximo_server .'repos/download?id='.$id) or die ('ERROR');
        $test = json_decode($newInstall,true);
        if( is_array($test) && count($test) >=1 ){
            $result = json_decode($newInstall,true);
            return response()->json(['status'=>'error','message'=> $result['message']]);  

        } else { 

            $dlHandler = fopen($temp, 'w');
            if ( !fwrite($dlHandler, $newInstall) ) { echo '<p>Could not save new update. Operation aborted.</p>'; exit(); }
            fclose($dlHandler);

            if($type =='' or $type =='module') {
                $data = \SximoHelpers::cf_unpackage( $temp );
                $msg = '.';
                if( isset($data['sql_error'])){
                    $msg = ", with SQL error ". $data['sql_error'];
                    return response()->json(['status'=>'success','message'=>  $msg ]); 
                }  
                unlink($temp);  
                self::createRouters();              
            } else if( $type ='core') {
                \SximoHelpers::upgrade( $temp);
                unlink($temp);               
            }  
            return response()->json(['status'=>'success','message'=> 'Install successfull !']); 
        }  

    }

    public function doUpdate( $request) {

           $path = base_path()."/resources/views/sximo/server/version.json";
            


            $version = $request->input('version');
            $email = $request->input('email');
            $password = $request->input('password');
            $uname = $email.'|'.$password ; 


            $temp = public_path()."/uploads/zip/". $version.'.zip';
            if ( !is_file(  $temp )) 
            {
                 $auth = base64_encode("$uname");
                $context = stream_context_create([
                    "http" => [
                        "header" => "Authorization: Basic $auth"
                    ]
                ]);
             
                $message =  '';
                $getVersions = config('version');
                $newUpdate = file_get_contents($this->sximo_server . 'repos/upgrade');
                $test = json_decode($newUpdate,true);
                 if( is_array($test) && count($test) >=1 ){
                    $result = json_decode($newUpdate,true);
                    return response()->json(['status'=>'error','message'=> $result['message']]);    
                } else {                    
                    $dlHandler = fopen($temp, 'w');
                    if ( !fwrite($dlHandler, $newUpdate) ) { echo '<p>Could not save new update. Operation aborted.</p>'; exit(); }
                    fclose($dlHandler);

                    if(\SximoHelpers::upgrade( $temp))
                    {
                        $message .= '  Update successfully ' ;
                        $change_version = 
                        $fp=fopen($path,"w+");              
                        fwrite($fp, json_encode($getVersions)); 
                        fclose($fp);    
                    }               
                    unlink($temp);
                    
                    return response()->json(['status'=>'success','message'=>$message]);
                }   

            } else {
                return response()->json(['status'=>'error','message'=>'Failed to Update(s) !']);
            }    

    }


    public function version( Request $request ) {

        $curr_version = config('version');
        //if($check){            
            ini_set('max_execution_time',60);
            //Check For An Update
            $getVersions = file_get_contents( $this->sximo_server .'repos/changelog?codename='.$curr_version['Codename']) or die ('ERROR');
           
             $checkVersion =[];
            if ($getVersions != '')
            {
                $checkVersion =  json_decode($getVersions,true);
            }
          //  echo '<pre>';print_r($checkVersion); echo '</pre>'; exit;
            if( count($checkVersion) >=1 && $checkVersion['updated']['status'] =='success') {
                return response()->json(['status'=>'success','message'=>'Updates Available ','version'=>  $checkVersion['updated']['data']['Version']]);
            } else {
                 return response()->json(['status'=>'error','message'=>'No Updates Available']);
            }
 


        //}   

    }
    function createRouters()
    {
        $rows = \DB::table('tb_module')->where('module_type','!=','core')->get();
        $val  =    "<?php
        "; 
       foreach($rows as $row)
        {
            $class = $row->module_name;
            $controller = ucwords($row->module_name).'Controller';

           $mType = ( $row->module_type =='addon' ? 'native' :  $row->module_type);
           include(base_path().'/resources/views/sximo/module/template/'.$row->module_type.'/config/route.php' );
            
  
        }
        $val .=     "?>";
         $filename = base_path().'/routes/module.php';
        $fp=fopen($filename,"w+"); 
        fwrite($fp,$val); 
        fclose($fp);    
        return true;    
        
    } 
}