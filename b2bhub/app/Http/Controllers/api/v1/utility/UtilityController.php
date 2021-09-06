<?php

namespace App\Http\Controllers\api\v1\utility;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Productarchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UtilityController extends Controller
{
    public function getImage(Request $r){
        $data = Storage::url($r['path']);
        // $converted = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        return ['img'=>$data];
    }
    public function makedamage(Request $r){
        $code = $r['code'];
        $barcode = Productarchive::where('barcode',$code)->first();
        $ret = [];
        if($barcode == null){
            $ret['status'] = 'error';
            $ret['title'] = 'Barcode Unavailable';
            $ret['message'] = 'Barcode Not Found';
        }
        else{

            $barcode->status = 99;
            $barcode->save();
            $ret['status'] = 'success';
            $ret['title'] = 'Successfully Damaged';
            $ret['message'] = 'Barcode Marking As Damage Successfull.';
        }
        return $ret;

    }
    public function makesellable(Request $r){
        $id = $r['outlet_id'];
        $code = $r['code'];
        $barcode = Productarchive::where('barcode',$code)->first();
        $ret = [];
        if($barcode == null){
            $ret['status'] = 'error';
            $ret['title'] = 'Barcode Unavailable';
            $ret['message'] = 'Barcode Not Found';
        }
        else{
            $outlet = Outlet::find($id);
            $barcode->outlet_id = $id;
            $barcode->outlet_name = $outlet->name;
            $barcode->status = 3;
            $barcode->save();
            $ret['status'] = 'success';
            $ret['title'] = 'Successful';
            $ret['message'] = 'Barcode Reset Successfull.';
        }
        return $ret;

    }
}
