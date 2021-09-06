<?php

namespace App\Http\Controllers\api\v1\common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(){
        return Customer::all();
    }

    public function create(Request $c){
        $customer = new Customer();
        $customer->mobile = $c['cmobile'];
        $customer->name = $c['cname'];
        $customer->address = $c['caddress'];
        $customer->due = 0;
        $customer->save();
        
        return Customer::all();
        //$ret = ['0'=>$customer];
        //return $ret;

    }

    public function customerfrommobile($mobile){
        return Customer::where('mobile',$mobile)->first();
    }

    public function idfrommobile($mobile){
        $customer = Customer::where('mobile',$mobile)->first();
        if (is_null($customer)) {
            $customer = new Customer();
            $customer->mobile = $mobile;
            $customer->due = 0;
            $customer->save();
            return $customer;
        }
        return $customer;
    }

    public function getCustomerScore($cid){
        $customer = (int)$cid;
        $data['score'] =
                DB::table('orders')
                 ->select('status as statusno', DB::raw('count(id) as counter'),DB::raw('count(id) as counter'))
                 ->where('customer', '=', $customer)
                 ->groupBy('status')
                 ->orderBy('status')
                 ->get();
        $data['orders'] =
                DB::table('orders')
                ->select('id','order_no', 'net_payable','status','paid','date')
                ->where('customer', '=', $customer)
                ->orderBy('entry_at','desc')
                ->get();
        return $data;
    }

    public function updateCustomer(Request $c){
        Customer::where('id',$c['id'])
                ->update(['mobile'=>$c['mobile'],'name'=>$c['name'],'address'=>$c['address'],
                'deliveryaddress'=>$c['delivery'],'email'=>$c['email'],'update_by'=>Auth::user()->id]);
        
        return ['status'=>'ok','message'=>'Customer Updated Successfully'];
    }
}
