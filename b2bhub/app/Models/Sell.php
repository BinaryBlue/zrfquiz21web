<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sell extends Model
{
    protected $table = 'sell';
    protected $casts = ['items'=>'array','screen_data'=>'array'];
    public $timestamps = false;
}
