<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class old_stock_entry extends Model
{
    protected $table = 'old_stock_entry';
    protected $casts = ['items'=>'array'];
    public $timestamps = false;
}
