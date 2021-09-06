<?php

namespace App\Http\Controllers\api\v1\sell;

use App\Events\StockIsZeroEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;


use App\Models\Sell as ModelsSell;
use App\Models\Customer as Customer;
use App\Models\Customer_due as Cdue;
use App\Models\Customer_due;
use App\Models\Stock;
use App\Models\Productarchive;
use App\Models\Product;
use App\Models\Balance;
use App\Models\Product_return as Preturn;
use App\Models\Balance_transection;

class SellController extends Controller
{
    public function profit_adjust($statement,$type){
        $rdata['profit'] = 0.00;
        $rdata['discount'] = 0.00;
        $rdata['returnprofit'] = 0.00;

        if($type=='return'){
            $return_rdata = $this->return_profit($statement);
            $rdata['returnprofit'] = $return_rdata['rnetprofit'];
            $rdata['rprofit'] = $return_rdata['rprofit'];
            $rdata['rdiscount'] = $return_rdata['rdiscount'];
            $rdata['netprofit'] = - (float)$rdata['returnprofit'];
        }
        else if($type=='sell'){
            $sells = ModelsSell::where('invoice_no',$statement)->get();
            $sell = $sells[0];
            $data = $sell->screen_data;
            foreach ($data['itemList'] as $key => $item) {
                $rdata['profit'] += ((float)$item['price'] - (float)$item['purchase_price']);
            }
            $rdata['discount'] = (float)$sell->discount;
            if($sell->returninvoice==null){
                $rdata['netprofit'] = $rdata['profit'] - $rdata['discount'];
            }
            else{
                $return_rdata = $this->return_profit($sell->returninvoice);
                $rdata['returnprofit'] = $return_rdata['rnetprofit'];
                $rdata['rprofit'] = $return_rdata['rprofit'];
                $rdata['rdiscount'] = $return_rdata['rdiscount'];
                $rdata['netprofit'] = $rdata['profit'] - $rdata['discount'] - (float)$rdata['returnprofit'];
                $rdata['netprofit'] = number_format((float)$rdata['netprofit'], 2); 
            }
            
        }
        
        return $rdata;
    }

    public function return_profit($statement){
        $returns = Preturn::where('receipt_no',$statement)->get();
        $return = $returns[0];
        $rdata['rprofit'] = 0.00;
        $rdata['rdiscount'] = 0.00;
        $rdata['rnetprofit'] = 0.00;
        foreach ($return->items as $key => $item) {
            $invoice = $item['sell_statement'];
            $sells = ModelsSell::where('invoice_no',$invoice)->get();
            $discount = (float)$sells[0]->discount;
            $rdata['rdiscount'] += ($discount / count($sells[0]->items));
            $rdata['rprofit'] += ((float)$item['price'] - (float)$item['purchase_price']);
        }
        $rdata['rnetprofit'] = $rdata['rprofit'] - $rdata['rdiscount'];
        return $rdata;
    }
    public function init(Request $request)
    {
        $r = $request['data'];

        if($r['operationtype']=='3'){ // Returns
            $invoice_no = $this->getReturnStatementNo($r['outlet']['id']);
            $r['receiptno'] = $invoice_no;
            //return $r;
            DB::transaction(function () use ($r,$invoice_no) {
                $return = new Preturn();
                $return->fy = date("Y");
                $return->date = date("Y-m-d");
                $return->receipt_no = $invoice_no;
                $return->outlet = $r['outlet']['id'];
                $return->amount = $r['retamount'];
                $return->paid = $r['retamount'];
                $return->items = $r['returnList'];
                $return->screen_data = $r;
                $return->save();

                foreach( $return->items  as $key=>$item){
                    // Stock Table
                    DB::table('stock')
                        ->where(['product' => $item['pid'], 'outlet' => $r['outlet']['id']])
                        ->increment('quantity', 1);
                    
                    // product archive table
                    DB::table('product_archive')
                        ->where('id',$item['arcid'])
                        ->update(['outlet_id'=>$r['outlet']['id'],'outlet_name'=>$r['outlet']['name'],'sell_statement'=>null,'status'=>3,'update_by'=>Auth::user()->id]);
                    // Product Table
                    DB::table('products')
                        ->where('id',$item['pid'])
                        ->increment('current_stock', 1);
                }

            });

            $receiptid = DB::table('product_returns')->where('receipt_no', $invoice_no)->pluck('id');
            $r['receiptid'] = $receiptid[0];
        }

        $invoice_no = $this->getNewStatementNo($r['outlet']['id']);
        $r['invoiceno'] = $invoice_no;
        DB::transaction(function () use ($r,$invoice_no) {

            // Sell Table
            $customer_due = 0; // Works as Flag
            $sell = new ModelsSell();
            $sell->fy = date("Y");
            $sell->date = date("Y-m-d");
            $sell->order_no = 'POS';
            $sell->invoice_no = $invoice_no;
            if($r['operationtype']=='3'){
                $sell->returninvoice = $r['receiptno'];
            }
            $sell->outlet = $r['outlet']['id'];
            $sell->amount = $r['amount'];
            $sell->customer = $r['customer']['id'];
            $sell->discount = $r['discamount'];
            $sell->vat = $r['vatamount'];
            $sell->payable = $r['netpayable'];
            $sell->items = $r['itemList'];
            $sell->payment_method = $r['paymentmethod']['id'];
            $sell->payment_method2 = $r['paymentmethod2']['id'];
            $sell->payment_method3 = $r['paymentmethod3']['id'];
            if($r['paymentmethod']['id']==2){ // partial cash
                $r['netpaid'] = $r['partialpaid'];
                $r['netdue'] = floatval($r['netpayable']) - floatval($r['partialpaid']);
                $sell->paid = $r['partialpaid'];
                $sell->due = floatval($r['netpayable']) - floatval($r['partialpaid']);
                $sell->status = '0';
                $customer_due = floatval($r['netpayable']) - floatval($r['partialpaid']);
            }
            else if($r['paymentmethod']['id']==3){ // complete due
                $r['netpaid'] = 0;
                $r['netdue'] = floatval($r['netpayable']);
                $sell->paid = 0;
                $sell->due = floatval($r['netpayable']);
                $sell->status = '0';
                $customer_due = floatval($r['netpayable']);
            }
            else if($r['paymentmethod']['id']==8){ //dual
                $r['netpaid'] = $r['netpayable'];
                $r['netdue'] = 0;
                $sell->paid = $r['netpayable'];
                $sell->paid2 = $r['dualamount'];
                $sell->paid3 = floatval($r['netpayable']) - floatval($r['dualamount']);
                $sell->due = 0;
                $sell->status = '1';
            }
            else{ // rest all
                $r['netpaid'] = $r['netpayable'];
                $r['netdue'] = 0;
                $sell->paid = $r['netpayable'];
                $sell->due = 0;
                $sell->status = '1';
            }
            $sell->entry_by = Auth::user()->id;
            $sell->screen_data = $r;
            $sell->save();

            $balance_amount = 0;
            $balance_method = 0;
            // Cash/Partial Cash
            if($r['paymentmethod']['id']==1 || $r['paymentmethod']['id']==2){
                $balance_amount = floatval($r['netpaid']);
                $balance_method = 1;
                DB::table('balance')
                    ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                    ->increment('amount', $balance_amount);
            }
            //City Amex, Bkash, UCB, UKash
            else if($r['paymentmethod']['id']==4 || $r['paymentmethod']['id']==6 || $r['paymentmethod']['id']==9 || $r['paymentmethod']['id']==11){
                $balance_amount = floatval($r['netpaid']);
                $balance_method = 2;
                DB::table('balance')
                    ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                    ->increment('amount', $balance_amount);
            }
            //DBBL, Rocket, Nexus
            else if($r['paymentmethod']['id']==5 || $r['paymentmethod']['id']==7 || $r['paymentmethod']['id']==10){
                $balance_amount = floatval($r['netpaid']);
                $balance_method = 3;
                DB::table('balance')
                    ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                    ->increment('amount', $balance_amount);
            }
            // Dual
            else if($r['paymentmethod']['id']==8){

                if($r['paymentmethod2']['id']==1 || $r['paymentmethod2']['id']==2){ // Cash
                    $balance_amount = floatval($r['dualamount']);
                    $balance_method = 1;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
                // City
                else if($r['paymentmethod2']['id']==4 || $r['paymentmethod2']['id']==6 || $r['paymentmethod2']['id']==9 || $r['paymentmethod2']['id']==11){
                    $balance_amount = floatval($r['dualamount']);
                    $balance_method = 2;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
                // DBBL
                else if($r['paymentmethod2']['id']==4 || $r['paymentmethod2']['id']==6 || $r['paymentmethod2']['id']==9 || $r['paymentmethod2']['id']==11){
                    //$balance_amount = floatval($r['netpayable']) - floatval($r['dualamount']);
                    $balance_amount = floatval($r['dualamount']);
                    $balance_method = 3;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
                
                // Special Transection Log For Dual Amount
                $transection_no = $this->getNewTransectionno($r['outlet']['id']);    
                $t_type = 1;
                if($balance_amount<0) $t_type = -1;
                DB::table('balance_transections')->insert(
                    [
                        'fy' => date("Y"), 'date' => date("Y-m-d"),'transection_no'=>$transection_no,
                        'outlet' => $r['outlet']['id'], 'amount' => $balance_amount,'type'=>1,'balance_method' =>$balance_method
                    ]
                );

                // End of Special Transection Log
                
                if($r['paymentmethod3']['id']==1 || $r['paymentmethod3']['id']==2){ // Cash
                    $balance_amount = floatval($r['netpayable']) - floatval($r['dualamount']);
                    $balance_method = 1;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
                //City
                else if($r['paymentmethod3']['id']==4 || $r['paymentmethod3']['id']==6 || $r['paymentmethod3']['id']==9 || $r['paymentmethod3']['id']==11){
                    $balance_amount = floatval($r['netpayable']) - floatval($r['dualamount']);
                    $balance_method = 2;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
                //DBBL
                else if($r['paymentmethod2']['id']==4 || $r['paymentmethod2']['id']==6 || $r['paymentmethod2']['id']==9 || $r['paymentmethod2']['id']==11){
                    $balance_amount = floatval($r['netpayable']) - floatval($r['dualamount']);
                    $balance_method = 3;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
            }

            //Common Transection Log
            $transection_no = $this->getNewTransectionno($r['outlet']['id']);    
            $t_type = 1;
            if($balance_amount<0) $t_type = -1;
            DB::table('balance_transections')->insert(
                [
                    'fy' => date("Y"), 'date' => date("Y-m-d"),'transection_no'=>$transection_no,
                    'outlet' => $r['outlet']['id'], 'amount' => $balance_amount,'type'=>1,'balance_method' =>$balance_method
                ]
            );

            foreach( $sell->items  as $key=>$item){
                // Stock Table
                DB::table('stock')
                    ->where(['product' => $item['pid'], 'outlet' => $r['outlet']['id']])
                    ->decrement('quantity', 1);
                
                // product archive table
                DB::table('product_archive')
                    ->where('id',$item['arcid'])
                    ->update(['status'=>4,'sell_statement'=>$invoice_no,'update_by'=>Auth::user()->id]);
                // Product Table
                DB::table('products')
                    ->where('id',$item['pid'])
                    ->decrement('current_stock', 1);

                $ecom_stock = (int)Productarchive::where(['code'=>$item['code'],'status'=>3])->count();
                if($ecom_stock < 1){
                    //event(new StockIsZeroEvent($item['code']));
                }
                $customerSMSid = $sell->customer;
                $customerSMS = Customer::find($customerSMSid);
                Http::asForm()->post('http://api.greenweb.com.bd/api.php', [
                    'token' => '23ad51465250a1351b745f9e46a8b744',
                    'to' => '+88'.$customerSMS->mobile,
                    'message' => '"দিগন্ত" বাচ্চাদের পোশাক। আপনার পণ্য এই ইনভয়েসের '.$sell->invoice_no.' মাধ্যমে নিশ্চিত করা হয়েছে। বিলঃ '.$sell->payable.'/-, পরিশোধিতঃ '.($sell->payable - $sell->due).'/-, বকেয়াঃ '.$sell->due.'/-। দিগন্তের সাথে থাকার জন্য ধন্যবাদ।'
                ]);
            }
            

            if($customer_due>0){ // Make Due Entry of Customer
                DB::table('customers')
                    ->where('id', $r['customer']['id'])
                    ->increment('dues', $customer_due);

                // Customer DUE Table
                $cdu = new Customer_due();
                $cdu->fy = date("Y");
                $cdu->customer = $r['customer']['id'];
                $cdu->c_name = $r['customer']['name'];
                $cdu->invoice = $invoice_no;
                $cdu->due = $customer_due;
                $cdu->entry_by = Auth::user()->id;
                $cdu->save();
            }


        });


        $inv = DB::table('sell')->where('invoice_no', $invoice_no)->pluck('id');
        $r['receiptid'] = $inv[0];

        $profit = $this->profit_adjust($invoice_no,'sell');
        DB::table('sell') ->where('id', $inv[0])->update(['profit' => $profit['netprofit']]);
        
        $rtp = 1;
        if($profit['netprofit']<0) $rtp = -1;
        DB::table('profits') ->where(['outlet' => $r['outlet']['id']])->increment('amount', $profit['netprofit']);
        DB::table('profit_transections')->insert(
            [
                'fy' => date("Y"), 'date' => date("Y-m-d"),'invoice_no'=>$invoice_no,
                'outlet' => $r['outlet']['id'], 'amount' =>$profit['netprofit'],'type'=>$rtp
            ]
        );

        return $r;
    }

    public function return(Request $request){
        $r = $request['data'];
        //return $r;
        $invoice_no = $this->getReturnStatementNo($r['outlet']['id']);
        $r['receiptno'] = $invoice_no;
        //return $r;
        DB::transaction(function () use ($r,$invoice_no) {
            $return = new Preturn();
            $return->fy = date("Y");
            $return->date = date("Y-m-d");
            $return->receipt_no = $invoice_no;
            $return->outlet = $r['outlet']['id'];
            $return->amount = $r['retamount'];
            $return->paid = $r['retamount'];
            $return->items = $r['returnList'];
            $return->screen_data = $r;
            $return->save();

            //$profit = $this->profit_adjust($invoice_no,'return');
            //$return->profit = $profit['netprofit'];
            $return->save();

            foreach( $return->items  as $key=>$item){
                // Stock Table
                DB::table('stock')
                    ->where(['product' => $item['pid'], 'outlet' => $r['outlet']['id']])
                    ->increment('quantity', 1);
                
                // product archive table
                DB::table('product_archive')
                    ->where('id',$item['arcid'])
                    ->update(['outlet_id'=>$r['outlet']['id'],'outlet_name'=>$r['outlet']['name'],'sell_statement'=>null,'status'=>3,'update_by'=>Auth::user()->id]);
                // Product Table
                DB::table('products')
                    ->where('id',$item['pid'])
                    ->increment('current_stock', 1);
            }

            $transection_no = $this->getNewTransectionno($r['outlet']['id']);    
            DB::table('balance_transections')->insert(
                [
                    'fy' => date("Y"), 'date' => date("Y-m-d"),'transection_no'=>$transection_no,
                    'outlet' => $r['outlet']['id'], 'amount' => $r['retamount'],'type'=>-1,'balance_method' =>1
                ]
            );

            DB::table('balance')
                ->where(['outlet' => $r['outlet']['id'], 'balance_method' => 1])
                ->decrement('amount', $r['retamount']);

        });

        $receiptid = DB::table('product_returns')->where('receipt_no', $invoice_no)->get();
        $r['receiptid'] = $receiptid[0]->id;

        $profit = $this->profit_adjust($invoice_no,'return');
        $rtp = -1;
        if($profit['netprofit'] > 0) $rtp = 1;
        DB::table('profits') ->where(['outlet' => $r['outlet']['id']])->increment('amount', $profit['netprofit']);
        DB::table('profit_transections')->insert(
            [
                'fy' => date("Y"), 'date' => date("Y-m-d"),'return_no'=>$invoice_no,
                'outlet' => $r['outlet']['id'], 'amount' =>$profit['netprofit'] ,'type'=>$rtp
            ]
        );
        return $r;
    }

    public function init_old(Request $request)
    {
        $r = $request['data'];
        $invoice_no = $this->getNewStatementNo($r['outlet']['id']);
        $r['invoiceno'] = $invoice_no;
        DB::transaction(function () use ($r,$invoice_no) {

            // Sell Table
            $customer_due = 0; // Works as Flag
            $sell = new ModelsSell();
            $sell->fy = date("Y");
            $sell->date = date("Y-m-d");
            $sell->order_no = 'POS';
            $sell->invoice_no = $invoice_no;
            $sell->outlet = $r['outlet']['id'];
            $sell->amount = $r['amount'];
            $sell->customer = $r['customer']['id'];
            $sell->discount = $r['discamount'];
            $sell->vat = $r['vatamount'];
            $sell->payable = $r['netpayable'];
            $sell->items = $r['itemList'];
            $sell->payment_method = $r['paymentmethod']['id'];
            $sell->payment_method2 = $r['paymentmethod2']['id'];
            $sell->payment_method3 = $r['paymentmethod3']['id'];
            if($r['paymentmethod']['id']==2){ // partial cash
                $r['netpaid'] = $r['partialpaid'];
                $r['netdue'] = floatval($r['netpayable']) - floatval($r['partialpaid']);
                $sell->paid = $r['partialpaid'];
                $sell->due = floatval($r['netpayable']) - floatval($r['partialpaid']);
                $sell->status = '0';
                $customer_due = floatval($r['netpayable']) - floatval($r['partialpaid']);
            }
            else if($r['paymentmethod']['id']==3){ // complete due
                $r['netpaid'] = 0;
                $r['netdue'] = floatval($r['netpayable']);
                $sell->paid = 0;
                $sell->due = floatval($r['netpayable']);
                $sell->status = '0';
                $customer_due = floatval($r['netpayable']);
            }
            else if($r['paymentmethod']['id']==8){ //dual
                $r['netpaid'] = $r['netpayable'];
                $r['netdue'] = 0;
                $sell->paid = $r['netpayable'];
                $sell->paid2 = $r['dualamount'];
                $sell->paid3 = floatval($r['netpayable']) - floatval($r['dualamount']);
                $sell->due = 0;
                $sell->status = '1';
            }
            else{ // rest all
                $r['netpaid'] = $r['netpayable'];
                $r['netdue'] = 0;
                $sell->paid = $r['netpayable'];
                $sell->due = 0;
                $sell->status = '1';
            }
            $sell->entry_by = Auth::user()->id;
            $sell->screen_data = $r;
            $sell->save();

            $balance_amount = 0;
            $balance_method = 0;
            // Cash/Partial Cash
            if($r['paymentmethod']['id']==1 || $r['paymentmethod']['id']==2){
                $balance_amount = floatval($r['netpaid']);
                $balance_method = 1;
                DB::table('balance')
                    ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                    ->increment('amount', $balance_amount);
            }
            //City Amex, Bkash, UCB, UKash
            else if($r['paymentmethod']['id']==4 || $r['paymentmethod']['id']==6 || $r['paymentmethod']['id']==9 || $r['paymentmethod']['id']==11){
                $balance_amount = floatval($r['netpaid']);
                $balance_method = 2;
                DB::table('balance')
                    ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                    ->increment('amount', $balance_amount);
            }
            //DBBL, Rocket, Nexus
            else if($r['paymentmethod']['id']==5 || $r['paymentmethod']['id']==7 || $r['paymentmethod']['id']==10){
                $balance_amount = floatval($r['netpaid']);
                $balance_method = 3;
                DB::table('balance')
                    ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                    ->increment('amount', $balance_amount);
            }
            // Dual
            else if($r['paymentmethod']['id']==8){

                if($r['paymentmethod2']['id']==1 || $r['paymentmethod2']['id']==2){ // Cash
                    $balance_amount = floatval($r['dualamount']);
                    $balance_method = 1;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
                // City
                else if($r['paymentmethod2']['id']==4 || $r['paymentmethod2']['id']==6 || $r['paymentmethod2']['id']==9 || $r['paymentmethod2']['id']==11){
                    $balance_amount = floatval($r['dualamount']);
                    $balance_method = 2;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
                // DBBL
                else if($r['paymentmethod2']['id']==4 || $r['paymentmethod2']['id']==6 || $r['paymentmethod2']['id']==9 || $r['paymentmethod2']['id']==11){
                    //$balance_amount = floatval($r['netpayable']) - floatval($r['dualamount']);
                    $balance_amount = floatval($r['dualamount']);
                    $balance_method = 3;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }


                if($r['paymentmethod3']['id']==1 || $r['paymentmethod3']['id']==2){ // Cash
                    $balance_amount = floatval($r['netpayable']) - floatval($r['dualamount']);
                    $balance_method = 1;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
                //City
                else if($r['paymentmethod3']['id']==4 || $r['paymentmethod3']['id']==6 || $r['paymentmethod3']['id']==9 || $r['paymentmethod3']['id']==11){
                    $balance_amount = floatval($r['netpayable']) - floatval($r['dualamount']);
                    $balance_method = 2;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
                //DBBL
                else if($r['paymentmethod2']['id']==4 || $r['paymentmethod2']['id']==6 || $r['paymentmethod2']['id']==9 || $r['paymentmethod2']['id']==11){
                    $balance_amount = floatval($r['netpayable']) - floatval($r['dualamount']);
                    $balance_method = 3;
                    DB::table('balance')
                        ->where(['outlet' => $r['outlet']['id'], 'balance_method' => $balance_method])
                        ->increment('amount', $balance_amount);
                }
            }

            //Transection Log
            $transection_no = $this->getNewTransectionno($r['outlet']['id']);    
            DB::table('balance_transections')->insert(
                [
                    'fy' => date("Y"), 'date' => date("Y-m-d"),'transection_no'=>$transection_no,
                    'outlet' => $r['outlet']['id'], 'amount' => $balance_amount,'type'=>1,'balance_method' =>$balance_method
                ]
            );

            foreach( $sell->items  as $key=>$item){
                // Stock Table
                DB::table('stock')
                    ->where(['product' => $item['pid'], 'outlet' => $r['outlet']['id']])
                    ->decrement('quantity', 1);
                
                // product archive table
                DB::table('product_archive')
                    ->where('id',$item['arcid'])
                    ->update(['status'=>4,'sell_statement'=>$invoice_no,'update_by'=>Auth::user()->id]);
                // Product Table
                DB::table('products')
                    ->where('id',$item['pid'])
                    ->decrement('current_stock', 1);
            }
            

            if($customer_due>0){ // Make Due Entry of Customer
                DB::table('customers')
                    ->where('id', $r['customer']['id'])
                    ->increment('dues', $customer_due);

                // Customer DUE Table
                $cdu = new Customer_due();
                $cdu->fy = date("Y");
                $cdu->customer = $r['customer']['id'];
                $cdu->c_name = $r['customer']['name'];
                $cdu->invoice = $invoice_no;
                $cdu->due = $customer_due;
                $cdu->entry_by = Auth::user()->id;
                $cdu->save();
            }


        });
        $invoiceid = DB::table('sell')->where('invoice_no', $invoice_no)->pluck('id');
        $r['receiptid'] = $invoiceid[0];
        
        return $r;
    }

    public function getInvoiceDetails(Request $r){
        return ModelsSell::where('invoice_no',$r['invoice_no'])->get();
    }

    public function getNewTransectionno($outlet){
        $today = date("Y-m-d");
        $test_inv = $today.'-'.$outlet;
        $statements = Balance_transection::where('transection_no','like', '%'.$test_inv.'%')->get();
        $new = $statements->count() + 1;
        return $test_inv.'-'.$new;
    }

    public function getNewStatementNo($outlet){
        $today = date("Y-m-d");
        $test_inv = $today.'-'.$outlet;
        $statements = ModelsSell::where('invoice_no','like', '%'.$test_inv.'%')->get();
        $new = $statements->count() + 1;
        return $test_inv.'-'.$new;
    }

    public function getReturnStatementNo($outlet){
        $today = date("Y-m-d");
        $test_inv = $today.'-'.$outlet;
        $statements = Preturn::where('receipt_no','like', '%'.$test_inv.'%')->get();
        $new = $statements->count() + 1;
        return $test_inv.'-'.$new;
    }


}
