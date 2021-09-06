<?php

namespace App\Http\Controllers\api\v1\callcenter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Models\Productarchive;
use App\Models\Outlet;

use Codexshaper\WooCommerce\Facades\Product as EProduct;
use Codexshaper\WooCommerce\Facades\Variation;


class ProductController extends Controller
{

    public function barcodes(Request $r){
        $id = $r['outlet_id'];
        $code = $r['code'];
        return Productarchive::where([['code','=',$r['code']], ['outlet_id','=',$r['outlet_id']], ['status','=',3] ])->get();
    }
   
    public function base_details(Request $r){
        $bcode = $r['code'];
        //Base Product
        $tmpHead = Product::where([['code','=',$bcode]])->first();
        //return $ret;
        
        
        if($tmpHead ===null){
            $ret['head'] = Product::where([['code','like','%'.$bcode.'%']])->first();
            $ret['ecommerce'] = null;
        }
        else{
            // Get Head
            $ret['head'] = $tmpHead;
            //Get E-commerce
            if($ret['head']->e_id >0){
                $tmpEcom = EProduct::find($ret['head']->e_id);
                if($tmpEcom=== null){
                    $ret['ecommerce'] = null;
                }
                else{
                    $ret['ecommerce']['base'] = $tmpEcom;
                    $ret['ecommerce']['variations'] = Variation::all($ret['head']->e_id);
                }
                
                
            }
            else $ret['ecommerce'] = null;
        }

         // Variations
         $ret['variations'] = Product::where([['code','like', $bcode.'-%'],['current_stock','>',0]])
         ->orderBy('selling_price')->get();

        // Outlet Wise Stock
        $ret['outlets'] = Outlet::select('id','name')->get();
        $ret['outletwise'] = Productarchive::select('code','outlet_id','outlet_name', 'price',DB::raw('count(*) as barcodes'))
                            ->where('status', '=', 3)
                            ->where('code', 'like', $bcode.'-%')
                            ->groupBy('code','outlet_id')
                            ->orderBy('price')
                            ->get();
        return ['status'=>'200','data'=>$ret];
    }

    public function product_details(Request $r){
        $bcode = $r['code'];
        //Base Product
        $ret['head'] = Product::where('code',$bcode)->first();
        if($ret['head']===null){
            return ['status'=>'404','title'=>'Not Found','message'=>'Product Code Not Exists'];
        }
        else{

            $ret['totalProduction']['counter'] = Productarchive::where('code', $bcode)->count();
            $ret['totalProduction']['barcodes'] = Productarchive::where('code', $bcode)->get();
            
            $ret['inWarehouse']['counter'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',1]])->count();
            $ret['inWarehouse']['barcodes'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',1]])->get();

            $ret['transferChannel']['counter'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',2]])->count();
            $ret['transferChannel']['barcodes'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',2]])->get();

            $ret['sellAble']['counter'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',3]])->count();
            $ret['sellAble']['barcodes'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',3]])->get();

            $ret['orderChannel']['counter'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',71]])->count();
            $ret['orderChannel']['barcodes'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',71]])->get();

            $ret['sold']['counter'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',4]])->count();
            $ret['sold']['barcodes'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',4]])->get();

            $ret['damaged']['counter'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',99]])->count();
            $ret['damaged']['barcodes'] = Productarchive::where([['code', '=' ,$bcode],['status', '=',99]])->get();

            // Outlet Wise Inventory
            $ret['statuswise'] = Productarchive::select('status', DB::raw('count(*) as barcodes'))
                ->where('code', $bcode)
                ->groupBy('status')
                ->get();

            $ret['outletwise'] = Productarchive::select('code','outlet_id','outlet_name', DB::raw('count(*) as barcodes'))
                ->where('code', $bcode)
                ->where('status','!=', 4)
                ->groupBy('outlet_id')
                ->orderBy('outlet_id')
                ->get();

            $ret['outlets'] = Outlet::select('id','name')->get();

            
            return ['status'=>'200','data'=>$ret];
        }
    }
}
