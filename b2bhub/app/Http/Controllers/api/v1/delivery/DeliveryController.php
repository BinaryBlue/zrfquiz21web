<?php

namespace App\Http\Controllers\api\v1\delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use App\Models\Customer;
use App\Models\DeliveryChannel;
use App\Models\Order;
use App\Models\OrderTransfer;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Productarchive;
use App\Models\OrderLog;
use App\Models\Packaging;
use App\Models\Shipment;
use App\Models\ShipmentLog;
use App\User;
use Validator;


class DeliveryController extends Controller
{
    public function insertOrderLog($order_no,$event,$message){
        $log = new OrderLog();
        $log->order_no = $order_no;
        //$log->tracking_no = $tracking_no;
        $log->event = $event;
        $log->message = $message;
        $log->entry_by = Auth::user()->id;
        $log->save();
    }
    public function insertShipmentLog($order_no,$tracking_no,$event,$message){
        $log = new ShipmentLog();
        $log->order_no = $order_no;
        $log->tracking_no = $tracking_no;
        $log->event = $event;
        $log->message = $message;
        $log->entry_by = Auth::user()->id;
        $log->save();
    }

    public function getDeliveryChannel(){
        $r['channels'] = DeliveryChannel::all();
        $r['boys'] = User::where('group_id',8)->get();
        $r['allUsers'] = User::all();
        return $r;
    }

    private function makeShipment(Request $r){

        $ret = DB::transaction(function () use ($r) {

            $counter = Shipment::where([['order_no','=',$r['order_no']],['status','=','Initiated']])->count();
            if($counter > 0) return ['status'=>'error','message'=>'Multiple Active Shipment Is Not Possible. Please Mark As Returned/Completed of Previous Shipments.'];        

            $packet = Packaging::where('packet_no',$r['packet_no'])->first();

            $channel = DeliveryChannel::find($r['channel_id']);
            $shipment = new Shipment();
            $shipment->packet_no = $packet->packet_no;
            $shipment->order_no = $packet->order_no;
            $shipment->tracking_no = $this->getTrackingNo();
            $shipment->channel_id = $channel->id;
            $shipment->channel_name = $channel->name;
            $shipment->channel_uid = $channel->uid;
            $shipment->mobile = $packet->mobile;
            $shipment->delivery_boy_id = $packet->delivery_boy_id;
            $shipment->delivery_boy_name = $packet->delivery_boy_name;
            $shipment->remarks = $packet->remarks;
            $shipment->status = 'Initiated';
            $shipment->photo = $packet->photo;
            $shipment->save();

            DB::table('orders')
                    ->where('order_no',$r['order_no'])
                    ->update(['status'=>'Shipped','update_by'=>Auth::user()->id]);

            $this->insertOrderLog($r['order_no'],'Shipment Approved','Shipment '.$shipment->tracking_no.' Initiated By '.Auth::user()->username);
            $this->insertShipmentLog($r['order_no'],$shipment->tracking_no,'Initiated','Shipment '.$shipment->tracking_no.' Initiated By '.Auth::user()->username);
            return [
                'status'=>'success',
                'title'=>'Shipment Approved',
                "message" => "New Shipment Approved With Tracking No ".$shipment->tracking_no,
            ];
            
        }); 

        return $ret;
    }
    private function approveDelivered(Request $r){
        $ret = DB::transaction(function () use ($r) {
            $retVal = $this->makeShipment($r);
            DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['status'=>'Shipped','approval_waiting'=>'','update_by'=>Auth::user()->id]);
            return $retVal;
        });

        return $ret;
    }
    private function approveReturned(Request $r){
        // DB::table('packaging')
        //             ->where('packet_no',$r['packet_no'])
        //             ->update(['status'=>'Warehoused','update_by'=>Auth::user()->id]);
        // $this->insertOrderLog($r['order_no'],'Warehoused','Packet #'.$r['packet_no'].' given back to Warehouse Approved By '.Auth::user()->username);
        // return 'Packet #'.$r['packet_no'].' given back to Warehouse Approved By '.Auth::user()->username;
    }
    private function approveShipped(Request $r){
        $ret = DB::transaction(function () use ($r) {
            $retVal = $this->makeShipment($r);
            DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['status'=>'Shipped','approval_waiting'=>'','update_by'=>Auth::user()->id]);
            return $retVal;
        });

        return $ret;
        
    }

    private function approveWarehoused(Request $r){
        DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['status'=>'Warehoused','approval_waiting'=>'','update_by'=>Auth::user()->id]);
        $this->insertOrderLog($r['order_no'],'Warehoused','Packet #'.$r['packet_no'].' given back to Warehouse Approved By '.Auth::user()->username);
        return ['status'=>'success','message'=>'Packet #'.$r['packet_no'].' given back to Warehouse Approved By '.Auth::user()->username];
    }

    public function approveHandler(Request $r){
        $fn = 'approve'.$r['approve'];
        return $this->$fn($r);
    }

    public function getMyPackets(){
        
        if(Auth::user()->group_id < 4){ // For Delivery Manager (Sabuj)
            $statuses = ['Shipped','Discard'];
            return Packaging::whereNotIn('status',$statuses)
                            ->orderBy('entry_at', 'desc')
                            ->get();
        }
        else{ // For Delivery Boy
            $statuses = ['Warehoused','Picked'];
            return Packaging::
                            where('delivery_boy_id',Auth::user()->id)
                            ->whereIn('status',$statuses)
                            ->orderBy('entry_at', 'desc')
                            ->get();
        }
    }

    public function getMyShipments(){
        
        if(Auth::user()->group_id < 4){ // For Delivery Manager (Sabuj)
            $statuses = ['Initiated'];
            return Shipment::whereIn('status',$statuses)
                            ->orderBy('entry_at', 'desc')
                            ->get();
        }
        else{ // For Delivery Boy
            $statuses = ['Initiated'];
            return Shipment::
                            where('delivery_boy_id',Auth::user()->id)
                            ->whereIn('status',$statuses)
                            ->orderBy('entry_at', 'desc')
                            ->get();
        }
    }

    public function getTrackingNo(){
        $serial = 'SHP-'.date("Y-m-d");
        $shipments = Shipment::where('tracking_no','like', '%'.$serial.'%')->get();
        $new = $shipments->count() + 1;
        return $serial.'-'.$new;
    }

    public function shipmentCompleted(Request $r){
        DB::transaction(function () use ($r) {
            DB::table('shipments')
                    ->where('tracking_no',$r['tracking_no'])
                    ->update(['status'=>'Completed','update_by'=>Auth::user()->id]);
            $this->insertOrderLog($r['order_no'],'Shipment Completed','Shipment '.$r['tracking_no'].' Completed By '.Auth::user()->username);
            $this->insertShipmentLog($r['order_no'],$r['tracking_no'],'Completed','Shipment '.$r['tracking_no'].' Completed By '.Auth::user()->username);
            
            return response()->json([
                'status'=>'success',
                "message" => "Shipment Completed With Tracking No ".$r['tracking_no'],
            ]);
        });
    }

    public function shipmentRequested(Request $r){
        $ret = DB::transaction(function () use ($r) {
            DB::table('shipments')
                    ->where('tracking_no',$r['tracking_no'])
                    ->update(['approval_waiting'=>'Completed','update_by'=>Auth::user()->id]);
            $this->insertOrderLog($r['order_no'],'Delivery Requested','Shipment '.$r['tracking_no'].' Requested For Delivery By '.Auth::user()->username);
            $this->insertShipmentLog($r['order_no'],$r['tracking_no'],'Completed','Shipment '.$r['tracking_no'].' Completed By '.Auth::user()->username);
            
            return [
                'status'=>'success',
                "message" => "Shipment Delivered Requested For Tracking No ".$r['tracking_no'],
            ];
        });
        return $ret;
    }


}
