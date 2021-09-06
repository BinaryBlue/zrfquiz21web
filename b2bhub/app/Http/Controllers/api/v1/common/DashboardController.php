<?php

namespace App\Http\Controllers\api\v1\common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Outlet;

class DashboardController extends Controller
{
    public function initdata()
    {
        if(Auth::user()->group_id<3) return $this->admindata();
        $oid = Auth::user()->outlet;

        $r = DB::table('stock_transfer')
                        ->select(DB::raw('COUNT(transfer_code) as total'))
                        ->where([
                                    ['transfer_to','=',$oid],
                                    ['status','=','0']
                                ])->get();
        $ret['pending_products'] = $r[0]->total;

        $r = DB::table('stock')
                        ->select(DB::raw('SUM(quantity) as current_stock'))
                        ->where('outlet',$oid)->get();
        $ret['current_stock'] = $r[0]->current_stock;

        $r = DB::table('stock')
                        ->select(DB::raw('COUNT(product) as total'))
                        ->where([
                                    ['outlet', '=', $oid],
                                    ['quantity', '>', 0],
                                ])
                        ->get();
        $ret['available_products'] = $r[0]->total;
        
        $r = DB::table('sell')
                        ->select(DB::raw('COUNT(invoice_no) as total'))
                        ->where([
                                    ['outlet', '=', $oid],
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_sell'] = $r[0]->total;

        $r = DB::table('sell')
                        ->select(DB::raw('SUM(payable) as total'))
                        ->where([
                                    ['outlet', '=', $oid],
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_sell_amount'] = $r[0]->total;

        $r = DB::table('sell')
                        ->select(DB::raw('SUM(JSON_LENGTH(items)) as total'))
                        ->where([
                                    ['outlet', '=', $oid],
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_sell_qtt'] = $r[0]->total;

        $r = DB::table('product_returns')
                        ->select(DB::raw('COUNT(receipt_no) as total'))
                        ->where([
                                    ['outlet', '=', $oid],
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_return'] = $r[0]->total;

        $r = DB::table('product_returns')
                        ->select(DB::raw('SUM(JSON_LENGTH(items)) as total'))
                        ->where([
                                    ['outlet', '=', $oid],
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_return_qtt'] = $r[0]->total;

        $r = DB::table('product_returns')
                        ->select(DB::raw('SUM(paid) as total'))
                        ->where([
                                    ['outlet', '=', $oid],
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_return_amount'] = $r[0]->total;

        $r = DB::table('balance')
                        ->select(DB::raw('*'))
                        ->where([
                                    ['outlet', '=', $oid],
                                ])
                        ->get();
        $ret['balance'] = $r;

        $r = DB::table('sell')
                        ->select(DB::raw('*'))
                        ->where([
                                    ['date', '=', date("Y-m-d")],
                                    ['outlet', '=', $oid],
                                ])
                        ->get();
        $ret['sell_summery'] = $r;

        $r = DB::table('product_returns')
                        ->select(DB::raw('*'))
                        ->where([
                                    ['date', '=', date("Y-m-d")],
                                    ['outlet', '=', $oid],
                                ])
                        ->get();
        $ret['return_summery'] = $r;




        return $ret;
    }

    public function admindata(){
        $r = DB::table('stock')
                        ->select(DB::raw('SUM(quantity) as current_stock'))->get();
        $ret['current_stock'] = $r[0]->current_stock;

        $r = DB::table('stock')
                        ->select(DB::raw('COUNT(product) as total'))
                        ->where([
                                    ['quantity', '>', 0],
                                ])
                        ->get();
        $ret['available_products'] = $r[0]->total;
        
        $r = DB::table('sell')
                        ->select(DB::raw('COUNT(invoice_no) as total'))
                        ->where([
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_sell'] = $r[0]->total;

        $r = DB::table('sell')
                        ->select(DB::raw('SUM(payable) as total'))
                        ->where([
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_sell_amount'] = $r[0]->total;

        $r = DB::table('product_returns')
                        ->select(DB::raw('COUNT(receipt_no) as total'))
                        ->where([
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_return'] = $r[0]->total;

        $r = DB::table('product_returns')
                        ->select(DB::raw('SUM(paid) as total'))
                        ->where([
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_return_amount'] = $r[0]->total;

        $r = DB::table('balance')->select(DB::raw('SUM(amount) as amount, balance_method'))->groupByRaw('balance_method')->get();
        $ret['balance'] = $r;

        $r = DB::table('sell')
                        ->select(DB::raw('SUM(JSON_LENGTH(items)) as total'))
                        ->where([
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_sell_qtt'] = $r[0]->total;

        $r = DB::table('product_returns')
                        ->select(DB::raw('SUM(JSON_LENGTH(items)) as total'))
                        ->where([
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_return_qtt'] = $r[0]->total;

        $r = DB::table('product_returns')
                        ->select(DB::raw('SUM(JSON_LENGTH(items)) as total'))
                        ->where([
                                    ['date', '=', date("Y-m-d")],
                                ])
                        ->get();
        $ret['todays_return_qtt'] = $r[0]->total;

        $r = DB::table('profits')
                        ->select(DB::raw('SUM(amount) as total'))
                        ->get();
        $ret['todays_profit'] = $r[0]->total;


        //Outlet wise sell calculate
        $outlets = DB::table('outlets')->select(DB::raw('*'))->get();
        $outlet_wise_sell = [];
        foreach ($outlets as $key => $outlet) {
            $outlet_wise_sell[$key]['id'] = $outlet->id;
            $outlet_wise_sell[$key]['name'] = $outlet->name;
            $r = DB::table('sell')
                        ->select(DB::raw('COUNT(invoice_no) as qtt, SUM(JSON_LENGTH(items)) as items, SUM(payable) as amount'))
                        ->where([
                                    ['date', '=', date("Y-m-d")],
                                    ['outlet', '=', $outlet->id]
                                ])  
                        ->get();
            $outlet_wise_sell[$key]['qtt'] = $r[0]->qtt;
            $outlet_wise_sell[$key]['items'] = $r[0]->items;
            $outlet_wise_sell[$key]['amount'] = $r[0]->amount;
        }

        $ret['outlet_wise_sell'] = $outlet_wise_sell;

        



        return $ret;
    }
}
