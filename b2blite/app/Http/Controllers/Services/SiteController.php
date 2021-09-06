<?php namespace App\Http\Controllers\Services;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Core\Users;
use App\Models\Core\Groups;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ; 


class SiteController extends Controller
{
    public $user ;
    public function __construct( Request $request )
    {
       // $this->user = SximoHelper::getUserByToken( $request );
    }

    public function info( )
    {
        $config = config('sximo');
        $data = [
            'appname'       => $config['cnf_appname'] ,
            'appdesc'       => $config['cnf_appdesc'] ,
            'comname'       => $config['cnf_comname'] ,
            'email'         => $config['cnf_email'] ,
            'logo'          => $config['cnf_logo'] ,
            'logo_path'     => asset('uploads/images/'.$config['cnf_logo']),
            'registration'  => $config['cnf_regist'] ,
            'site_url'      => url('')
        ];
        return response()->json(['status'=>'success','data'=> $data ] );
    }

    public function show( Request $request  , $route ) {

        switch( $route ) {
            default :
                return $this->info();
                break ;


        }

    }
    public function profile( Request $request ) {
         
        $profile = $request->get('profile');
        if(file_exists('./uploads/users/'.$profile->avatar) &&  $profile->avatar !='' ) {
            $avatar_url = asset('uploads/users/'.$profile->avatar);

        } else {
           $avatar_url = asset('uploads/users/avatar.png');
        }

        $data = [
            'id'            => $profile->id ,
            'username'      => $profile->username ,
            'first_name'    => $profile->first_name ,
            'last_name'     => $profile->last_name ,
            'email'         => $profile->email ,
            'birth_of_day'  => $profile->birth_of_day ,
            'address_1'     => $profile->address_1 ,
            'address_2'     => $profile->address_2 ,
            'phone'         => $profile->phone ,
            'city'          => $profile->city ,
            'state'         => $profile->state ,
            'country'       => $profile->country ,
            'avatar_url'    => $avatar_url 
        ];

        return response()->json(['status' => 'success', 'data'=> $data ]);  
    }
    public function saveprofile( Request $request)
    {
       
        $profile = $request->get('profile');
        $posts = json_decode($request->input('data'),true);
        $pics = json_decode($request->input('avatar'),true);
        $rules = array(
            'first_name'=>'required|alpha_num|min:2',
            'last_name'=>'required|alpha_num|min:2',
            );  
            
        if(isset( $posts['password']) && $posts['password'] != '') {
            $rules['password'] = 'required|between:6,12|confirmed';
        }   
                
        $validator = Validator::make( $posts, $rules);

        if ($validator->passes()) {

            if(is_array($pics)) {
               
                $destinationPath = "./uploads/users/";

                if(file_exists($destinationPath . $user->avatar) && $user->avatar !='') {
                  unlink($destinationPath . $user->avatar);
                }
                $ext   = str_replace("image/","", $pics['ext'] );
                $file   = $pics['url'] ;
                if($ext =='png') {
                  $image    = str_replace("data:image/png;base64,","" , $pics['url'] ); 
                }
                else {
                  $image    = str_replace("data:image/jpeg;base64,","" , $pics['url'] ); 
                } 
                $to_image = strtotime(date("Y-m-d H:i:s")) .'.'.$ext ;
                file_put_contents( $destinationPath .  $to_image , (base64_decode( $image ))  );
                $posts['avatar'] =  $to_image;

            }
            if(isset($posts['password']))
            {
                if($posts['password'] !='') {
                    $posts['password'] = Hash::make($posts['password'] );
                    unset($posts['confirm_password']);
                } else {
                    unset($posts['password']);
                    unset($posts['confirm_password']);
                }  
            }             
            unset($posts['avatar_url']);
            unset($posts['id']);
            DB::table('tb_users')->where('id',$profile->id)->update($posts);

             return response()->json(['status'=>'success','message'=> 'Profile has been saved!'] );
        } else {
             return response()->json(['status'=>'error','message'=> 'The following errors occurred'] );
        }   
    
    }


    public function config( Request $request)
    {
        $config = config('sximo');
        $groups = \DB::table('groups')->get();
        $activation = [
          ['value'=>'auto' , 'text'  => 'Auto activation by system'],
          ['value'=>'email' , 'text'  => 'Verification by email'],
          ['value'=>'manual' , 'text'  => 'Manual activation by admin']
        ];
        return response()->json(['status'=>'success','data'=> $config , 'groups' => $groups , 'activation' => $activation ] );
    }
    public function cruds( Request $request ) {

        if($request->has('config')) {
            $cruds = \DB::table('tb_module')->where('module_name',$request->get('config'))->get();
            $r = $cruds[0];
            $data = [] ;
            $langs = (json_decode($r->module_lang,true));
            $data['id']     = $r->module_id; 
            $data['title']  = \SiteHelpers::infoLang($r->module_title,$langs,'title'); 
            $data['note']   = \SiteHelpers::infoLang($r->module_note,$langs,'note'); 
            $data['controller']   = $r->module_name ; 
            $data['table']  = $r->module_db; 
            $data['key']    = $r->module_db_key;
            $data['type']   = $r->module_type;
            $data['config'] = \SiteHelpers::CF_decode_json($r->module_config);
            $rows = $data ;
        }
        else {
            $cruds = \DB::table('tb_module')->where('module_type','!=','core')->get();    

            $rows = [] ;
            foreach($cruds as $r) {
                $data = [] ;
                $langs = (json_decode($r->module_lang,true));
                $data['id']     = $r->module_id; 
                $data['title']  = \SiteHelpers::infoLang($r->module_title,$langs,'title'); 
                $data['note']   = \SiteHelpers::infoLang($r->module_note,$langs,'note'); 
                $data['controller']   = $r->module_name ; 
                $data['table']  = $r->module_db; 
                $data['key']    = $r->module_db_key;
                $data['type']   = $r->module_type;
                $data['config'] = \SiteHelpers::CF_decode_json($r->module_config);
               
                $rows[] = $data ;
            }
        }
        return response()->json( array( 'status' => 'success' , 'data' => $rows )  );

    }
 
    public function notification( Request $request ) {
        $s = $request->get('s');
        if(!is_null($s))
        {
            $result = DB::table('tb_notification')->where('is_read','0')->get();
        } else {
            $result = DB::table('tb_notification')->orderby('created','desc')->get();   
             DB::table('tb_notification')->update(['is_read'=>'1']);   
        }
        
        return response()->json(['status'=>'success','data'=> $result ] );

    }   
}
