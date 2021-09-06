<?php

namespace App\Http\Controllers\api\v1\common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Outlet;

class ReportController extends Controller
{
   public function stockReport(Request $request){
        $code = $request['code'];
        $ret = [];
        //return $request;
        $products = DB::table('products')
                    ->where('code', 'like', $code.'%')
                    ->get();
        foreach ($products  as $key=>$product) {
            $ret['summery'][$key]['id'] = $product->id;
            $ret['summery'][$key]['code'] = $product->code;
            $ret['summery'][$key]['name'] = $product->name;
            $ret['summery'][$key]['price'] = $product->selling_price;
            $ret['summery'][$key]['stock_report'] = $this->stockofSingleProduct($product->id);
            $ret['summery'][$key]['live_stock_report'] = $this->liveStock($product->id);
        }
        $oid = Auth::user()->outlet;
        if($oid==0){
            $ret['barcodes'] = DB::table('product_archive')
            ->where([
                        ['barcode', 'like', $code.'%'],
                        ['status', '=', 3],
                    ])
            ->orderBy('outlet_id', 'asc')
            ->get();
        }
        else{
            $ret['barcodes'] = DB::table('product_archive')
            ->where([
                        ['barcode', 'like', $code.'%'],
                        ['outlet_id', '=', $oid],
                        ['status', '=', 3],
                    ])
            ->orderBy('outlet_id', 'asc')
            ->get();
        }
        
        return $ret;
   }
   public function liveStock($pid){
    $r = DB::table('product_archive')
        ->select(DB::raw('COUNT(id) as metro_stock'))
        ->where([['outlet_id','=',2],['product_id','=',$pid],['status', '=', 3]])->get();
    $ret['metro_stock'] = (int)$r[0]->metro_stock;

    $r = DB::table('product_archive')
        ->select(DB::raw('COUNT(id) as savar_stock'))
        ->where([['outlet_id','=',3],['product_id','=',$pid],['status', '=', 3]])->get();
    $ret['savar_stock'] = (int)$r[0]->savar_stock;

    $r = DB::table('product_archive')
        ->select(DB::raw('COUNT(id) as stock'))
        ->where([['outlet_id','=',4],['product_id','=',$pid],['status', '=', 3]])->get();
    $ret['orchid_stock'] = (int)$r[0]->stock;

    $r = DB::table('product_archive')
        ->select(DB::raw('COUNT(id) as stock'))
        ->where([['outlet_id','=',1],['product_id','=',$pid],['status', '=', 3]])->get();
    $ret['office_stock'] = (int)$r[0]->stock;        

    $r = DB::table('product_archive')
        ->select(DB::raw('COUNT(id) as stock'))
        ->where([['outlet_id','=',5],['product_id','=',$pid],['status', '=', 3]])->get();
    $ret['ecom_stock'] = (int)$r[0]->stock;

    $r = DB::table('product_archive')
        ->select(DB::raw('COUNT(id) as stock'))
        ->where([['outlet_id','=',6],['product_id','=',$pid],['status', '=', 3]])->get();
    $ret['fcom_stock'] = (int)$r[0]->stock;

    $r = DB::table('product_archive')
        ->select(DB::raw('COUNT(id) as stock'))
        ->where([['outlet_id','=',7],['product_id','=',$pid],['status', '=', 3]])->get();
    $ret['daraz_stock'] = (int)$r[0]->stock;
    
    $r = DB::table('product_archive')
        ->select(DB::raw('COUNT(id) as stock'))
        ->where([['outlet_id','=',8],['product_id','=',$pid],['status', '=', 3]])->get();
    $ret['north_stock'] = (int)$r[0]->stock;
    
    return $ret;
}
   public function stockofSingleProduct($pid){
        $r = DB::table('stock')
            ->select(DB::raw('SUM(quantity) as metro_stock'))
            ->where([['outlet','=',2],['product','=',$pid]])->get();
        $ret['metro_stock'] = (int)$r[0]->metro_stock;

        $r = DB::table('stock')
            ->select(DB::raw('SUM(quantity) as savar_stock'))
            ->where([['outlet','=',3],['product','=',$pid]])->get();
        $ret['savar_stock'] = (int)$r[0]->savar_stock;

        $r = DB::table('stock')
            ->select(DB::raw('SUM(quantity) as stock'))
            ->where([['outlet','=',4],['product','=',$pid]])->get();
        $ret['orchid_stock'] = (int)$r[0]->stock;

        $r = DB::table('stock')
            ->select(DB::raw('SUM(quantity) as stock'))
            ->where([['outlet','=',1],['product','=',$pid]])->get();
        $ret['office_stock'] = (int)$r[0]->stock;        

        $r = DB::table('stock')
            ->select(DB::raw('SUM(quantity) as stock'))
            ->where([['outlet','=',5],['product','=',$pid]])->get();
        $ret['ecom_stock'] = (int)$r[0]->stock;

        $r = DB::table('stock')
            ->select(DB::raw('SUM(quantity) as stock'))
            ->where([['outlet','=',6],['product','=',$pid]])->get();
        $ret['fcom_stock'] = (int)$r[0]->stock;

        $r = DB::table('stock')
            ->select(DB::raw('SUM(quantity) as stock'))
            ->where([['outlet','=',7],['product','=',$pid]])->get();
        $ret['daraz_stock'] = (int)$r[0]->stock;
        
        $r = DB::table('stock')
            ->select(DB::raw('SUM(quantity) as stock'))
            ->where([['outlet','=',8],['product','=',$pid]])->get();
        $ret['north_stock'] = (int)$r[0]->stock;
        
        return $ret;
   }
}

