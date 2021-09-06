<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stocktransfer extends Model
{
    protected $table = 'stock_transfer';
    protected $casts = ['items'=>'array'];
    public $timestamps = false;
}
