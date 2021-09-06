<table style="width:100%;">
       <tr>
              <td style="width:70%">
                     <table style="width: 100%;border: 1px solid; border-collapse: collapse;font-size: 1.0em;">
                            <tr>
                                   <td style="border: 1px solid;background:#dadada:3px;"><h4 style="padding-left:3px;">Report Name</h4></td>
                                   <td style="border: 1px solid;"><h4>{{$type}} Sell Report</h4></td>
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
<br/>
@if($type=='Barcode')
<table style="border:1px solid; width:100%;border-collapse: collapse;">
       <tr style="border: 1px solid;">
              <td style="border: 1px solid;background:#dadada;">SL</td>
              <td style="border: 1px solid;background:#dadada;">Date</td>
              <td style="border: 1px solid;background:#dadada;">Product</td>
              <td style="border: 1px solid;background:#dadada;">Name</td>
              <td style="border: 1px solid;background:#dadada;">Barcode</td>
              
              <td style="border: 1px solid;background:#dadada;">Customer</td>
              <td style="border: 1px solid;background:#dadada;">Invoice</td>
              <td style="border: 1px solid;background:#dadada;">Price</td>
       </tr>
       <?php $serial = 1; $btotal=0; ?>
       @foreach($sells as $key => $sell)
              @foreach($sell->items as $k => $itm)
                     @if ($code=='All' || (strpos($itm['code'], $code) === 0))
                     <tr style="border: 1px solid;">
                            <td style="border: 1px solid;">{{$serial++}}</td>
                            <td style="border: 1px solid;">{{$sell->date}}</td>
                            <td style="border: 1px solid;">{{$itm['code']}}</td>
                            <td style="border: 1px solid;">{{$itm['name']}}</td>
                            <td style="border: 1px solid;">{{$itm['barcode']}}</td>
                            
                            <td>{!! BHelper::customerdetails($sell->customer)->mobile !!}</td>
                            <td style="border: 1px solid;">{{$sell->invoice_no}}</td>
                            <td style="border: 1px solid;">{{$itm['price']}}/-</td>
                            <?php $btotal += (float)$itm['price']; ?>
                     </tr> 
                     @endif                   
              @endforeach
       @endforeach
       <tr style="border: 1px solid;">
              <td style="border: 1px solid;background:#dadada;" colspan="7">Total</td>
              <td style="border: 1px solid;background:#dadada;">{{$btotal}}/-</td>
       </tr> 
</table>
@endif

@if($type=='Complete')
<table style="border:1px solid; width:100%;border-collapse: collapse;">
       <tr style="border: 1px solid;">
              <td style="border: 1px solid;background:#dadada;">SL</td>
              <td style="border: 1px solid;background:#dadada;">Date</td>
              <td style="border: 1px solid;background:#dadada;">Order</td>
              <td style="border: 1px solid;background:#dadada;">Invoice No.</td>
              <td style="border: 1px solid;background:#dadada;">Customer</td>
              <td style="border: 1px solid;background:#dadada;">Items</td>
       </tr>
       <?php $ctotal = 0;$dtotal=0; ?>
       @foreach($sells as $key => $sell)
       <tr style="border: 1px solid #727272;font-size:0.8em;">
              <td style="border: 1px solid #727272;font-size:0.8em;">{{$key+1}}</td>
              <td style="border: 1px solid #727272;font-size:0.8em;">{{$sell->date}}</td>
              <td style="border: 1px solid #727272;font-size:0.8em;">{{$sell->order_no}}</td>
              <td style="border: 1px solid #727272;font-size:0.8em;">{{$sell->invoice_no}}</td>
              <td>{!! BHelper::customerdetails($sell->customer)->mobile !!}</td>
              <td style="border:1px solid;">
                     <table style="width: 100%;border-collapse: collapse;">
                            <tr style="border: 1px solid;background:#dadada;">
                                   <td style="border:1px solid;">SL</td>
                                   <td style="border:1px solid;">Code</td>
                                   <td style="border:1px solid;">Barcode</td>
                                   <td style="border:1px solid;">Name</td>
                                   <td style="border:1px solid;">Price</td>
                            </tr>
                            <?php $sub=0; ?>
                            @foreach($sell->items as $k => $itm)
                            <?php 
                                   $sub += (float)$itm['price'];
                                   //$ctotal += (float)$itm['price'];
                                   
                             ?>
                            <tr>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$k+1}}</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$itm['code']}}</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$itm['barcode']}}</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$itm['name']}}</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$itm['price']}}/-</td>
                            </tr>
                            @endforeach
                            <tr>
                                   <td colspan="4" style="border: 1px solid #727272;font-size:0.8em;">Sub Total</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$sub}}/-</td>
                                   <?php $ctotal += (float)$sub ?>
                            </tr>
                            @if($sell->discount>0)
                            <tr>
                                   <td colspan="4" style="border: 1px solid #727272;font-size:0.8em;">Discount</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$sell->discount}}/-</td>
                                   <?php 
                                   $dtotal += (float)$sell->discount;
                                   
                             ?>
                            </tr>
                            @endif
                            @if($sell->vat>0)
                            <tr>
                                   <td colspan="4" style="border: 1px solid #727272;font-size:0.8em;">Vat</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$sell->vat}}/-</td>
                            </tr>
                            @endif
                            <tr>
                                   <td colspan="4" style="border: 1px solid #727272;font-size:0.8em;">Paid</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$sell->paid}}/-</td>
                            </tr>
                            @if($sell->due>0)
                            <tr>
                                   <td colspan="4" style="border: 1px solid #727272;font-size:0.8em;">Due</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$sell->due}}/-</td>
                            </tr>
                            @endif
                     </table>
              </td>
       </tr>       
       @endforeach
       <tr style="border: 1px solid;">
              <td style="border: 1px solid;background:#dadada;" colspan="5">Total Sell</td>
              <td style="border: 1px solid;background:#dadada;">{{number_format($ctotal)}}/-</td>
       </tr> 
       <tr style="border: 1px solid;">
              <td style="border: 1px solid;background:#dadada;" colspan="5">Total Discount</td>
              <td style="border: 1px solid;background:#dadada;">{{number_format($dtotal)}}/-</td>
       </tr>
       
</table>
@endif