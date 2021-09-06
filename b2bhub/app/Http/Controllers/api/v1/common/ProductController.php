<?php

namespace App\Http\Controllers\api\v1\common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Models\Productarchive;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return Product::find(1)->toArray();
        //return Product::where('active','Yes')->toArray(); ->select('id','code','name','mrp','metric')
        //return Product::with('metric')->where('active','Yes')->toArray();
        return Product::select('id','code','name','mrp','metric_id','size_id','cat_id','supplier_id')->where('active','Yes')
                ->with([
                    'category'=> function($query) {
                        $query->select('id','name');
                    },
                    'size'=> function($query) {
                        $query->select('id','name');
                    },
                    'brand'=> function($query) {
                        $query->select('id','name');
                    },
                    'metric'=> function($query) {
                        $query->select('id','name');
                    }

                ])->get();
    }

    public function paginatedProduct(){
        // Update Based Product Stock
        $products = Product::where('ecommerce','yes')->get();
        foreach($products as $product)
        {
            $code = $product->code;
            $stock = (int)Productarchive::where(
                [
                    ['code','like', '%'.$code.'%'],
                    ['status','=',3]
                ]
                )->count();
            DB::table('products')
                ->where('id',$product->id)
                ->update(['current_stock'=>$stock]);
        }
        //DB::table('products')->where('e_id', 0)->update(['ecommerce' => 'no']);
        // return Product::where('ecommerce','=','yes')->paginate(30);
        return Product::where('ecommerce','=','yes')->orderBy('last_synched','desc')->get();
        
    }

    public function ecomindex(){
        return Product::where('ecommerce','=','yes')->get();
    }

    public function productdetailsfrombarcode($code){
        return Product::where(['code'=>$code,'ecommerce'=>'no'])->get();
    }
    
    public function combinationFromCode($code){
    	return DB::table('products')
                ->where('code', 'like', $code.'%')
                ->get();
    }
    public function outletwisebarcode($code){
        $items = DB::table('product_archive')
                 ->select('outlet_id','outlet_name', DB::raw('count(*) as barcodes'))
                 ->where('status', '=', 3)
                 ->where('code', '=', $code)
                 ->groupBy('outlet_id')
                 ->orderBy('outlet_id')
                 ->get();
        $retItems = [];
        foreach( $items  as $key=>$item){
            $archive = Productarchive::where(['outlet_id'=>$item->outlet_id,'code'=>$code, 'status'=>3 ])->first();
            $retitem['barcode'] = $archive->barcode;
            $retitem['barcode_id'] = $archive->id;
            $retitem['outlet_id'] =  $item->outlet_id;
            $retitem['outlet_name'] =  $item->outlet_name;
            $retitem['barcodes'] =  $item->barcodes;
            array_push($retItems,$retitem);
        }
        return $retItems;
    }
   
}
