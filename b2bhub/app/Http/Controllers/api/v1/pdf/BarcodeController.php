<?php

namespace App\Http\Controllers\api\v1\pdf;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use PDF;

use App\Models\Stockentry;
use App\Models\Productarchive;

class BarcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function barcodes($statement)
    {
        //$barcodes = Productarchive::where('entry_statement',$statement)->get();
        $bcode = Stockentry::find($statement)->statement;
        //var_dump($barcodes);
        $barcodes = Productarchive::where('entry_statement',$bcode)->get();
        //dd($barcodes);
        $data = [ 'barcodes' => $barcodes  ];
        $pdf = PDF::loadView('barcodes', $data,[],[
            'format' => 'A4',
            'margin_left' => '2',
            'margin_right' => '2',
            'margin_top' => '2',
            'margin_bottom' => '2'
          ]);
        return $pdf->stream($bcode.'-barcodes.pdf');
        //return view('barcodes',$data);
    }

    public function getBarcodeNumber($product_code)
    {
        $serial = 1 + Productarchive::where('code',$product_code)->count();
        return $product_code.'-'.$serial;
    }
    
    public function index()
    {
        //
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
