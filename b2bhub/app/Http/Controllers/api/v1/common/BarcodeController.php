<?php

namespace App\Http\Controllers\api\v1\common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Productarchive;

class BarcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBarcodesofProduct($pid){
        return Productarchive::where(['product_id' => $pid, 'status' => 1])->get();
    }
    public function getBarcodeDetails($barcode)
    {
        return Productarchive::where(['barcode' => $barcode, 'status' => 1])->get();
    }

    public function barcodeofoutlet($outlet,$barcode,$status){
        return Productarchive::where(['barcode' => $barcode, 'status' => $status,'outlet_id'=>$outlet])->get();
    }

    public function getavailableOldbarcode(Request $request){
        $skip = (int)$request['scount'];
        $ptype = (int)$request['ptype'];
        $oid = (int) $request['outlet_id'];
        if($oid==0){
            if($ptype==0) return Productarchive::where(['code'=>$request['barcode'],'status'=>1 ])->skip($skip)->first();
            else return Productarchive::where(['barcode'=>$request['barcode'],'status'=>1 ])->first();
        }
        else{
            if($ptype==0) return Productarchive::where(['code'=>$request['barcode'],'outlet_id'=>$request['outlet_id'],'status'=>3 ])->skip($skip)->first();
            else return Productarchive::where(['barcode'=>$request['barcode'],'outlet_id'=>$request['outlet_id'],'status'=>3 ])->first();
            
        }
        
    }



    public function getArchive(Request $request)
    {
        return Productarchive::where(['barcode'=>$request['barcode'],'status'=>4 ])->first();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
