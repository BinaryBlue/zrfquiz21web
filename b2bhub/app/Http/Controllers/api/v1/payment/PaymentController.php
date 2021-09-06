<?php

namespace App\Http\Controllers\api\v1\payment;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Balance_transection;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Payment;


class PaymentController extends Controller
{
    public function getNewTransectionno($outlet){
        $today = date("Y-m-d");
        $test_inv = $today.'-'.$outlet;
        $statements = Balance_transection::where('transection_no','like', '%'.$test_inv.'%')->get();
        $new = $statements->count() + 1;
        return $test_inv.'-'.$new;
    }

    public function getNewPaymentno($outlet){
        $today = date("Y-m-d");
        $test_inv = 'P-'.$today.'-'.$outlet;
        $statements = Payment::where('payment_no','like', '%'.$test_inv.'%')->get();
        $new = $statements->count() + 1;
        return $test_inv.'-'.$new;
    }


    public function makePayment(Request $r){
        
        $ret = DB::transaction(function () use ($r) {
            $balance_amount = 0;
            $balance_method = 0;
            $type = $r['type'];
            // Cash/Partial Cash
            if($r['paymentmethod']['id']==1 || $r['paymentmethod']['id']==2){
                $balance_amount = floatval($r['amount']);
                $balance_method = 1;
                Balance::where(['outlet' => $r['outlet'], 'balance_method' => $balance_method])->increment('amount', $balance_amount);
            }
            //City Amex, Bkash, UCB, UKash
            else if($r['paymentmethod']['id']==4 || $r['paymentmethod']['id']==6 || $r['paymentmethod']['id']==9 || $r['paymentmethod']['id']==11){
                $balance_amount = floatval($r['amount']);
                $balance_method = 2;
                Balance::where(['outlet' => $r['outlet'], 'balance_method' => $balance_method])
                    ->increment('amount', $balance_amount);
            }
            //DBBL, Rocket, Nexus
            else if($r['paymentmethod']['id']==5 || $r['paymentmethod']['id']==7 || $r['paymentmethod']['id']==10){
                $balance_amount = floatval($r['amount']);
                $balance_method = 3;
                Balance::where(['outlet' => $r['outlet'], 'balance_method' => $balance_method])
                    ->increment('amount', $balance_amount);
            }

            //Common Transection Log
            $transection_no = $this->getNewTransectionno($r['outlet']);    
            $transaction = new Balance_transection();
            $transaction->fy = date("Y");
            $transaction->date = date("Y-m-d");
            $transaction->transection_no = $transection_no;
            $transaction->outlet = (int)$r['outlet'];
            $transaction->amount = $balance_amount;
            $transaction->order_no = $r['order_no'];
            $transaction->balance_method = $balance_method;
            $transaction->type = $type;
            $transaction->entry_by = Auth::user()->id;
            $transaction->save();

            $payment_no = $this->getNewPaymentno($r['outlet']);
            $paymnt = new Payment();
            $paymnt->fy = date("Y");
            $paymnt->date = date("Y-m-d");
            $paymnt->balance_tran_no = $transection_no;
            $paymnt->payment_no = $payment_no;
            $paymnt->outlet = (int)$r['outlet'];
            $paymnt->amount = $balance_amount;
            $paymnt->order_no = $r['order_no'];
            $paymnt->balance_method = $balance_method;
            $paymnt->payment_method = (int) $r['paymentmethod']['id'];
            $paymnt->type = $type;
            $paymnt->entry_by = Auth::user()->id;
            $paymnt->save();

            //$this->insertOrderLog($r['order_no'],'Payment','Payment '.$payment_no.' Amount '.$paymnt->amount.'/- Via '.$r['paymentmethod']['name'].' Entry By '.Auth::user()->username);

            Order::where(['order_no' => $r['order_no']])->increment('paid', $balance_amount);

            return $paymnt;
        });

        return $ret;
    }

    public function getPaymentList(Request $r){
        $id = (int)$r['outlet'];
        $fromDate = $r['fromDate'];
        $toDate = $r['toDate'];
        
        if(Auth::user()->group_id < 3){ // For Top management +
            return 
                Payment::whereBetween('date', [$fromDate, $toDate])
                ->orderBy('entry_at','desc')
                ->get();
        }
        else{ // For Individual
            return Payment::where('outlet',$id)
                ->whereBetween('date', [$fromDate, $toDate])
                ->orderBy('entry_at','desc')
                ->get();
        }
    }
}
