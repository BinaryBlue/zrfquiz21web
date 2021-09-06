<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_return extends Model
{
    protected $table = 'product_returns';
    protected $casts = ['items'=>'array','screen_data'=>'array'];
    public $timestamps = false;
}
