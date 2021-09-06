<style>
       @page {
              footer: page-footer;
       }
       html, body, div {
              font-family: Arial,blueerp, Helvetica, sans-serif;
            }
</style>

{{--  <htmlpagefooter name="page-footer">
	<table style="width:100%;text-align:center;font-size:0.8em;">
              <tr>
                     <td>
                            <strong>Head Office</strong><br/>
                            House : 40, Bosila, Mohammadpur, Dhaka - 1207<br/>
                            +88 0155 310 0040, +88 02 48121025
                     </td>
                     <td>
                            <strong>Metro Shopping Mall</strong> <br/>
                            Shop # 311, Dhanmondi 31, Dhaka - 1209 <br/>
                            +88 01686 824 141
                     </td>
                     <td>
                            <strong>Orchid Plaza</strong><br/>
                            Shop # 31, Dhanmondi 28, Dhaka - 1209 <br/>
                            +88 01714 774 813, +88 01911 951 089
                     </td>
                     <td>
                            <strong>Savar City Center</strong> <br/>
                            Shop # 1030,Savar Dhaka-1340 <br/>
                            +88 01313 012 206, +88 01991 122 442
                     </td>
                     <td>
                            <strong>North Tower</strong> <br/>
                            Shop # 410, Sector # 07, Uttara Dhaka-1230 <br/>
                            +88 0178 500 557, +88 01720 015 332
                     </td>
              </tr>
       </table>
</htmlpagefooter>  --}}
<table style="width: 100%;">
       <tr>
              <td  align="left">
                     <img style="" src="{{ public_path('/images/shopnodip.jpg') }}" style="height:130px;" />
              </td>
              <td  align="center">
                     
              </td>
              
              
              <td  align="right">
                     <img style="border:2px solid;" src="{{ public_path('order_doc/'.$order->order_no.'.png') }}" />
              </td>
       </tr>
</table>
{{--  <table style="width: 100%;">
       <tr>
              <td><h3 style="text-decoration: underline;">Order Invoice</h3> </td>
              <td></td>
              <td align="center" style="font-size: 1.05em;width:150px;"><strong>{{$order->order_no}}</strong></td>
       </tr>
</table>  --}}
<table style="width: 100%;">
       <tr>
              <td align="center"><h2 style="text-decoration: underline;text-align:center;">Sell Invoice</h2> </td>
       </tr>
       <tr>
              <td style="text-align:center;">
                        <barcode size="0.75" code="{{ $order->order_no }}" type="C128A"/>
                        <h3 style="font-size:0.9em;">Invoice # {{ $order->order_no }}</h3>
               </td>
          </tr>
</table>
<table style="width: 100%;">
       <tr>
              <td style="width:75%">
                     <table style="width:100%;border: 1px solid #727272;border-collapse: collapse;">
                            <tr style="border: 1px solid #727272;">
                                   <td style="border: 1px solid #727272; background: #eceaec;padding:3px;">Invoice Number</td>
                                   <td><strong>{{$order->order_no}}</strong></td>
                            </tr>
                            <tr style="border: 1px solid #727272;">
                                   <td style="border: 1px solid #727272; background: #eceaec;padding:3px;">Status</td>
                                   <td><strong>{{$order->status}}</strong></td>
                            </tr>
                            <tr style="border: 1px solid #727272;">
                                   <td style="border: 1px solid #727272; background: #eceaec;padding:3px;">Customer Name</td>
                                   <td style="font-family:Arial, blueerp;">{{$customer->name}} </td>
                            </tr>
                            <tr>
                                   <td style="border: 1px solid #727272; background: #eceaec;padding:3px;">Mobile </td>
                                   <td style="border: 1px solid #727272; ">{{$customer->mobile}}</td>
                            </tr>
                            <tr>
                                   <td style="border: 1px solid #727272; background: #eceaec;padding:3px;">Delivery Address</td>
                                   <td style="border: 1px solid #727272; font-family:Arial, blueerp;">{{$customer->address}}</td>
                            </tr>
                            <tr>
                                   <td style="border: 1px solid #727272; background: #eceaec;padding:3px;">Print Time</td>
                                   <td style="border: 1px solid #727272; ">{{date('Y-m-d H:i:s')}}</td>
                            </tr>
                     </table>
              </td>
              <td>
                     
              </td>
              
       </tr>
</table>
       {{--  <tr>
              <td style="font-size:0.9em;padding-top: 0px;padding-bottom: 0px;text-align:center;border-bottom: 1px dashed #727272;">
                     
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
       </tr>  --}}
<table style="width:100%;border-collapse: collapse;">
       <tr style="border: 1px solid #727272; background: #f1f2f3;" >
              <td style="border: 1px solid #727272;width:50px;text-align:center;">SL</td>
              <td style="border: 1px solid #727272;width:100px;text-align:center;">Brand</td>
              <td style="border: 1px solid #727272;width:100px;text-align:center;">Category</td>
              <td style="border: 1px solid #727272;width:150px;text-align:center;">Code</td>
              <td style="border: 1px solid #727272;width:80px;text-align:center;">Size</td>
              <td style="border: 1px solid #727272;width:80px;text-align:center;">MRP</td>
              <td style="border: 1px solid #727272;width:80px;text-align:center;">Discount</td>
              <td style="border: 1px solid #727272;width:80px;text-align:center;">Price</td>
              <td style="border: 1px solid #727272;width:80px;text-align:center;">Quantity</td>
              <td style="border: 1px solid #727272;text-align:right;width:80px;">Payable</td>
       </tr>
</table>
<?php
       $item_total = $order->net_payable + $order->discount - $order->delivery_fee - $order->vat;
       $perc = $order->discount/$item_total;
?>
<?php $sub = 0; ?>
@foreach($order->items as $key => $value)
<?php $sub += (float)$value['price']; ?>
       <table style="width: 100%;border-collapse: collapse;">
              <tr style="border-bottom: 1px solid #727272;">
                     <td style="border: 1px solid #727272;width:50px;text-align:center;">{{($key + 1)}}</td>
                     <td style="border: 1px solid #727272;width:100px;text-align:center;">{{$value['brand']}}</td>
                     <td style="border: 1px solid #727272;width:100px;text-align:center;">{{$value['category']}}</td>
                     <td style="border: 1px solid #727272;width:150px;text-align:center;">{{$value['code']}}</td>
                     <td style="border: 1px solid #727272;width:80px;text-align:center;">{{$value['size']}}</td>
                     <td style="border: 1px solid #727272;width:80px;text-align: center;font-weight:bold;">{{$value['price']}}/-</td>
                     <td style="border: 1px solid #727272;width:80px;text-align: center;font-weight:bold;">{{$value['disc']}}/-</td>
                     <td style="border: 1px solid #727272;width:80px;text-align: center;font-weight:bold;">{{$value['price'] - $value['disc']}}/-</td>
                     <td style="border: 1px solid #727272;width:80px;text-align: center;font-weight:bold;">{{$value['qtt']}}</td>
                     <td style="border: 1px solid #727272;width:80px;text-align: right;font-weight:bold;">{{ ($value['qtt']* ($value['price'] - $value['disc'])) }}/-</td>
              </tr>       
       </table>
       @if($key == 14)
              <div style="page-break-after: always;">&nbsp;</div>
       @endif
@endforeach

<table style="border: 1px solid #727272;border-collapse: collapse; width:100%;">
       <tr style="border-bottom: 1px solid #727272;">
              <td style="border-bottom: 1px solid #727272;"  colspan="3">Item Total</td>
              <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold;">{{number_format($item_total)}}/-</td>
       </tr>
       @if($order->discount > 0)
       <tr style="border-bottom: 1px solid #727272;">
              <td style="border-bottom: 1px solid #727272;"  colspan="3">Discount (Round Figure)</td>
              <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold;">{{number_format($order->discount)}}/-</td>
       </tr>
       @endif
       @if($order->vat>0)
       <tr style="border-bottom: 1px solid #727272;">
              <td style="border-bottom: 1px solid #727272;s"  colspan="3"> Vat </td>
              <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold; ">  {{$order->vat}}/-</td>
       </tr>
       @endif
       @if($order->delivery_fee>0)
       <tr style="border-bottom: 1px solid #727272; ">
              <td style="border-bottom: 1px solid #727272; "  colspan="3"> Delivery Fee </td>
              <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold; ">{{$order->delivery_fee}}/-</td>
       </tr>
       @endif
       <tr style="border-bottom: 1px solid #727272; ">
              <td style="border-bottom: 1px solid #727272; " colspan="3">Net Payable </td>
              <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold; ">{{number_format($order->net_payable)}}/-</td>
       </tr>
       <tr style="border-bottom: 1px solid #727272; ">
              <td style="border-bottom: 1px solid #727272; ">In Words</td>
              <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold; " colspan="3">{{  ucfirst(BHelper::inWords(ceil($order->net_payable)))  }} Taka</td>
       </tr>
       
       <tr style="border-bottom: 1px solid #727272; ">
              <td style="border-bottom: 1px solid #727272; "  colspan="3">Paid</td>
              <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold; ">{{number_format($order->paid)}}/-</td>
       </tr>
       @if(($order->net_payable - $order->paid)>0)
       <tr style="border-bottom: 1px solid #727272; ">
              <td style="border-bottom: 1px solid #727272; " colspan="3">Due</td>
              <td style="border-bottom: 1px solid #727272;text-align: right;font-weight:bold; ">{{number_format(($order->net_payable - $order->paid))}}/-</td>
       </tr>
       @endif
       @if($order->remarks!='')
       <tr style="border-bottom: 1px solid #727272; ">
              <td style="border-bottom: 1px solid #727272; font-weight:bold;" >Remarks:</td>
              <td style="border-bottom: 1px solid #727272;text-align: left;font-weight:600;font-family:blueerp " colspan="3">{{$order->remarks}}</td>
       </tr>
       @endif
      
</table>
<table style="width: 100%;">
       <tr>
              <td>
                     <h5 >Terms & Condition</h5>
                     <p >1. Price excluding Carrying, Vat, Tax & Ait.</p>
                     <p >2. Delivery After cheque clearing.</p>
                     
              </td>
       </tr>
       <tr>
              <td style="text-align: right;padding-top:20px;border-bottom:1px dashed;"><br/><br/>Served By : {{$creator->username}}</td>
       </tr>
       <tr>
              <td style="text-align: center;">
                     <p>A Software as a Service (SaaS) of : B2B Solver</p>
                     <p>www.b2bsolver.com</p>
              </td>
       </tr>
       
</table>
