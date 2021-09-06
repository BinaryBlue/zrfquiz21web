<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'lte_orders';
    protected $casts = ['items'=>'array'];
    public $timestamps = false;
}
