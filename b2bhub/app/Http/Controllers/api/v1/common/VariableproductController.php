<?php

namespace App\Http\Controllers\api\v1\common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;

class VariableproductController extends Controller
{
    public function getVariables($pid){
        $code = Product::where('id' , $pid)->first('code');
        //return $code;
        return Product::where('code', 'LIKE', $code->code.'%')
                        ->where('id','!=',$pid)->get();
    }
}
