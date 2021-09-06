<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Support\Facades\Hash;

//use App\User;

class LoginController extends Controller
{
    public function login(Request $request){
        //User::create(request(['Jasim Uddin', 'jasimmailid@gmail.com', '98899889aA']));
        // $user = \App\User::create([
        //     'name' => 'Jasim Uddin',
        //     'email' => 'jasimmailid@gmail.com',
        //     'password' => Hash::make('98899889aA'),
        // ]);

        $login = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if(!Auth::attempt($login)){
            return response([
                'message'=>'Invalid Credentials!'
            ]);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;
        
        return response(['user'=>Auth::user(),'access_tokken'=>$accessToken]);
    }

    public function firebaselogin (Request $request){
        //dd($request);
        // Launch Firebase Auth
        $auth = app('firebase.auth');
        // Retrieve the Firebase credential's token
        $idTokenString = $request['Firebasetoken'];

        
        try { // Try to verify the Firebase credential token with Google
            
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
            
        } catch (\InvalidArgumentException $e) { // If the token has the wrong format
            
            return response()->json([
                'message' => 'Unauthorized - Can\'t parse the token: ' . $e->getMessage()
            ], 401);        
            
        } catch (InvalidToken $e) { // If the token is invalid (expired ...)
            
            return response()->json([
                'message' => 'Unauthorized - Token is invalide: ' . $e->getMessage()
            ], 401);
            
        }

        // Retrieve the UID (User ID) from the verified Firebase credential's token
        $uid = $verifiedIdToken->getClaim('sub');
        // Retrieve the user model linked with the Firebase UID
        
        $user = User::where('uid',$uid)->first(); // B2BHub user
        $firebaseuser = $auth->getUser($uid);
        //dd($user);
        if($user!=null){ // UID exists
            // Sync existing user
            $cuser = User::find($user->id);
            if($cuser->email=='') $cuser->email = $firebaseuser->email;
            if($cuser->phoneNumber=='') $cuser->phoneNumber = $firebaseuser->phoneNumber;
            if($cuser->first_name=='') $cuser->first_name = $firebaseuser->displayName;
            $cuser->password = Hash::make('12345678');
            $cuser->save();
        }
        else{
            $user = new User();
            $user->uid = $uid;
            $user->email = $firebaseuser->email;
            $user->group_id = 3;
            $user->password = Hash::make('12345678');
            $user->phoneNumber = $firebaseuser->phoneNumber;
            $user->first_name = $firebaseuser->displayName;
            $user->save();
        }

        $accessToken = $user->createToken('authToken')->accessToken;
        
        return response(['user'=>$user,'access_tokken'=>$accessToken]);
    }

    public function userList(){
        return DB::table('tb_users')->select('id', 'email')->where('group_id','>',2)->get();
    }

    public function assigntooutlet(Request $r){
        DB::table('tb_users')
              ->where('id', $r['uid'])
              ->update(['outlet' => $r['oid']]);
        return(['status'=>'ok','message'=>'success']);
    }
}
