<?php

namespace App\Http\Controllers\api\v1\expense;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ExpenseController extends Controller
{
    public function entry(Request $r){
        $expense = new Expense();
        $expense->date = date("Y-m-d");
        $expense->sector_id = $r['sector_id'];
        $expense->sector_name = $r['sector_name'];
        $expense->receipt_no = $r['order_no'];
        $expense->amount = $r['amount'];
        $expense->remarks = $r['remarks'];
        $expense->save();

        return [
            'status'=>'success',
            'title'=>'Expense Added',
            "message" => 'Expense Successfully Inserted For This Order',
        ];
    }
}
