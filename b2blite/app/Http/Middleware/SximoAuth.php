<?php

namespace App\Http\Middleware;

use Closure;

class SximoAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( !$request->header('Authorization')){
            return response()->json(['message' => 'Not a valid API request.'],401);
        }

        $token = $request->header('Authorization');
        $token = str_replace('Bearer ','', $token);
        $user = \DB::table('tb_token')
                ->leftJoin('tb_users', 'tb_users.id', '=', 'tb_token.userId')
                ->where('token', trim($token) )
                ->get();
        if(count($user) <= 0) {
            return response()->json(['message' => ' Un Authenticated.'],401);
        }
        
        $request->attributes->add(['profile' => $user[0] ]);
        return $next($request);
        
    }
}
