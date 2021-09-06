<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packaging extends Model
{
    protected $table = 'packaging';
    protected $casts = ['remarks'=>'array'];
    public $timestamps = false;
}
