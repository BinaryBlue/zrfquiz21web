<?php

namespace App\Http\Controllers\api\v1\pdf;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Sell as Modelsell;
use App\Models\Product_return as Modelreturn;


use PDF;

class DownloadtController extends Controller
{
    public function sellreport(Request $request){
        //$r = $request['data'];
        $r['outlet'] = $request['outlet'];
        $r['from'] = $request['from'];
        $r['to'] = $request['to'];
        if($request['product']=='') $r['product'] = 'All';
        else $r['product'] = $request['product'];
        $r['type'] = $request['type'];
        //return $r;
        $pdf = null;
        if($r['type']=='Complete'){
            $sells = Modelsell::where('outlet',$r['outlet'])
                    ->whereBetween('date', [$r['from'], $r['to']])
                    ->get();
            $data = [ 'sells' => $sells,'type' => $r['type'],'code' => 'All','r'=>$r];
            $pdf = PDF::loadView('sellreport', $data,[],[
                'format' => 'A4',
                ]);
        }
        else if($r['type']=='Barcode'){
            $sells = Modelsell::where('outlet',$r['outlet'])
                    ->whereBetween('date', [$r['from'], $r['to']])
                    ->get();
            $data = [ 'sells' => $sells,'type' => $r['type'],'code' => $r['product'],'r'=>$r];
            $pdf = PDF::loadView('sellreport', $data,[],[
                'format' => 'A4',
                ]);
        }
        
        return $pdf->stream($r['from'].' to '.$r['to'].'-'.$r['type'].'-sell-report.pdf');
        
    }

    public function returnreport(Request $request){
        //$r = $request['data'];
        $r['outlet'] = $request['outlet'];
        $r['from'] = $request['from'];
        $r['to'] = $request['to'];
        if($request['product']=='') $r['product'] = 'All';
        else $r['product'] = $request['product'];
        $r['type'] = $request['type'];
        //return $r;
        $pdf = null;
        if($r['type']=='Complete'){
            $sells = Modelreturn::where('outlet',$r['outlet'])
                    ->whereBetween('date', [$r['from'], $r['to']])
                    ->get();
            $data = [ 'returns' => $sells,'type' => $r['type'],'code' => 'All','r'=>$r];
            $pdf = PDF::loadView('returnreport', $data,[],[
                'format' => 'A4',
                ]);
        }
        else if($r['type']=='Barcode'){
            $sells = Modelreturn::where('outlet',$r['outlet'])
                    ->whereBetween('date', [$r['from'], $r['to']])
                    ->get();
            $data = [ 'returns' => $sells,'type' => $r['type'],'code' => $r['product'],'r'=>$r];
            $pdf = PDF::loadView('returnreport', $data,[],[
                'format' => 'A4',
                ]);
        }
        
        return $pdf->stream($r['from'].' to '.$r['to'].'-'.$r['type'].'-return-report.pdf');
        
    }

    public function finalreport(Request $request){
        //$r = $request['data'];
        $r['outlet'] = $request['outlet'];
        $r['from'] = $request['from'];
        $r['to'] = $request['to'];
        if($request['product']=='') $r['product'] = 'All';
        else $r['product'] = $request['product'];
        $r['type'] = $request['type'];
        //return $r;
        
        $this->calculatePriceTotal();
        
        $pdf = null;
        
        $rt = DB::table('sell')
                        ->select(DB::raw('SUM(price_total) as total, SUM(discount) as discount, SUM(payable) as net, COUNT(id) as qtt, SUM(JSON_LENGTH(items)) as barcodes'))
                        ->where('outlet',$r['outlet'])
                        ->whereBetween('date', [$r['from'], $r['to']])
                        ->get();
        $ret['sell'] = $rt[0]->total;
        $ret['discount'] = $rt[0]->discount; 
        $ret['net'] = $rt[0]->net; 
        $ret['qtt'] = $rt[0]->qtt; 
        $ret['barcodes'] = $rt[0]->barcodes; 
        
        $rt = DB::table('sell')
                        ->select(DB::raw('COUNT(id) as dis_qtt'))
                        ->where('discount','>',0)
                        ->where('outlet',$r['outlet'])
                        ->whereBetween('date', [$r['from'], $r['to']])
                        ->get();
        $ret['dis_qtt'] = $rt[0]->dis_qtt;
        $rt = DB::table('product_returns')
                        ->select(DB::raw('SUM(amount) as ret_total, COUNT(id) as ret_qtt, SUM(JSON_LENGTH(items)) as ret_barcodes'))
                        ->where('outlet',$r['outlet'])
                        ->whereBetween('date', [$r['from'], $r['to']])
                        ->get();
        $ret['ret_total'] = $rt[0]->ret_total;
        $ret['ret_qtt'] = $rt[0]->ret_qtt;
        $ret['ret_barcodes'] = $rt[0]->ret_barcodes;
        
        /// End of Net Revenue
        
        $rt = DB::table('balance_transections')
                        ->select(DB::raw('SUM(amount) as cash_sell'))
                        ->where('outlet',$r['outlet'])
                        ->where('balance_method',1)
                        ->where('type',1)
                        ->whereBetween('date', [$r['from'], $r['to']])
                        ->get();
        $ret['cash_sell'] = $rt[0]->cash_sell;
        $rt = DB::table('balance_transections')
                        ->select(DB::raw('SUM(amount) as cash_return'))
                        ->where('outlet',$r['outlet'])
                        ->where('balance_method',1)
                        ->where('type',-1)
                        ->whereBetween('date', [$r['from'], $r['to']])
                        ->get();
        $ret['cash_return'] = $rt[0]->cash_return;
        
        $rt = DB::table('balance_transections')
                        ->select(DB::raw('SUM(amount) as city_sell'))
                        ->where('outlet',$r['outlet'])
                        ->where('balance_method',2)
                        ->where('type',1)
                        ->whereBetween('date', [$r['from'], $r['to']])
                        ->get();
        $ret['city_sell'] = $rt[0]->city_sell;
        
        $rt = DB::table('balance_transections')
                        ->select(DB::raw('SUM(amount) as city_return'))
                        ->where('outlet',$r['outlet'])
                        ->where('balance_method',2)
                        ->where('type',-1)
                        ->whereBetween('date', [$r['from'], $r['to']])
                        ->get();
        $ret['city_return'] = $rt[0]->city_return;
        
        $rt = DB::table('balance_transections')
                        ->select(DB::raw('SUM(amount) as dbbl_sell'))
                        ->where('outlet',$r['outlet'])
                        ->where('balance_method',3)
                        ->where('type',1)
                        ->whereBetween('date', [$r['from'], $r['to']])
                        ->get();
        $ret['dbbl_sell'] = $rt[0]->dbbl_sell;
        
        $rt = DB::table('balance_transections')
                        ->select(DB::raw('SUM(amount) as dbbl_return'))
                        ->where('outlet',$r['outlet'])
                        ->where('balance_method',3)
                        ->where('type',-1)
                        ->whereBetween('date', [$r['from'], $r['to']])
                        ->get();
        $ret['dbbl_return'] = $rt[0]->dbbl_return;
        
        
        //// Payment method wise collection
        $ret['Bkash'] = 0.0;
        $ret['Rocket'] = 0.0;
        $ret['City Amex'] = 0.0;
        $ret['DBBL'] = 0.0;
        $ret['UCB'] = 0.0;
        $ret['Nexus'] = 0.0;
        $ret['UKash'] = 0.0;
        $sells = DB::table('sell')->where('payment_method','>',1)
                        ->where('outlet',$r['outlet'])
                        ->whereBetween('date', [$r['from'], $r['to']])
                        ->get();
        foreach($sells as $sell){
            if($sell->payment_method==4){
                $ret['Bkash'] += $sell->paid;
            }
            else if($sell->payment_method==5){
                $ret['Rocket'] += $sell->paid;
            }
            else if($sell->payment_method==6){
                $ret['City Amex'] += $sell->paid;
            }
            else if($sell->payment_method==7){
                $ret['DBBL'] += $sell->paid;
            }
            else if($sell->payment_method==9){
                $ret['UCB'] += $sell->paid;
            }
            else if($sell->payment_method==10){
                $ret['Nexus'] += $sell->paid;
            }
            else if($sell->payment_method==11){
                $ret['UKash'] += $sell->paid;
            }
            else if($sell->payment_method==8){
                /// For Dual
                if($sell->payment_method2==4){
                    $ret['Bkash'] += $sell->paid2;
                }
                else if($sell->payment_method2==5){
                    $ret['Rocket'] += $sell->paid2;
                }
                else if($sell->payment_method2==6){
                    $ret['City Amex'] += $sell->paid2;
                }
                else if($sell->payment_method2==7){
                    $ret['DBBL'] += $sell->paid2;
                }
                else if($sell->payment_method2==9){
                    $ret['UCB'] += $sell->paid2;
                }
                else if($sell->payment_method2==10){
                    $ret['Nexus'] += $sell->paid2;
                }
                else if($sell->payment_method2==11){
                    $ret['UKash'] += $sell->paid2;
                }
                
                if($sell->payment_method3==4){
                    $ret['Bkash'] += $sell->paid3;
                }
                else if($sell->payment_method3==5){
                    $ret['Rocket'] += $sell->paid3;
                }
                else if($sell->payment_method3==6){
                    $ret['City Amex'] += $sell->paid3;
                }
                else if($sell->payment_method3==7){
                    $ret['DBBL'] += $sell->paid3;
                }
                else if($sell->payment_method3==9){
                    $ret['UCB'] += $sell->paid3;
                }
                else if($sell->payment_method3==10){
                    $ret['Nexus'] += $sell->paid3;
                }
                else if($sell->payment_method3==11){
                    $ret['UKash'] += $sell->paid3;
                }
            }
        }
        //dd($ret);
        $data = [ 'data' => $ret,'type' => $r['type'],'code' => 'All','r'=>$r];
        $pdf = PDF::loadView('finalreport', $data,[],[
                'format' => 'A4',
                ]);
        
        return $pdf->stream($r['from'].' to '.$r['to'].'-'.$r['type'].'-return-report.pdf');
        
    }
    
    public function calculatePriceTotal(){
        $sells = Modelsell::where('price_total',0)->get();
        foreach($sells as $s){
            $sid = $s->id;
            $price_total = 0;
            foreach($s->items as $item){
                $price_total += (float)$item['price'];
            }
            DB::table('sell')
              ->where('id', $sid)
              ->update(['price_total' => $price_total]);
        }
    }
}
