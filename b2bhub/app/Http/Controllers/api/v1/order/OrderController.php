<?php

namespace App\Http\Controllers\api\v1\order;

use App\Events\StockIsZeroEvent;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
 
use Carbon\Carbon;

use App\Models\Order;
use App\Models\OrderTransfer;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Productarchive;
use App\Models\OrderLog;
use App\Models\Packaging;
use App\Models\Product;
use App\Models\Shipment;
use App\User;
use PDF;

class OrderController extends Controller
{

    public function makePending(Request $r){
        DB::transaction(function () use ($r) {
            Order::where('order_no',$r['order_no'])->update(['status'=>'Pending','update_by'=>Auth::user()->id]);
        });
    }
    public function makeConfirmed(Request $r){
        return DB::transaction(function () use ($r) {
            Order::where('order_no',$r['order_no'])
                    ->update(['status'=>'Confirmed','update_by'=>Auth::user()->id]);
        
            //$this->insertOrderLog($r['order_no'],'Confirmed','Order Status Changed By '.Auth::user()->username);

            $order = Order::where('order_no',$r['order_no'])->first();

            $ret = 'Sell Successfully Confirmed Without Sending SMS';
            if($r['sms']=='yes'){
                $ret = Http::asForm()->post('http://api.greenweb.com.bd/api.php', [
                    'token' => '23ad51465250a1351b745f9e46a8b744',
                    'to' => '+88'.$order->mobile,
                    'message' => '"স্বপ্নদ্বীপে স্বাগতম"   আপনার অর্ডার '.$order->order_no.' নিশ্চিত করা হয়েছে। বিলঃ '.$order->net_payable.'/-, পরিশোধিতঃ '.$order->paid.'/-, বকেয়াঃ '.($order->net_payable - $order->paid).'/-। স্বপ্নদ্বীপ এর সাথে থাকার জন্য ধন্যবাদ।'
                ]);
            }
            return $ret;
            // return  Http::post('http://api.greenweb.com.bd/api.php', [
            //     'token' => '23ad51465250a1351b745f9e46a8b744',
            //     'to' => '+8801791767175',
            //     'message' => 'আপনার অর্ডার '.$order->order_no.' নিশ্চিত করা হয়েছে। বিলঃ '.$order->net_payable.'/-, পরিশোধিতঃ '.$order->paid.'/-, বকেয়াঃ '.($order->net_payable - $order->paid).'/-। দিগন্তের সাথে থাকার জন্য ধন্যবাদ।'
            // ]);
        });
    }

    public function makeCompleted(Request $r){
        $ret = DB::transaction(function () use ($r) { 
            $order = Order::where('order_no',$r['order_no'])->first();
            $status = 'ok';
            foreach ($order->items as $index => $item) {
                $stock = Product::where('id',$item['pid'])->pluck('stock')[0];
                if($item['qtt'] > $stock) $status = 'oops';
            }
            if($status=='ok'){
               Order::where('order_no',$r['order_no'])
                ->update(['status'=>'Completed','update_by'=>Auth::user()->id]);

                foreach ($order->items as $index => $item) {
                    Product::where(['id' => $item['pid']])
                        ->decrement('stock',$item['qtt']);
                }

                return [
                    'status'=>'success',
                    'title'=>'Sell Completed',
                    "message" => 'Sell Completed Successful By '.Auth::user()->username,
                ]; 
            }
            else{
                return [
                    'status'=>'error',
                    'title'=>'Out Of Stock',
                    "message" => 'Please Adjust Stock First',
                ];
            }
                // Order::where('order_no',$r['order_no'])
                // ->update(['status'=>'Completed','update_by'=>Auth::user()->id]);

                // return [
                //     'status'=>'success',
                //     'title'=>'Order Completed',
                //     "message" => 'Order Completed Successful By '.Auth::user()->username,
                // ];
            //}

        });
        return $ret;
    }

    public function makeCanceled(Request $r){
        $ret = DB::transaction(function () use ($r) { 
                Order::where('order_no',$r['order_no'])
                ->update(['status'=>'Canceled','update_by'=>Auth::user()->id]);

                return [
                    'status'=>'success',
                    'title'=>'Order Canceled',
                    "message" => 'Order Cancel Successful By '.Auth::user()->username,
                ];
            //}

        });
        return $ret;
    }
    
    public function getOrder($order){
        $ret['order_info'] = Order::where('order_no','=', $order)->orderBy('entry_at','desc')->get();
        //$ret['transfer_info'] = OrderTransfer::where('order_no','=', $order)->orderBy('entry_at','desc')->get();
        $ret['payment_info'] = Payment::where('order_no','=', $order)->orderBy('entry_at','desc')->get();
        $ret['customer_info'] = Customer::where('id','=', $ret['order_info'][0]->customer)->get();
        $ret['log_info'] = OrderLog::where('order_no','=', $order)->orderBy('entry_at', 'desc')->get();
        //$ret['packet_info'] = Packaging::where('order_no','=', $order)->orderBy('entry_at', 'desc')->get();
        //$ret['shipment_info'] = Shipment::where('order_no','=', $order)->orderBy('entry_at', 'desc')->get();
        $ret['expense_info'] = Expense::where('receipt_no','=', $order)->orderBy('entry_at', 'desc')->get();
        return $ret;
    }

    public function getAllOrdersOfLastMonth(){
        return Order::where([['status','!=','Completed']])->orderBy('entry_at', 'desc')->get();
    }
    public function init($outlet,$customer){
        
        //Customer::
        $c = (int) $customer;
        $order_no = $this->getOrderNo($outlet); // Collect New Order No.
        $order = new Order();
        $order->fy = date("Y");
        $order->date = date("Y-m-d");
        $order->outlet = (int)$outlet;
        $order->customer = (int)$c;
        $order->mobile = Customer::find($c)->mobile;
        $order->order_no = $order_no;
        $order->status = 'Pending';
        $order->entry_by = Auth::user()->id;
        $order->save(); // Order Entry
        ////
        $ret['id'] = $order->id;
        $ret['order_no'] = $order->order_no;

        //Order Log
        //$this->insertOrderLog($order->order_no,'Created','New Order #'.$order->order_no.' Created By '.Auth::user()->username);
        return $ret;
        //$qrcode = new Generator;
        //$qrcode->size(500)->generate('Make a qrcode without Laravel!');
        ////

    }

    public function getOrderNo($outlet){
        $today = date("Y-m-d");
        $test_inv = 'INV-'.$today;
        $statements = Order::where('order_no','like', '%'.$test_inv.'%')->get();
        $new = $statements->count() + 1;
        return $test_inv.'-'.$new;
    }

    // public function deleteitem(Request $r){
    //     $this->insertOrderLog($r['orderno'],'Item Removed','Item #'.$r['code'].' Removed By '.Auth::user()->username);
    //     return ['status'=>'ok','message'=>'Item Deleted Successfully'];
    // }

    public function itemupdate(Request $r){
    DB::transaction(function () use ($r) {
        Order::where('order_no',$r['order_no'])
                    ->update(['items'=>$r['itemList'],'sub_total'=>$r['subtotal'],'discount'=>$r['discamount'],
                    'vat'=>$r['vatamount'],'delivery_fee'=>$r['deliveryfee'],'net_payable'=>$r['netpayable'],
                    'remarks'=>$r['orderremarks'],'update_by'=>Auth::user()->id]);
        if($r['newItem']!=''){
            $this->insertOrderLog($r['order_no'],'Product Added','Product '.$r['newItem'].' added By '.Auth::user()->username);
        }
        
    });

    return ['status'=>'ok','message'=>'Order Updated Successfully'];
    }

    public function updateProductStock($code){
        $stock = (int)Productarchive::where(['code'=>$code,'status'=>3])->count();
        DB::table('products')
                    ->where('code',$code)
                    ->update(['current_stock'=>$stock]);
        return $stock;

    }



    public function getOutletOrders($outlet,$fromDate,$toDate){
        $id = (int)$outlet;
        
        if(Auth::user()->group_id < 3){ // For Top management +
            return 
            Order::whereBetween('date', [$fromDate, $toDate])
            ->orderBy('entry_at','desc')
            ->get();
        }
        else{ // For Individual
            return Order::where('outlet',$id)
            ->whereBetween('date', [$fromDate, $toDate])
            ->orderBy('entry_at','desc')
            ->get();
        }
    }

    public function getDueOrders($outlet,$fromDate,$toDate){
        $id = (int)$outlet;
        return 
                Order::where('outlet',$id)
                ->whereRaw('orders.paid != orders.net_payable')
                ->whereBetween('date', [$fromDate, $toDate])
                ->orderBy('entry_at','desc')
                ->get();
    }

    public function printThreeInchOrder($order){
        $data['order'] = Order::where('order_no','=', $order)->first();
        $data['payment'] = Payment::where('order_no','=', $order)->first();
        $data['customer'] = Customer::where('id','=', $data['order']->customer)->first();
        $data['outlet'] = Outlet::where('id','=',$data['order']->outlet)->first();
        $data['creator'] = User::where('id','=',$data['order']->entry_by)->first();

        $pdf = PDF::loadView('order3inch', $data,[],[
            'format' => 'A4',
            //'default_font' => 'blueerp',
            'mode' => 'utf-8',
            'margin_left' => '2',
            'margin_right' => '2',
            'margin_top' => '2',
            'margin_bottom' => '2'

          ]);
        return $pdf->stream($data['order']->order_no.'-sell-invoice.pdf');
    }

    public function printA4Order($order){
        $data['order'] = Order::where('order_no','=', $order)->first();
        $data['payment'] = Payment::where('order_no','=', $order)->get();
        $data['customer'] = Customer::where('id','=', $data['order']->customer)->first();
        //$data['outlet'] = Outlet::where('id','=',$data['order']->outlet)->first();
        $data['creator'] = User::where('id','=',$data['order']->entry_by)->first();


        $result = Builder::create()
                    ->writer(new PngWriter())
                    ->writerOptions([])
                    ->data($order)
                    ->encoding(new Encoding('UTF-8'))
                    ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                    ->size(150)
                    ->margin(10)
                    ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                    ->build();
                    header('Content-Type: '.$result->getMimeType());
        $result->saveToFile('order_doc/'.$order.'.png');
        
        $pdf = PDF::loadView('ordera4', $data,[],[
            'format' => 'A4',
            //'default_font' => 'blueerp',
            'mode' => 'utf-8',
            'margin_left' => '10',
            'margin_right' => '10',
            'margin_top' => '10',
            'margin_bottom' => '10'
          ]);
//           $pdf->SetHTMLFooter('
// <table width="100%">
//     <tr>
//         <td width="33%">{DATE j-m-Y}</td>
//         <td width="33%" align="center">{PAGENO}/{nbpg}</td>
//         <td width="33%" style="text-align: right;">My document</td>
//     </tr>
// </table>');
        return $pdf->stream($data['order']->order_no.'-invoice.pdf');
                    
    }
    


}
