<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\Libary\SiteHelpers;
use Socialize;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;


class UserController extends Controller {

	
	protected $layout = "layouts.main";

	public function __construct() {
		parent::__construct();
		$this->data = array();

	} 

	public function getRegister() {
        
		if(config('sximo.cnf_regist') =='false') :    
			if(\Auth::check()):
				 return redirect('')->with(['message'=>'Youre already login','status'=>'error']);
			else:
				 return redirect('user/login');
			  endif;			  
		else :
				$this->data['socialize'] =  config('services');
				return view('user.register', $this->data);  
		 endif ;        
	}
	public function postCreate( Request $request) {
	
		$rules = array(
			'username'=>'required|alpha|between:3,12|unique:tb_users',
			'firstname'=>'required|alpha_num|min:2',
			'lastname'=>'required|alpha_num|min:2',
			'email'=>'required|email|unique:tb_users',
			'password'=>'required|between:6,12|confirmed',
			'password_confirmation'=>'required|between:6,12'
		);	

		if($request->has('mobile')) {
			unset($rules['password_confirmation']);
			$rules['password'] = 'required|between:6,12';
		} 
		else {
			if(config('sximo.cnf_recaptcha') =='true') 
			{
				$return = $this->reCaptcha($request->all());
				if($return !== false)
				{
					if($return['success'] !='true')
					{
						return response()->json(['status' => $return['success'], 'message' =>'Invalid reCpatcha']);	
					}
					
				}
			}
		}

		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$code = rand(10000,10000000);			
			$authen = new User;
			$authen->username = $request->input('username');
			$authen->first_name = $request->input('firstname');
			$authen->last_name = $request->input('lastname');
			$authen->email = trim($request->input('email'));
			$authen->activation = $code;
			$authen->group_id = $this->config['cnf_group'];
			$authen->password = \Hash::make($request->input('password'));
			if($this->config['cnf_activation'] == 'auto') { $authen->active = '1'; } else { $authen->active = '0'; }
			$authen->save();
			
			$data = array(
				'firstname'	=> $request->input('firstname') ,
				'lastname'	=> $request->input('lastname') ,
				'email'		=> $request->input('email') ,
				'password'	=> $request->input('password') ,
				'code'		=> $code ,
				'subject'	=> "[ " .$this->config['cnf_appname']." ] REGISTRATION "				
			);
			if(config('sximo.cnf_activation') == 'confirmation')
			{ 
				$to = $request->input('email');
				$subject = "[ " .$this->config['cnf_appname']." ] REGISTRATION "; 			
				if($this->config['cnf_mail'] =='swift')
				{ 				
					\Mail::send('user.emails.registration', $data, function ($message) use ($data) {
			    		$message->to($data['email'])->subject($data['subject']);
			    	});				    	
				}  else {		
					$message = view('user.emails.registration', $data);
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: '.$this->config['cnf_appname'].' <'.$this->config['cnf_email'].'>' . "\r\n";
						mail($to, $subject, $message, $headers);	
				}

				$message = "Thanks for registering! . Please check your inbox and follow activation link";
								
			} elseif($this->config['cnf_activation']=='manual') {
				$message = "Thanks for registering! . We will validate you account before your account active";
			} else {
   			 	$message = "Thanks for registering! . Your account is active now ";         
			
			}	
			if($request->has('mobile') )
			{
				 return response()->json(['status' => 'success', 'message' => $message ]);
			} 
			else {
				return redirect('user/login')->with(['message' => $message,'status'=>'success']);
			}

		} else {
			if($request->has('mobile') )
			{
				 return response()->json(['status' => 'error', 'message' => $validator->errors() ]);
			} 
			else {
				return redirect('user/register')->with(['message'=>'The following errors occurred','status'=>'success'])
				->withErrors($validator)->withInput();
			}
			
		}
	}
	
	public function getActivation( Request $request  )
	{
		$num = $request->input('code');
		if($num =='')
			return redirect('user/login')->with(['message'=>'Invalid Code Activation!','status'=>'error']);
		
		$user =  User::where('activation','=',$num)->get();
		if (count($user) >=1)
		{
			\DB::table('tb_users')->where('activation', $num )->update(array('active' => 1,'activation'=>''));
			return redirect('user/login')->with(['message'=>'Your account is active now!','status'=>'success']);
			
		} else {
			return redirect('user/login')->with(['message'=>'Invalid Code Activation!','status'=>'error']);
		}
		
		
	
	}

	public function getLogin() {
	
		if(\Auth::check())
		{
			return redirect('')->with(['message'=>'success','Youre already login','status'=>'success']);

		} else {
			$this->data['socialize'] =  config('services');
			return View('user.login',$this->data);
			
		}	
	}

	public function reCaptcha( $request)
	{
		if(!is_null($request['g-recaptcha-response']))
        {
            $api_url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . config('sximo.cnf_recaptchaprivatekey') . '&response='.$request['g-recaptcha-response'];
            $response = @file_get_contents($api_url);
            $data = json_decode($response, true);
 
           return $data;
        }
        else
        {
           return false ;
        }		
	}

	public function postSignin( Request $request) {

		$rules = array(
			'uid'=>'required',
			//'password'=>'required',
		);		
		if(config('sximo.cnf_recaptcha') =='true') {
			$return = $this->reCaptcha($request->all());
			if($return !== false)
			{
				if($return['success'] !='true')
				{
					return response()->json(['status' => $return['success'], 'message' =>'Invalid reCpatcha']);	
				}
				
			}
		}
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {	

			$remember = (!is_null($request->get('remember')) ? 'true' : 'false' );
			
			if (\Auth::attempt(array('email'=>$request->input('email'), 'password'=> $request->input('password') ), $remember )
				or 
				\Auth::attempt(array('username'=>$request->input('email'), 'password'=> $request->input('password') ), $remember )
				or 
				\Auth::attempt(array('uid'=>$request->input('uid'), 'password'=> '12345678' ), $remember )

			) {
				if(\Auth::check())
				{
					$row = User::find(\Auth::user()->id); 	
					if($row->active =='Inactive')
					{
						// inactive 
						if($request->ajax() == true )
						{
							return response()->json(['status' => 'error', 'message' => 'Your Account is not active']);
						} else {
							\Auth::logout();
							return redirect('user/login')->with(['status' => 'error', 'message' => 'Your Account is not active']);
						}
						
					} else if($row->active=='Banned')
					{

						if($request->ajax() == true )
						{
							return response()->json(['status' => 'error', 'message' => 'Your Account is BLocked']);
						} else {
							// BLocked users
							\Auth::logout();
							return redirect('user/login')->with(['status' => 'error', 'message' => 'Your Account is BLocked']);
						}
					} else {

						//B2B Custom Passport
						// $client = new Client(['base_uri' => env('API_URL')]);
						// $response = $client->request('POST', 'api/v1/user/login', ['form_params' => [
						// 	'email' => $request->input('email'),
						// 	'password' => $request->input('password')
						// ]]);

						// $ch = curl_init();
						// curl_setopt($ch, CURLOPT_URL, env('API_URL').'api/v1/user/login');
						// // SSL important
						// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
						// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						// curl_setopt($ch, CURLOPT_POSTFIELDS, ['email'=>$request->input('email'),'password'=>$request->input('password')]);

						// $output = curl_exec($ch);
						// curl_close($ch);
						// dd($output);
						// $client = new Client();
						// $response = $client->request('POST', 'http://127.0.0.1:8000/api/v1/user/login', ['form_params' => [
						// 	'email' => $request->input('email'),
						// 	'password' => $request->input('password')
						// ]]);
						
						//Final
						// $response = Http::asForm()->post(env('API_URL').'api/v1/user/login', [
						// 	'email' => $request->input('email'),
						// 	'password' => $request->input('password')
						// ]);
						//dd($response->json());
						//$pData = $response->json();
						\DB::table('tb_users')->where('id', '=',$row->id )->update(array('p_token' => $request->input('access_tokken')));


						\DB::table('tb_users')->where('id', '=',$row->id )->update(array('last_login' => date("Y-m-d H:i:s")));
						$level = 99;
						$sql = \DB::table('tb_groups')->where('group_id' , $row->group_id )->get();
				        if(count($sql))
				        {
				            $l = $sql[0];
				            $level = $l->level ;
				        }

						$session = array(
							'p_token' => $request->input('access_tokken'),
							'gid' => $row->group_id,
							'uid' => $row->id,
							'eid' => $row->email,
							'll' => $row->last_login,
							'fid' =>  $row->first_name.' '. $row->last_name,
							'username' =>  $row->username ,
							'join'	=>  $row->created_at ,
							'level'	=> $level 
						);
						/* Set Lang if available */
						if(!is_null($request->input('language')))
						{
							$session['lang'] = $request->input('language');		
						} else {
							$session['lang'] = config('sximo.cnf_lang');
							
						}
						
						if($request->ajax() == true )
						{

							if( config('sximo.cnf_front') =='false') :
								return response()->json(['status' => 'success', 'url' => url('dashboard')]);					
							else :
								return response()->json(['status' => 'success', 'url' => url('')]);
							endif;	

						} 
						else {
							session($session);
							if( config('sximo.cnf_front') =='false') :
								return redirect('dashboard');						
							else :
								return redirect('');
							endif;	
						}				
					}	
				}		
				
			} 
			else {

				if($request->ajax() == true )
				{
					return response()->json(['status' => 'error', 'message' => 'Your username/password combination was incorrect']);
				} else {

					return redirect('user/login')
						->with(['status' => 'error', 'message' => 'Your username/password combination was incorrect'])
						->withInput();					
				}
			}
		} 
		else {

				if($request->ajax() == true)
				{
					return response()->json(['status' => 'error', 'message' => 'The following  errors occurred']);
				} else {

					return redirect('user/login')
						->with(['status' => 'error', 'message' => 'The following  errors occurred'])
						->withErrors($validator)->withInput();
				}	

		}	
	}

    public function postSigninMobile(  Request $request ) {
        $rules = array(
            'email'                 =>'required|email',
            'password'              =>'required'
        );  
        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $email      =  trim($request->input('email')) ;
            $password   =  trim($request->input('password')) ;
            if (\Auth::attempt(array('email'=> $email, 'password'=> $password ))) {

                $row = User::find(\Auth::user()->id);   
                if($row->status =='0')
                {
                    return response()->json(['status' => 'error', 'message' => ' Your account is active yet ' ]);
                } 
                else if( $row->status == '2') {
                    return response()->json(['status' => 'error', 'message' => ' Your account is blocked / banned ' ]);
                } 
                else {
                   
                    $data = array(
                            'gid' => $row->group_id,
                            'uid' => $row->id,
                            'eid' => $row->email,
                            'll' => $row->last_login,
                            'fid' =>  $row->name,
                            'uname'     => $row->username ,
                            'logged_in' => true ,
                            'join'  =>  $row->created_at                             
                        );
                    if(file_exists('./uploads/users/'.$row->avatar ) && $row->avatar !='') {
                            $data['avatar'] = asset('uploads/users/'.$row->avatar) ;
                        } else {
                            $data['avatar'] =  asset('uploads/users/avatar.png') ;
                        }
                    $data['access'] = [];

                    $token =  hash('sha256',Str::random(60)) ;

                    \DB::table('tb_token')->insert([
                    	'userId'	=> $row->id ,
                    	'token'		=> $token ,
                    	'created'	=> date("Y-m-d H:i:s")
                	]);

                    return response()->json([
                        'status' => 'success', 
                        'message' => 'You are Logged',
                        'token'=> $token,
                        'data'   => $data
                    ]);
                }
            } 
            else {
                return response()->json(['status' => 'error', 'message' => 'Your username/password combination was incorrect']);
            }
        }
        else {
            return response()->json(['status' => 'error', 'message' => $validator->errors() ]);
        }
    }

	public function getProfile() {
		
		if(!\Auth::check()) return redirect('user/login');
		
		
		$info =	User::find(\Auth::user()->id);
		$this->data = array(
			'pageTitle'	=> 'My Profile',
			'pageNote'	=> 'View Detail My Info',
			'info'		=> $info,
		);
		return view('user.profile',$this->data);
	}

	public function getTheme() {
		
		return view('layouts.palette');
	}	
	public function postSaveprofile( Request $request)
	{
		if(!\Auth::check()) return redirect('user/login');
		$rules = array(
			'first_name'=>'required|alpha_num|min:2',
			'last_name'=>'required|alpha_num|min:2',
			);	
			
		if($request->input('email') != \Session::get('eid'))
		{
			$rules['email'] = 'required|email|unique:tb_users';
		}	

		if(!is_null($request->file('avatar'))) $rules['avatar'] = 'mimes:jpg,jpeg,png,gif,bmp';

				
		$validator = Validator::make($request->all(), $rules);

		if ($validator->passes()) {
			
			
			if(!is_null($request->file('avatar')))
			{
				$file = $request->file('avatar'); 
				$destinationPath = './uploads/users/';
				$filename = $file->getClientOriginalName();
				$extension = $file->getClientOriginalExtension(); //if you need extension of the file
				$newfilename = \Session::get('uid').'.'.$extension;
				$uploadSuccess = $request->file('avatar')->move($destinationPath, $newfilename);				 
				if( $uploadSuccess ) {
				    $data['avatar'] = $newfilename; 
				}
				$orgFile = $destinationPath.'/'.$newfilename; 
				\SiteHelpers::cropImage('80' , '80' , $orgFile ,  $extension,	 $orgFile)	;
				
			}		
			
			$user = User::find(\Session::get('uid'));
			$user->first_name 	= $request->input('first_name');
			$user->last_name 	= $request->input('last_name');
			$user->email 		= $request->input('email');
			if(isset( $data['avatar']))  $user->avatar  = $newfilename; 			
			$user->save();

			$newUser = User::find(\Session::get('uid'));

			\Session::put('fid',$newUser->first_name.' '.$newUser->last_name);

			return redirect('user/profile')->with('message','Profile has been saved!')->with('status','success');
		} else {
			return redirect('user/profile')->with('message','The following errors occurred')->with('status','error')
			->withErrors($validator)->withInput();
		}	
	
	}
	
	public function postSavepassword( Request $request)
	{
		$rules = array(
			'password'=>'required|between:6,12',
			'password_confirmation'=>'required|between:6,12'
			);		
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$user = User::find(\Session::get('uid'));
			$user->password = \Hash::make($request->input('password'));
			$user->save();

			return redirect('user/profile')->with(['status' => 'success', 'message' => 'Password has been saved!'] );
		} else {
			return redirect('user/profile')->with(['status' => 'error', 'message' => 'The following errors occurred'])
			->withErrors($validator)->withInput();
		}	
	
	}	
	
	public function getReminder()
	{
	
		return view('user.remind');
	}	

	public function postRequest( Request $request)
	{

		$rules = array(
			'credit_email'=>'required|email'
		);	
		
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {	
	
			$user =  User::where('email','=',$request->input('credit_email'));
			if($user->count() >=1)
			{
				$user = $user->get();
				$user = $user[0];
				$data = array('token'=>$request->input('_token'));	
				$to = $request->input('credit_email');
				$subject = "[ " .config('sximo.cnf_appname')." ] REQUEST PASSWORD RESET "; 
				$data['subject'] =  $subject;	
				$data['email'] = $to;

				if(config('sximo.cnf_mail') =='swift')
				{ 
					
					\Mail::send('user.emails.auth.reminder', $data, function ($message) use ($data)  {
			    		$message->to($data['email'])->subject($data['subject']);
			    	});	 
			    	

				}  else {

							
					$message = view('user.emails.auth.reminder', $data);
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: '.config('sximo.cnf_appname').' <'.config('sximo.cnf_email').'>' . "\r\n";
						mail($to, $subject, $message, $headers);	
				}					
			
				
				$affectedRows = User::where('email', '=',$user->email)
								->update(array('reminder' => $request->input('_token')));
								
				return redirect('user/login')->with(['message' => 'Please check your email','status'=>'success']);	
				
			} else {
				return redirect('user/login?reset')->with(['message' => 'Cant find email address','status'=>'error']);
			}

		}  else {
			return redirect('user/login?reset')->with(['message' => 'The following errors occurred','status'=>'error'])->withErrors($validator)->withInput();
		}	 
	}	
	
	public function getReset( $token = '')
	{
		if(\Auth::check()) return redirect('dashboard');

		$user = User::where('reminder','=',$token);;
		if($user->count() >=1)
		{
			$this->data['verCode']= $token;
			return view('user.remind',$this->data);

		} else {
			return redirect('user/login')->with(['message'=>'Cant find your reset code','status'=>'error']);
		}
		
	}	
	
	public function postDoreset( Request $request , $token = '')
	{
		$rules = array(
			'password'=>'required|alpha_num|between:6,12|confirmed',
			'password_confirmation'=>'required|alpha_num|between:6,12'
			);		
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			
			$user =  User::where('reminder','=',$token);
			if($user->count() >=1)
			{
				$data = $user->get();
				$user = User::find($data[0]->id);
				$user->reminder = '';
				$user->password = \Hash::make($request->input('password'));
				$user->save();			
			}

			return redirect('user/login')->with(['message'=>'Password has been saved!','status'=>'success'] );
		} else {
			return redirect('user/reset/'.$token)->with(['message'=>'The following errors occurred','status'=>'error'])->withErrors($validator)->withInput();
		}	
	
	}	

	public function getLogout() {
		\Auth::logout();
		\Session::flush();
		return redirect('')->with(['message'=>'Your are now logged out!','status'=>'success']);
	}

	function socialize( $social )
	{
		return Socialize::driver($social)->redirect();
	}

	function autosocialize( $social )
	{
		$user = Socialize::driver($social)->user();		
		$users = \DB::table('tb_users')->where('email',$user->email)->get();

		if(count($users)){
			$row = $users[0];
			return self::autoSignin($row->id);		

		} else {
			return redirect('user/login')
				->with(['message'=>'You have not registered yet ','status'=>'error']);
		}
			
	}

	function autoSignin($id)
	{
		
		if(is_null($id)){
		  return redirect('user/login')
				->with(['message'=>'You have not registered yet ','status'=>'error']);
		} else{

		    \Auth::loginUsingId( $id );
			if(\Auth::check())
			{
				$row = User::find(\Auth::user()->id); 

				if($row->active =='0')
				{
					// inactive 
					\Auth::logout();
					return redirect('user/login')->with(['message'=>'Your Account is not active','status'=>'error']);

				} else if($row->active=='2')
				{
					// BLocked users
					\Auth::logout();
					return redirect('user/login')->with(['message'=>'Your Account is BLocked','status'=>'error']);
				} else {
					$session = array(
						'gid' => $row->group_id,
						'uid' => $row->id,
						'eid' => $row->email,
						'll' => $row->last_login,
						'fid' =>  $row->first_name.' '. $row->last_name,
						'username' =>  $row->username ,
						'join'	=>  $row->created_at
					);
					if($this->config['cnf_front'] =='false') :
						return redirect('dashboard');						
					else :
						return redirect('');
					endif;					
					
										
				}
				
				
			}
		}

	}
	
	
}