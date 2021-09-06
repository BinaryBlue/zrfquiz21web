<?php

namespace App\Http\Controllers\api\v1\common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\Outlet;

class OutletController extends Controller
{
    public function index(Request $request)
    {
        $gid = Auth::user()->group_id;
        $id = Auth::user()->id;
        $oid = Auth::user()->outlet;
        if($gid < 3){
            return Outlet::all();
        }
        else{
            //return Auth::user();
            return Outlet::where('id', $oid)->get();
        }
    }

    public function all(){
        return Outlet::all();
    }

    
}
