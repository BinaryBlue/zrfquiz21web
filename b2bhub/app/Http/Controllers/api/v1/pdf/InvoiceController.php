<?php

namespace App\Http\Controllers\api\v1\pdf;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use PDF;

use App\Models\Sell as Modelsell;
use App\Models\Customer;

class InvoiceController extends Controller
{
    public function sellinvoiceThreeinch($id){
        $sell_data = Modelsell::find($id);
        $customer = Customer::find($sell_data->customer);
        $data = [ 'sell_data' => $sell_data->screen_data,'customer'=>$customer ];
        $pdf = PDF::loadView('invoice3inch', $data,[],[
            'format' => 'A4',
            //'default_font' => 'bangla',
            'mode' => 'utf-8',
            'margin_left' => '2',
            'margin_right' => '2',
            'margin_top' => '2',
            'margin_bottom' => '2'
          ]);
        return $pdf->stream($sell_data['invoiceno'].'-sell-invoice.pdf');
    }
}
