<?php

namespace App\Http\Controllers\api\v1\ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Codexshaper\WooCommerce\Facades\Product;

class EproductController extends Controller
{
    public function create(Request $request){
        $categories = [
            [
                'id' => 1,
            ],
            [
                'id' => 3,
            ],
        ];
        
        $images = [
            [
                'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg',
            ],
            [
                'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_back.jpg',
            ],
        ];

        $attributes = [
            [
                'id' => 1,
                'name' => 'color',
                'position' => 0,
                'visible' => true,
                'variation' => true,
                'options' => [
                    'Black',
                    'Green'
                ]
            ],
            [
                'id' => 2,
                'name' => "Baby's Age",
                'position' => 1,
                'visible' => true,
                'variation' => true,
                'options' => [
                    'S',
                    'M'
                ]
            ]
        ];
        $default_attributes = [
            [
                'id' => 6,
                'option' => 'Black'
            ],
            [
                'name' => 'Size',
                'option' => 'S'
            ]
        ];
        
        $product                    = new Product;
        $product->name              = 'Product Eloquent 2';
        $product->type              = 'simple';
        $product->regular_price     = '100';
        $product->sale_price        = '';
        $product->description       = 'Product Description';
        $product->short_description = 'Product Short Description';
        $product->categories        = $categories;
        $product->images            = $images;
        $product->attributes        = $attributes;
        $product->default_attributes= $default_attributes;
        $product->save();
    }
    
    public function all()
    {
        return Product::all(['per_page'=>100,'status'=>'publish']);
    }
}
