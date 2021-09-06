<?php

namespace App\Http\Controllers\api\v1\packaging;

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

class PackagingController extends Controller
{
    public function getPacketNo(){
        $serial = 'PKT-'.date("Y-m-d");
        $packets = Packaging::where('packet_no','like', '%'.$serial.'%')->get();
        $new = $packets->count() + 1;
        return $serial.'-'.$new;
    }
    public function __insertRemarks(Request $r){
        $pkt = Packaging::where('packet_no',$r['packet_no'])->first();
        $eRemarks = $pkt->remarks;
        $rem['when'] = date('Y-m-d-h:i:s');
        $rem['what'] = $r['remarks'];
        array_unshift($eRemarks,$rem);

        DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['remarks'=>$eRemarks,'update_by'=>Auth::user()->id]);
        return ['status'=>'success','title'=>'Remarks Added','message'=>'Remarks Added Successfully'];
    }
    public function insertOrderLog($order_no,$event,$message){
        $log = new OrderLog();
        $log->order_no = $order_no;
        //$log->tracking_no = $tracking_no;
        $log->event = $event;
        $log->message = $message;
        $log->entry_by = Auth::user()->id;
        $log->save();
    }
    private function _updatePacket(Request $r){
        $rules = ['file'  => 'mimes:png,jpg,jpeg|max:2048'];
        $validator = Validator::make($r->all(),$rules);
        if ($validator->fails()) return ['status'=>'error','message'=>'Please Upload Image File (PNG,JPG,JPEG)','error'=>$validator->errors()];
        
        $file = '';
        if ($files = $r->file('file')) {
            $file = $r->file->store('public/shipment_doc/'.date("Y").'/'.date("M").'/'.date("d"));
        }
        $pkt = Packaging::where('packet_no',$r['packet_no'])->first();
        $eRemarks = $pkt->remarks;
        $rem['when'] = date('Y-m-d-h:i:s');
        $rem['what'] = $r['remarks'];
        array_unshift($eRemarks,$rem);

        DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['remarks'=>$eRemarks,'photo'=>$file,'update_by'=>Auth::user()->id]);
        return ['status'=>'success'];
    }
    private function setReturned(Request $r){
        $ret = $this->_updatePacket($r);
        DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['approval_waiting'=>'Returned','update_by'=>Auth::user()->id]);
        $this->insertOrderLog($r['order_no'],'Returned','Packet #'.$r['packet_no'].' Delivered. Requested By '.Auth::user()->username);
        $ret['title'] = 'Returned Requested';
        $ret['msg'] = 'Packet #'.$r['packet_no'].' Returned Requested For Approval. Requested By '.Auth::user()->username;
        return $ret;
    }

    private function setDelivered(Request $r){
        //$ret = $this->_updatePacket($r);
        DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['approval_waiting'=>'Delivered','update_by'=>Auth::user()->id]);
        $this->insertOrderLog($r['order_no'],'Delivered Requested','Packet #'.$r['packet_no'].' Delivered. Requested By '.Auth::user()->username);
        $ret['title'] = 'Delivered Requested';
        $ret['msg'] = 'Packet #'.$r['packet_no'].' Delivered Requested For Approval. Requested By '.Auth::user()->username;
        return $ret;
    }
    private function setShipped(Request $r){
        $ret = $this->_updatePacket($r);
        DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['approval_waiting'=>'Shipped','update_by'=>Auth::user()->id]);
        $this->insertOrderLog($r['order_no'],'Shipment Requested','Packet #'.$r['packet_no'].' Shipped. Requested By '.Auth::user()->username);
        $ret['title'] = 'Shippment Requested';
        $ret['msg'] = 'Packet #'.$r['packet_no'].' Shipped Requested For Approval. Requested By '.Auth::user()->username;
        return $ret;
    }

    private function setWarehoused(Request $r){
        DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['approval_waiting'=>'Warehoused','update_by'=>Auth::user()->id]);
        $this->insertOrderLog($r['order_no'],'Warehoused','Packet #'.$r['packet_no'].' given back to Warehouse Requested By '.Auth::user()->username);
        return 'Packet #'.$r['packet_no'].' given back to Warehouse Requested By '.Auth::user()->username;
    }
    private function setPicked(Request $r){
        DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['status'=>'Picked','approval_waiting'=>'','update_by'=>Auth::user()->id]);
        $this->insertOrderLog($r['order_no'],'Picked','Packet #'.$r['packet_no'].' Picked By '.Auth::user()->username);
        return 'Packet #'.$r['packet_no'].' Picked By '.Auth::user()->username;
    }
    public function actionHandler(Request $r){
        $fn = 'set'.$r['action'];
        return $this->$fn($r);
    }
    public function assignBoyToPacket(Request $r){
        $ret = DB::transaction(function () use ($r) {
            $packet = Packaging::find($r['id']);
            $packet->delivery_boy_id = $r['boy_id'];
            $packet->delivery_boy_name = $r['boy_name'];
            $packet->save();
            $this->insertOrderLog($r['order_no'],'Boy Assigned','Packet #'.$packet->packet_no.' assigned For '.$r['boy_name'].' By '.Auth::user()->username);
            return [
                'status'=>'success',
                "message" => 'Product Packaged '.$packet->packet_no.' For '.$r['boy_name'],
            ];
        });
        return $ret;
    }
    public function makePacket(Request $r){
        $ret = DB::transaction(function () use ($r) {

            $counter = Packaging::where([['order_no','=',$r['order_no']],['status','=','Initiated']])->count();
            if($counter > 0) return response()->json(['status'=>'error','message'=>'Packaging Already Initiated. Please Discard Previous Packet']);        

            $channel = DeliveryChannel::find($r['channel_id']);
            $packet = new Packaging();
            $packet->order_no = $r['order_no'];
            $packet->packet_no = $this->getPacketNo();
            $packet->channel_id = $channel->id;
            $packet->channel_name = $channel->name;
            //$packet->delivery_boy_id = $r['delivery_boy_id'];
            //$packet->delivery_boy_name = $r['delivery_boy_name'];
            $packet->status = 'Warehoused';
            $packet->approval_waiting = '';
            $packet->remarks = array();
            $packet->photo = '';
            $packet->mobile = $r['mobile'];
            $packet->save();

            DB::table('orders')
                    ->where('order_no',$r['order_no'])
                    ->update(['status'=>'Packaged','update_by'=>Auth::user()->id]);
        
            // $this->insertOrderLog($r['order_no'],'Packaged','Packet #'.$packet->packet_no.' assigned For '.$r['delivery_boy_name'].' By '.Auth::user()->username);
            // return response()->json([
            //     'status'=>'success',
            //     "message" => 'Product Packaged '.$packet->packet_no.' For '.$r['delivery_boy_name'],
            // ]);
            $this->insertOrderLog($r['order_no'],'Packaged','Packet #'.$packet->packet_no.' Created By '.Auth::user()->username);
            return response()->json([
                'status'=>'success',
                "message" => 'Product Packaged With Packet No. #'.$packet->packet_no,
            ]);
            
        }); 

        return $ret;
    }

    public function discardPacket(Request $r){
        DB::transaction(function () use ($r) {
            DB::table('packaging')
                    ->where('packet_no',$r['packet_no'])
                    ->update(['status'=>'Discard','update_by'=>Auth::user()->id]);
            $this->insertOrderLog($r['order_no'],'Packet Discarded','Packet '.$r['packet_no'].' Discarded By '.Auth::user()->username);
                    
            return response()->json([
                'status'=>'success',
                "message" => "Packet ".$r['packet_no'].'Discarded',
            ]);
        });
    }
}
