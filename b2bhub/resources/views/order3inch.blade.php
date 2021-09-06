<style>
       @page {
              footer: page-footer;
       }
       html, body, div {
              font-family: Arial,blueerp, Helvetica, sans-serif;
            }
</style>
<table style="width: 225px;boder:0px solid;">
       <tr style="width:140px;">
              <td style="font-size:0.9em;padding-top: 0px;padding-bottom: 0px;text-align:center;border-bottom: 1px dashed #727272;">
                     <img style="" src="{{ public_path('/images/logo_bn.png') }}" style="height:100px;width:140px;" />
                     <p style="font-size:0.8em;">{{ $outlet->description }}</p>
                     <p style="font-size:0.8em;">{{ $outlet->description2 }}</p>
                     <p style="font-size:0.8em;">Vat Reg. No : 912119691</p>
                     <p style="font-size:0.8em;">Contact : {{ $outlet->mobile }}</p>
              </td>
              
       </tr>
       <tr>
           <td style="text-align:center;">
                     <barcode size="0.75" code="{{ $order->order_no }}" type="C128A"/>
                     <h3 style="font-size:0.9em;">Invoice # {{ $order->order_no }}</h3>
            </td>
       </tr>
       <tr>
              <td style="text-align: center;">
                     <div style="text-align: left;font-size:0.80em;">
                            Name: {{$customer->name}}<br/>
                            Mobile: {{$customer->mobile}}<br/>
                            Address: {{$customer->address}}
                     </div>
                     <table style="width:95%;border: 1px solid #727272;border-collapse: collapse;font-size:0.8em; ">
                     	
                            <tr style="border: 1px solid #727272; background: #f1f2f3;" >
                                   <td style="border: 1px solid #727272;font-size:0.8em;">SL</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">Barcode</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">Name</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">Amount</td>
                            </tr>
                            <?php $sub = 0; ?>
                            @foreach($order->items as $key => $value)
                            <?php $sub += (float)$value['price']; ?>
                            <tr style="border-bottom: 1px solid #727272;font-size:0.8em;">
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$key+1}}</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$value['barcode']}}</td>
                                   <td style="border: 1px solid #727272;font-size:0.8em;">{{$value['name']}}</td>
                                   <td style="border: 1px solid #727272;text-align: right;font-weight:bold;font-size:0.8em;">{{$value['price']}}/-</td>
                            </tr>       
                            @endforeach

                            

                            @if($order->discount > 0)
                            <tr style="border-bottom: 1px solid #727272;font-size:0.8em;">
                                   <td style="border-bottom: 1px solid #727272;font-size:0.8em;"  colspan="3">Discount</td>
                                   <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold;font-size:0.8em;">{{$order->discount}}/-</td>
                            </tr>
                            @endif
                            @if($order->vat>0)
                            <tr style="border-bottom: 1px solid #727272;font-size:0.8em;">
                                   <td style="border-bottom: 1px solid #727272;font-size:0.8em;"  colspan="3"> Vat </td>
                                   <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold;font-size:0.8em;">{{$order->vat}}/-</td>
                            </tr>
                            @endif
                            @if($order->delivery_fee>0)
                            <tr style="border-bottom: 1px solid #727272;font-size:0.8em;">
                                   <td style="border-bottom: 1px solid #727272;font-size:0.8em;"  colspan="3"> Delivery Fee </td>
                                   <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold;font-size:0.8em;">{{$order->delivery_fee}}/-</td>
                            </tr>
                            @endif
                            <tr style="border-bottom: 1px solid #727272;font-size:0.8em;">
                                   <td style="border-bottom: 1px solid #727272;font-size:0.8em;" colspan="3">Net Payable</td>
                                   <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold;font-size:0.8em;">{{$order->net_payable}}/-</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #727272;font-size:0.8em;">
                                   <td style="border-bottom: 1px solid #727272;font-size:0.8em;"  colspan="3">Paid</td>
                                   <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold;font-size:0.8em;">{{$order->paid}}/-</td>
                            </tr>
                            @if(($order->net_payable - $order->paid)>0)
                            <tr style="border-bottom: 1px solid #727272;font-size:0.8em;">
                                   <td style="border-bottom: 1px solid #727272;font-size:0.8em;" colspan="3">Due</td>
                                   <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold;font-size:0.8em;">{{$order->net_payable - $order->paid}}/-</td>
                            </tr>
                            @endif
                            @if($order->remarks!='')
                            <tr style="border-bottom: 1px solid #727272;font-size:0.8em;">
                                   <td style="border-bottom: 1px solid #727272;font-size:0.8em;font-weight:bold;" >Remarks:</td>
                                   <td style="border-bottom: 1px solid #727272;text-align: left;font-size:0.8em;" colspan="3">{{$order->remarks}}</td>
                            </tr>
                            @endif
                           
                     </table>
              </td>       
       </tr>
       <tr>
              <td>
                     <h5 style="font-size: 0.6em;">Terms & Condition</h5>
                     <p style="font-size: 0.6em;">1. It is required to show invoice at the time of any replacement it must be within 15 days from purchase.</p>
                     <p style="font-size: 0.6em;">2. No money refund.</p>
                     
              </td>
       </tr>
       <tr>
              <td style="text-align: center;font-size:0.6em;">Thank You. Please Visit Again</td>
       </tr>
       <tr>
              <td style="text-align: right;padding-top:20px;border-bottom:1px dashed;font-size:0.7em;">Served By : {{$creator->username}}</td>
       </tr>
       <tr>
              <td style="text-align: center;font-size:0.7em;">
                     <p>Software Powered By : Binary Blue</p>
                     <p>www.binarybluebd.com</p>
              </td> 
       </tr>
       
</table>
