<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'lte_products';
    protected $casts = ['rpp'=>'array'];
    public $timestamps = false;
    protected $fillable = array('cat_id','size_id','supplier_id','metric_id');

    public function category(){
        return $this->belongsTo(Category::class, 'cat_id','id');
    }

    public function size(){
        return $this->belongsTo(Size::class, 'size_id','id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class, 'supplier_id','id');
    }

    public function metric(){
        return $this->belongsTo(Metric::class, 'metric_id','id');
    }
}
