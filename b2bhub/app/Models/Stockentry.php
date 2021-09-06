<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stockentry extends Model
{
    protected $table = 'lte_stock_entry';
    protected $casts = ['items'=>'array'];
    public $timestamps = false;
}
