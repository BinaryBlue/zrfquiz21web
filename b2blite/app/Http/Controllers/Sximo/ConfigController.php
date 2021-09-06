<?php namespace App\Http\Controllers\sximo;

use App\Http\Controllers\Controller;
use App\Models\Core\Groups;
use App\User;
use Illuminate\Http\Request;
use Validator, Input, Redirect; 

class ConfigController extends Controller {

    public function __construct()
    {
    	parent::__construct();
        $this->middleware(function ($request, $next) {           
            if(session('gid') !='1')
                return redirect('dashboard')
                ->with('message','You Dont Have Access to Page !')->with('status','error');            
            return $next($request);
        });
        $this->data['pageTitle'] =  __('core.t_generalsetting');
        $this->data['pageNote']  =   __('core.t_generalsettingsmall');

       
    }

	public function getIndex()
	{	
 		$this->data['pageTitle'] =  __('core.t_generalsetting');
        $this->data['pageNote']  =   __('core.t_generalsettingsmall');	
		$this->data['active'] = '';
		return view('sximo.config.index',$this->data);	
	}


	public function postSave( Request $request )
	{
           
		$rules = array(
			'cnf_appname'=>'required|min:2',
			'cnf_appdesc'=>'required|min:2',
			'cnf_comname'=>'required|min:2',
			'cnf_email'=>'required|email',
		);
		$validator = Validator::make($request->all(), $rules);	
		if (!$validator->fails()) 
		{
			$logo = '';
			if(!is_null( $request->file('logo')))
			{

				$file =  $request->file('logo'); 
			 	$destinationPath = public_path().'/uploads/images/'; 
				$filename = $file->getClientOriginalName();
				$extension =$file->getClientOriginalExtension(); //if you need extension of the file
				$logo = 'backend-logo.'.$extension;
				$uploadSuccess = $file->move($destinationPath, $logo);
			}

$val  =		"<?php \n"; 
$val  .= 	"return [\n";
	$val  .= 	"'cnf_appname' 			=> '".$request->input('cnf_appname')."',\n";
	$val  .= 	"'cnf_appdesc' 			=> '".$request->input('cnf_appdesc')."',\n";
	$val  .= 	"'cnf_comname' 			=> '".$request->input('cnf_comname')."',\n";
	$val  .= 	"'cnf_email' 			=> '".$request->input('cnf_email')."',\n";
	$val  .= 	"'cnf_metakey' 			=> '".$request->input('cnf_metakey')."',\n";
	$val  .= 	"'cnf_metadesc' 		=> '".$request->input('cnf_metadesc')."',\n";
	$val  .= 	"'cnf_group' 			=> '".$this->config['cnf_group']."',\n";
	$val  .= 	"'cnf_activation' 		=> '".$this->config['cnf_activation']."',\n";
	$val  .= 	"'cnf_multilang' 		=> '".(!is_null($request->input('cnf_multilang')) ? 1 : 0 )."',\n";
	$val  .= 	"'cnf_lang' 			=> '".$request->input('cnf_lang')."',\n";
	$val  .= 	"'cnf_regist' 			=> '".$this->config['cnf_regist']."',\n";
	$val  .= 	"'cnf_front' 			=> '".(!is_null($request->input('cnf_front')) ? 'true' : 'false')."',\n";
	$val  .= 	"'cnf_recaptcha' 		=> '".$this->config['cnf_recaptcha']."',\n";
	$val  .= 	"'cnf_theme' 			=> '".$request->input('cnf_theme')."',\n";
	$val  .= 	"'cnf_backend' 			=> '".$request->input('cnf_backend')."',\n";
	$val  .= 	"'cnf_recaptchapublickey' => '".$this->config['cnf_recaptchapublickey']."',\n";
	$val  .= 	"'cnf_recaptchaprivatekey' => '".$this->config['cnf_recaptchaprivatekey']."',\n";
	$val  .= 	"'cnf_mode' 			=> '".(!is_null($request->input('cnf_mode')) ? 'production' : 'development' )."',\n";
	$val  .= 	"'cnf_logo' 			=> '".($logo !=''  ? $logo : $this->config['cnf_logo'])."',\n";
	$val  .= 	"'cnf_allowip' 			=> '".$this->config['cnf_allowip']."',\n";
	$val  .= 	"'cnf_restrictip' 		=> '".$this->config['cnf_restrictip']."',\n";
	$val  .= 	"'cnf_mail' 			=> '".$this->config['cnf_mail']."',\n";
	$val  .= 	"'cnf_maps' 			=> '".$this->config['cnf_maps']."',\n";
	$val  .= 	"'cnf_date' 			=> '".(!is_null($request->input('cnf_date')) ? $request->input('cnf_date') : 'Y-m-d' )."',\n";
$val  .= 	"];\n";
	
			$filename = base_path().'/config/sximo.php';
			//chmod(  $filename , 775);
			$fp=fopen($filename,"w+"); 
			fwrite($fp,$val); 
			fclose($fp); 

			return redirect('sximo/config')->with('message','Setting Has Been Save Successful')->with('status','success');
		} else {
			return redirect('sximo/config')->with('message', 'The following errors occurred')->with('status','success')
			->withErrors($validator)->withInput();
		}			
	
	}




	public function getEmail()
	{
		
		$regEmail = base_path()."/resources/views/user/emails/registration.blade.php";
		$resetEmail = base_path()."/resources/views/user/emails/auth/reminder.blade.php";
		$this->data = array(
			'groups'	=> Groups::all(),
			'pageTitle'	=> __('core.t_emailtemplate'),
			'pageNote'	=> __('core.t_emailtemplatesmall'),
			'regEmail' 	=> file_get_contents($regEmail),
			'resetEmail'	=> 	file_get_contents($resetEmail),
			'active'		=> 'email',
		);	
		return view('sximo.config.email',$this->data);		
	
	}
	
	function postEmail( Request $request)
	{
		
		//print_r($_POST);exit;
		$rules = array(
			'regEmail'		=> 'required|min:10',
			'resetEmail'		=> 'required|min:10',				
		);	
		$validator = Validator::make($request->all(), $rules);	
		if ($validator->passes()) 
		{
			$regEmailFile = base_path()."/resources/views/user/emails/registration.blade.php";
			$resetEmailFile = base_path()."/resources/views/user/emails/auth/reminder.blade.php";			
			
			$fp=fopen($regEmailFile,"w+"); 				
			fwrite($fp,$_POST['regEmail']); 
			fclose($fp);	
			
			$fp=fopen($resetEmailFile,"w+"); 				
			fwrite($fp,$_POST['resetEmail']); 
			fclose($fp);
			
			return redirect('sximo/config/email')->with('message', 'Email Has Been Updated')->with('status','success');	
			
		}	else {

			return redirect('sximo/config/email')->with('message', 'The following errors occurred')->with('status','success')
			->withErrors($validator)->withInput();
		}
	
	}
	
	public function getSecurity()
	{

		$this->data['groups']	= Groups::all();
		$this->data['pageTitle']	= __('core.t_loginsecurity');
		$this->data['pageNote']	= __('core.t_loginsecuritysmall');
		$this->data['active']	= 'security';

		//print_r($this->data) ;exit;
	
		return view('sximo.config.security',$this->data);		
	
	}	
	
		

	
	public function postLogin( Request $request)
	{

		$rules = array(

		);
		$validator = Validator::make($request->all(), $rules);	
		if ($validator->passes()) {


$val  =		"<?php \n"; 
$val  .= 	"return [\n";
	$val  .= 	"'cnf_appname' 			=> '".$this->config['cnf_appname']."',\n";
	$val  .= 	"'cnf_appdesc' 			=> '".$this->config['cnf_appdesc']."',\n";
	$val  .= 	"'cnf_comname' 			=> '".$this->config['cnf_comname']."',\n";
	$val  .= 	"'cnf_email' 			=> '".$this->config['cnf_email']."',\n";
	$val  .= 	"'cnf_metakey' 			=> '".$this->config['cnf_metakey']."',\n";
	$val  .= 	"'cnf_metadesc' 		=> '".$this->config['cnf_metadesc']."',\n";
	$val  .= 	"'cnf_group' 			=> '".$request->input('CNF_GROUP')."',\n";
	$val  .= 	"'cnf_activation' 		=> '".$request->input('CNF_ACTIVATION')."',\n";
	$val  .= 	"'cnf_multilang' 		=> '".$this->config['cnf_multilang']."',\n";
	$val  .= 	"'cnf_lang' 			=> '".$this->config['cnf_lang']."',\n";
	$val  .= 	"'cnf_regist' 			=> '".(!is_null($request->input('CNF_REGIST')) ? 'true':'false')."',\n";
	$val  .= 	"'cnf_front' 			=> '".(!is_null($request->input('CNF_FRONT')) ? 'true':'false')."',\n";
	$val  .= 	"'cnf_recaptcha' 		=> '".(!is_null($request->input('cnf_recaptcha')) ? 'true':'false')."',\n";
	$val  .= 	"'cnf_theme' 			=> '".$this->config['cnf_theme']."',\n";
	$val  .= 	"'cnf_backend' 			=> '".$this->config['cnf_backend']."',\n";
	$val  .= 	"'cnf_recaptchapublickey' => '".$request->input('cnf_recaptchapublickey')."',\n";
	$val  .= 	"'cnf_recaptchaprivatekey' => '".$request->input('cnf_recaptchaprivatekey')."',\n";
	$val  .= 	"'cnf_mode' 			=> '".$this->config['cnf_mode']."',\n";
	$val  .= 	"'cnf_logo' 			=> '".$this->config['cnf_logo']."',\n";
	$val  .= 	"'cnf_allowip' 			=> '".$request->input('CNF_ALLOWIP')."',\n";
	$val  .= 	"'cnf_restrictip' 		=> '".$request->input('CNF_RESTRICIP')."',\n";
	$val  .= 	"'cnf_mail' 			=> '".(!is_null($request->input('CNF_MAIL')) ? $request->input('CNF_MAIL'):'phpmail')."',\n";
	$val  .= 	"'cnf_maps' 			=> '".$request->input('CNF_MAPS')."',\n";
	$val  .= 	"'cnf_date' 			=> '".$this->config['cnf_date']."',\n";
$val  .= 	"];\n";
	
			$filename = base_path().'/config/sximo.php';
			$fp=fopen($filename,"w+"); 
			fwrite($fp,$val); 
			fclose($fp); 
			return redirect('sximo/config/security')->with('message','Setting Has Been Save Successful')->with('status','success');
		} else {
			return redirect('sximo/config/security')->with('message', 'The following errors occurred')->with('status','error')
			->withErrors($validator)->withInput();
		}	
	}
	
	public function getLog( $type = null)
	{
	
		
		$this->data = array(
			'pageTitle'	=> __('core.m_clearcache'),
			'pageNote'	=> __('core.dash_clearcache'),
			'active'	=> 'log'
		);	
		return view('sximo.config.log',$this->data);	
	}
		
	
	public function getClearlog()
	{
		
		$dir = base_path()."/storage/logs";	
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
			{
				//removedir($file);
			} else {

				unlink($file);
			}
		}

		$dir = base_path()."/storage/framework/views";	
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
			{
				//removedir($file);
			} else {
				
				unlink($file);
			}
		}		

		return response()->json(array(
			'status'	=>__('core.note_t_success'),
			'message'	=>  __('core.note_success_action')
		));


		//return redirect('sximo/config/log')->with('message','Cache has been cleared !')->with('status','success');	
	}
	
	function removeDir($dir) {
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				removedir($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}
	
	public function getTranslation( Request $request, $type = null)
	{
		if(!is_null($request->input('edit')))
		{
			$file = (!is_null($request->input('file')) ? $request->input('file') : 'core.php'); 
			$files = scandir(base_path()."/resources/lang/".$request->input('edit')."/");

			//$str = serialize(file_get_contents('./protected/app/lang/'.$request->input('edit').'/core.php'));
			$str = \File::getRequire(base_path()."/resources/lang/".$request->input('edit').'/'.$file);
			
			
			$this->data = array(
				'pageTitle'	=> 'Translation',
				'pageNote'	=> 'Add Multilangues Option',
				'stringLang'	=> $str,
				'lang'			=> $request->input('edit'),
				'files'			=> $files ,
				'file'			=> $file ,
			);	
			$template = 'edit';
		
		} else {

			$this->data = array(
				'pageTitle'	=> 'Translation',
				'pageNote'	=> 'Add Multilangues Option',
			);	
			$template = 'index';		
		
		}

		return view('sximo.config.translation.'.$template,$this->data);	
	}
	
	public function getAddtranslation()
	{
		return view("sximo.config.translation.create");
	} 
	
	public function postAddtranslation( Request $request)
	{
		$rules = array(
			'name'		=> 'required',
			'folder'	=> 'required|alpha',
			'author'	=> 'required',
		);
		$validator = Validator::make($request->all(), $rules);	
		if ($validator->passes()) {

			$template = base_path();

			$folder = $request->input('folder');
			mkdir( $template."/resources/lang/".$folder ,0777 );	
			
			$info = json_encode(array("name"=> $request->input('name'),"folder"=> $folder , "author" => $request->input('author')));
			$fp=fopen(  $template.'/resources/lang/'.$folder.'/info.json',"w+"); 
			fwrite($fp,$info); 
			fclose($fp); 	
					
			$files = scandir( $template .'/resources/lang/en/');
			foreach($files as $f)
			{
				if($f != "." and $f != ".." and $f != 'info.json')
				{
					copy( $template .'/resources/lang/en/'.$f, $template .'/resources/lang/'.$folder.'/'.$f);
				}
			}
			return redirect('sximo/config/translation')->with('messagetect','New Translation has been added !')->with('status','success');	;			
			
		} else {
			return redirect('sximo/config/translation')->with('message','Failed to add translation !' )->with('status','error')->withErrors($validator)->withInput();
		}		
	
	}
	
	public function postSavetranslation( Request $request)
	{
		$template = base_path();
		
		$form  	= "<?php \n"; 
		$form 	.= "return array( \n";
		foreach($_POST as $key => $val)
		{
			if($key !='_token' && $key !='lang' && $key !='file') 
			{
				if(!is_array($val))
				{
					$form .= '"'.$key.'"			=> "'.strip_tags($val).'", '." \n ";
				
				} else {
					$form .= '"'.$key.'"			=> array( '." \n ";
					foreach($val as $k=>$v)
					{
							$form .= '      "'.$k.'"			=> "'.strip_tags($v).'", '." \n ";
					}
					$form .= "), \n";
				}
			}		
		
		}
		$form .= ');';
		//echo $form; exit;
		$lang = $request->input('lang');
		$file	= $request->input('file');
		$filename = $template .'/resources/lang/'.$lang.'/'.$file;
	//	$filename = 'lang.php';
		$fp=fopen($filename,"w+"); 
		fwrite($fp,$form); 
		fclose($fp); 	
		return redirect('sximo/config/translation?edit='.$lang.'&file='.$file)
		->with('message','Translation has been saved !')->with('status','success');	
	
	} 	
	
	public function getRemovetranslation( $folder )
	{
		self::removeDir( base_path()."/resources/lang/".$folder);
		return redirect('sximo/config/translation')->with('message','Translation has been removed !')->with('status','success');	
		
	}		


}