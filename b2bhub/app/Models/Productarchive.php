<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productarchive extends Model
{
    protected $table = 'product_archive';
    protected $casts = ['transfer'=>'array'];
    public $timestamps = false;
}
