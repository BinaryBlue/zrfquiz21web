<?php

namespace App\Listeners;

use App\Models\Product;
use App\Models\Productarchive;

use Codexshaper\WooCommerce\Facades\Product as EProduct;
use Codexshaper\WooCommerce\Facades\Order as EOrder;
use Codexshaper\WooCommerce\Facades\Attribute;
use Codexshaper\WooCommerce\Facades\Term;
use Codexshaper\WooCommerce\Facades\Variation as Variation;
use Illuminate\Support\Facades\DB;



use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;


class RemoveVariationFromEcommerceListner
{
    public function handle($event)
    {
        $codes = (explode("-",$event->code));
        $base_code = $codes[0].'-'.$codes[1];
        $var_code = $event->code;
        $base_product = Product::where('code',$base_code)->first();
        $var_product = Product::where('code',$var_code)->first();

        if(($base_product === null) || ($var_product === null)){
            // Do Nothing
        }
        else{
            $pid = $base_product->e_id;
            $vid = $var_product->e_variation;

            if($pid>0 && $vid>0){
                $options = ['force' => true]; // Set force option true for delete permanently. Default value false
                Variation::delete($pid, $vid, $options);
                
                $var_product->e_variation = 0;
                $var_product->save();
            }
        }

        
        
    }
    // public function handle($event)
    // {
    //     // Initial Section
    //     $products = Product::where('ecommerce','yes')->get();
    //     foreach ($products as $key => $product) {
    //         $id = $product->id;
    //         $pid = $product->e_id;
    //         $code = $product->code;

    //         //Delete All Existing Variations
    //         $variations = Variation::all($pid);
    //         foreach ($variations as $key => $var) {
    //             $options = ['force' => true]; // Set force option true for delete permanently. Default value false
    //             Variation::delete($pid, $var->id, $options);
    //         }


    //         // //Create New Variations
            
    //         // // Collect Variation Codes
    //         // $items = DB::table('product_archive')
    //         //     ->select('product_id','code','price', DB::raw('count(*) as stock'))
    //         //     ->where('status', '=', 3)
    //         //     ->where('code', 'like', $code.'-%')
    //         //     ->groupBy('code')
    //         //     ->orderBy('code')
    //         //     ->get();

    //         // // Create New Variations
    //         // foreach( $items  as $key=>$item){
    //         //     $ep = Product::find($item->product_id);
    //         //     $data = [
    //         //         'sku' => $item->code,
    //         //         'regular_price' => "$item->price",
    //         //         'manage_stock' => false,
    //         //         'stock_quantity' => null,
    //         //         'stock_status' => 'instock',
    //         //         'attributes'    => [
    //         //             [
    //         //                 'id'     => 2,
    //         //                 'option' => $ep->getsize->age_range,
    //         //             ],
    //         //         ],
    //         //     ];
    //         //     $var = Variation::create($pid, $data);
    //         //     //$ep = EProduct::find($id);
    //         //     $ep->e_variation = $var->id;
    //         //     $ep->save();
    //         //}

    //         DB::table('products')->where('id', $id)->update(['last_synched' => now()]);
    //     }
    // }
}
