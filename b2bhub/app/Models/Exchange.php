<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $table = 'exchanges';
    protected $casts = ['items'=>'array'];
    public $timestamps = false;
}
