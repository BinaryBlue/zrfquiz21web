<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;
use App\Models\Customer;
use App\Models\Outlet;
use NumberToWords\NumberToWords;


class BHelper
{
    public static function customerdetails($id)
    {
        return Customer::find($id);
    }
    public static function outletdetails($id)
    {
        return Outlet::find($id);
    }

    public static function inWords($number){
        $toWords = new NumberToWords();
        $numberTransformer = $toWords->getNumberTransformer('en');
        return $numberTransformer->toWords($number);
        //return $r;
    }
}