<?php

namespace App\Http\Controllers\api\v1\ecommerce;

use App\Events\StockIsZeroEvent;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Codexshaper\WooCommerce\Facades\Product;
use Codexshaper\WooCommerce\Facades\Order;
use Codexshaper\WooCommerce\Facades\Attribute;
use Codexshaper\WooCommerce\Facades\Term;
use Codexshaper\WooCommerce\Facades\Variation; 

use App\Models\Product as Eproduct;
use App\Models\Color as Ecolor;
use App\Models\Size as Esize;

class EcommerceController extends Controller
{
    public function all_products(){
        return Product::all(['per_page'=>100,'status'=>'publish']);
    }
    public function all_orders(){
        //return Order::all(['per_page'=>100,'status'=>'processing']);
        //return Attribute::all();
        return Attribute::all();
        //return Term::all(1);             
    }

    //public function remove_ecom(Request)
    public function create_new(Request $request){
        //if 
        //return $request;
        //$ultima = [];
        //$color_options = [];
        $size_options = [];
        $variations = [];
        foreach ($request['itemList'] as $key => $item) {
            $product = Eproduct::find($item['id']);
            // if(!in_array($product->getcolor->name,$color_options)){
            //     array_push($color_options,$product->getcolor->name);
            // }
            if(!in_array($product->getsize->age_range,$size_options)){
                array_push($size_options,$product->getsize->age_range);
            }
            $v['erp_id'] = $product->id;
            $v['var_data'] = [
                'sku' => $product->code,
                'regular_price' => "$product->selling_price",
                'manage_stock' => false,
                'stock_quantity' => null,
                'stock_status' => 'instock',
                'attributes'    => [
                    // [
                    //     'id'     => 1,
                    //     'option' => $product->getcolor->name,
                    // ],
                    [
                        'id'     => 2,
                        'option' => $product->getsize->age_range,
                    ],
                ],
            ];
            array_push($variations,$v);
        
        }
        //return $color_options;
        
        // $images = [
        //     ['src'=>'http://phpstack-423465-1456329.cloudwaysapps.com/blueerp/public/products/images/1598773009-60370812.jpg']
        // ];

        $images=[];
        if($request['basedproduct']['photo']!=''){
            $temp = [ 'src' => env('APP_URL').'products/images/'.$request['basedproduct']['photo']];
            array_push($images,$temp);
        }
        if($request['basedproduct']['photo1']!=''){
            $temp = [ 'src' => env('APP_URL').'products/images/'.$request['basedproduct']['photo1']];
            array_push($images,$temp);
        }
        if($request['basedproduct']['photo2']!=''){
            $temp = [ 'src' => env('APP_URL').'products/images/'.$request['basedproduct']['photo2']];
            array_push($images,$temp);
        }
        $attributes = [
            // [
            //     'id' => 1,
            //     'name' => 'color',
            //     'position' => 0,
            //     'visible' => true,
            //     'variation' => true,
            //     'options' => $color_options
            // ],
            [
                'id' => 2,
                'name' => "Baby's Age",
                'position' => 1,
                'visible' => true,
                'variation' => true,
                'options' => $size_options
            ]
        ];

        $data = [
            'name' => $request['basedproduct']['name'],
            'type' => 'variable',
            'status' => 'publish',
            'price' => $request['basedproduct']['selling_price'],
            'description' => $request['basedproduct']['long_description'],
            'short_description' => $request['basedproduct']['description'],
            "manage_stock" => false,
            "stock_quantity" =>null,
            "stock_status" => "instock",
            "attributes" => $attributes,
            'images' => $images
        ];
        
        $product = Product::create($data);
        foreach ($variations as $key=>$var) {
            $v = Variation::create($product->id, $var['var_data']);
            $ep = Eproduct::find($var['erp_id']);
            $ep->e_variation = $v->id;
            $ep->save();
        }

        $basedp = Eproduct::find($request['basedproduct']['id']);
        $basedp->e_id = $product->id;
        $basedp->last_synched = now();
        $basedp->save();

        return ['status'=>'success','title' => 'Successful','message'=>'Product Successfully Submitted to Ecommerce Site','id'=>$product->id];
        
        //return $data;

    }
    
    public function sync_delete_var(Request $r){
        event(new StockIsZeroEvent('code')); 
        // $pid = $r['pid'];
        // //Delete Existing Variations
        // $variations = Variation::all($pid);
        // foreach ($variations as $key => $var) {
        //     $options = ['force' => true]; // Set force option true for delete permanently. Default value false
        //     Variation::delete($pid, $var->id, $options);
        // }
        return ['status'=>'success'];
    }
    
    public function soft_sync(Request $r){  
        $ret_val = DB::transaction(function () use ($r) {
            // $pid = $r['pid'];
            // $code = $r['code'];

            // // Collect Variation Codes
            // $items = DB::table('product_archive')
            //     ->select('product_id','code','price', DB::raw('count(*) as stock'))
            //     ->where('status', '=', 3)
            //     ->where('code', 'like', $code.'-%')
            //     ->groupBy('code')
            //     ->orderBy('code')
            //     ->get();

            // // Create New Variations
            // foreach( $items  as $key=>$item){
            //     $ep = Eproduct::find($item->product_id);
            //     $data = [
            //         'sku' => $item->code,
            //         'regular_price' => "$item->price",
            //         'manage_stock' => false,
            //         'stock_quantity' => null,
            //         'stock_status' => 'instock',
            //         'attributes'    => [
            //             [
            //                 'id'     => 2,
            //                 'option' => $ep->getsize->age_range,
            //             ],
            //         ],
            //     ];
            //     Variation::create($pid, $data);
            // }

            // DB::table('products')->where('e_id', $pid)->update(['last_synched' => now()]);
            return ['status'=>'success','last_synched'=>now(), 'title' => 'Successful','message'=>'Product Successfully Synced'];
        });
        return $ret_val;


    }
    public function update_variation($code){ // Updates stock and price
        //dd(['aa'=>$code]);
        $eid = 0;
        $products = Eproduct::where('code','like', '%'.$code.'-%')->get();
        foreach($products as $p){
           // dd($p);
            if($p->eid > 0) $eid = $p->eid;
            else{
                // if($p->e_variation > 0){ //  Updates existing variation.
                //     $vid = $p->e_variation;
                //     $data = [
                //         'stock_quantity' => $p->current_stock,
                //         'regular_price' => "$p->selling_price"
                //     ];
                //     Variation::update($eid, $vid, $data);
                // }
                // else{ // Create New Variation
                //     $ep = Eproduct::find($p->id);
                //     $stock_status = 'outofstock';
                //     if($p->current_stock > 0) $stock_status = 'instock';
                //     $data = [
                //         'sku' => $p->code,
                //         'regular_price' => "$p->selling_price",
                //         'manage_stock' => true,
                //         'stock_quantity' => $p->current_stock,
                //         'stock_status' => $stock_status,
                //         'attributes'    => [
                //             [
                //                 'id'     => 2,
                //                 'option' => $ep->getsize->age_range,
                //             ],
                //         ],
                //     ];
                //     $v = Variation::create($p->e_id, $data);
                //     //$ep = Eproduct::find($p->id);
                //     $ep->e_variation = $v->id;
                //     $ep->save();
                // }
            }
        }
    }
    public function submit_product(Request $request){
        $existing = (int)$request['basedproduct']['e_id'];
        $ret = null;
        if($existing == 0){
            $ret = $this->create_new($request);
        }
        else{
            $ret = $this->update_variation($request['basedproduct']['code']);
        } 
    }
}
