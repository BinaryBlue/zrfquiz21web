<table style="width:100%;">
       <tr>
              <td style="width:70%">
                     <table style="width: 100%;border: 1px solid; border-collapse: collapse;font-size: 1.0em;">
                            <tr>
                                   <td style="border: 1px solid;background:#dadada:3px;"><h4 style="padding-left:3px;">Report Name</h4></td>
                                   <td style="border: 1px solid;"><h4>{{$type}}  Report</h4></td>
                            </tr>
                            <tr>
                                   <td style="border: 1px solid;background:#dadada;"><h4>Date Range</h4></td>
                                   <td style="border: 1px solid;"><h4>{{$r['from']}} To {{$r['to']}}</h4></td>
                            </tr>
                            <tr>
                                   <td style="border: 1px solid;background:#dadada;"><h4>Reporting Outlet</h4></td>
                                   <td style="border: 1px solid;"><h4>{!! BHelper::outletdetails($r['outlet'])->name !!}</h4></td>
                            </tr>
                            <tr>
                                   <td style="border: 1px solid;background:#dadada;"><h4>Product Filter</h4></td>
                                   <td style="border: 1px solid;"><h4>{{$code}}</h4></td>
                            </tr>
                            <tr>
                                   <td style="border: 1px solid;background:#dadada;"><h4>Printed By</h4></td>
                                   <td style="border: 1px solid;"><h4>USERNAME</h4></td>
                            </tr>
                            <tr>
                                   <td style="border: 1px solid;background:#dadada;"><h4>Printent At</h4></td>
                                   <td style="border: 1px solid;"><h4>{{date("Y-m-d h:i:sa")}}</h4></td>
                            </tr>
                     </table>
              </td>
              <td style="width: 30%;text-align:right;"><img src="{{ public_path('/images/logo_en.png') }}" style="height:100px;width:150px;" /></td>
              
       </tr>
</table>
<br/>
<h3 align="center" style="text-decoration:underline;">Net Revenue</h3>
<table style="border:1px solid; width:100%;border-collapse: collapse;">
       <tr style="border: 1px solid;">
              <td style="border: 1px solid;background:#dadada;font-weight:bold;"></td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Quantity</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Total Products</td>
              <td align="right" style="border: 1px solid;background:#dadada;font-weight:bold;">Amount</td>
       </tr>
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Total Sell</td>
              <td align="center" style="border: 1px solid;">{{$data['qtt']}}</td>
              <td align="center" style="border: 1px solid;">{{$data['barcodes']}}</td>
              <td align="right" style="border: 1px solid;">{{ number_format((int)$data['sell'])  }}/-</td>
       </tr>
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Total Return</td>
              <td align="center" style="border: 1px solid;">{{$data['ret_qtt']}}</td>
              <td align="center" style="border: 1px solid;">{{$data['ret_barcodes']}}</td>
              <td align="right" style="border: 1px solid;">{{number_format($data['ret_total'])}}/-</td>
       </tr>
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Total Discount</td>
              <td align="center" style="border: 1px solid;">{{$data['dis_qtt']}}</td>
              <td align="center" style="border: 1px solid;">-</td>
              <td align="right" style="border: 1px solid;">{{number_format($data['discount'])}}/-</td>
       </tr>
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Net Revenue</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">-</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">{{ (int)$data['barcodes'] - (int)$data['ret_barcodes']  }}</td>
              <td align="right" style="border: 1px solid;background:#dadada;font-weight:bold;">{{ number_format((int)$data['sell'] - (int)$data['discount'] - (int)$data['ret_total'])  }}/-</td>
       </tr>
       
</table>

<h3 align="center" style="text-decoration:underline;">Balance Collection</h3>
<table style="border:1px solid; width:100%;border-collapse: collapse;">
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Balance Method</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Balance In</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Balance Out</td>
              <td align="right" style="border: 1px solid;background:#dadada;font-weight:bold;">Net Balance</td>
       </tr>
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Cash</td>
              <td align="center" style="border: 1px solid;">{{ number_format((float)$data['cash_sell'])  }}/-</td>
              <td align="center" style="border: 1px solid;">{{number_format($data['cash_return'])}}/-</td>
              
              <td align="right" style="border: 1px solid;">{{number_format((float)$data['cash_sell'] - (float)$data['cash_return'])}}/-</td>
       </tr>
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">City Bank</td>
              <td align="center" style="border: 1px solid;">{{number_format($data['city_sell'])}}/-</td>
              <td align="center" style="border: 1px solid;">{{number_format($data['city_return'])}}/-</td>
              <td align="right" style="border: 1px solid;">{{ number_format((float)$data['city_sell'] - (float)$data['city_return'])  }}/-</td>
       </tr>
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">DBBL</td>
              <td align="center" style="border: 1px solid;">{{number_format($data['dbbl_sell'])}}/-</td>
              <td align="center" style="border: 1px solid;">{{number_format($data['dbbl_return'])}}/-</td>
              <td align="right" style="border: 1px solid;">{{ number_format((float)$data['dbbl_sell'] - (float)$data['dbbl_return'])  }}/-</td>
       </tr>
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Net Collection</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">{{ number_format(
                                (float)$data['cash_sell'] + 
                                (float)$data['city_sell'] + 
                                (float)$data['dbbl_sell'] 
                                
                                )  }}/-
                </td>
              
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">{{ number_format((float)$data['cash_return'] + (float)$data['city_return'] + (float)$data['dbbl_return'] )  }}/-</td>
              <td align="right" style="border: 1px solid;background:#dadada;font-weight:bold;">{{ number_format((float)$data['cash_sell'] + (float)$data['city_sell'] + (float)$data['dbbl_sell'] - (float)$data['cash_return'] - (float)$data['city_return'] - (float)$data['dbbl_return'])  }}/-</td>
              
       </tr>
        
</table>
<h3 align="center" style="text-decoration:underline;">Due Amount</h3>
<table style="border:1px solid; width:100%;border-collapse: collapse;">
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Net Revenue</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Net Collection</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Due Amount</td>
       </tr>
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;font-weight:bold;">{{ number_format((int)$data['sell'] - (int)$data['discount'] - (int)$data['ret_total'])  }}/-</td>
              <td align="center" style="border: 1px solid;font-weight:bold;">{{ number_format((float)$data['cash_sell'] + (float)$data['city_sell'] + (float)$data['dbbl_sell'] - (float)$data['cash_return'] - (float)$data['city_return'] - (float)$data['dbbl_return'])  }}/-</td>
              <td align="center" style="border: 1px solid;font-weight:bold;">{{ 
              number_format(
                
                (float)$data['sell'] - (float)$data['discount'] - (float)$data['ret_total'] - ((float)$data['cash_sell'] + (float)$data['city_sell'] + (float)$data['dbbl_sell'] - (float)$data['cash_return'] - (float)$data['city_return'] - (float)$data['dbbl_return'])
              )
              
              
              }}/-</td>
       </tr>
       
       
</table>


<h3 align="center" style="text-decoration:underline;">Digital Payments</h3>
<table style="border:1px solid; width:100%;border-collapse: collapse;">
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Bkash</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Rocket</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">City Amex</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">DBBL</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">UCB</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">Nexus</td>
              <td align="center" style="border: 1px solid;background:#dadada;font-weight:bold;">UKash</td>
              
       </tr>
       <tr style="border: 1px solid;">
              <td align="center" style="border: 1px solid;font-weight:bold;">{{number_format($data['Bkash']) }}/-</td>
              <td align="center" style="border: 1px solid;font-weight:bold;">{{number_format($data['Rocket']) }}/-</td>
              <td align="center" style="border: 1px solid;font-weight:bold;">{{number_format($data['City Amex']) }}/-</td>
              <td align="center" style="border: 1px solid;font-weight:bold;">{{number_format($data['DBBL']) }}/-</td>
              <td align="center" style="border: 1px solid;font-weight:bold;">{{number_format($data['UCB']) }}/-</td>
              <td align="center" style="border: 1px solid;font-weight:bold;">{{number_format($data['Nexus']) }}/-</td>
              <td align="center" style="border: 1px solid;font-weight:bold;">{{number_format($data['UKash']) }}/-</td>
       </tr>
       
       
</table>