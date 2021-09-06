<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Artisan;
use Closure;

class ReloadController extends Controller
{
    public function clear(){
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        //Artisan::call('optimize');
        //exec('composer dump-autoload');
        echo 'All Cache Cleared!!!';
    }
}
