<?php

namespace App\Http\Controllers\api\v1\common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Payment_method;
use App\Models\Balance_transection;

class PaymentmethodController extends Controller
{
    public function index(){
        return Payment_method::all();
    }
}
