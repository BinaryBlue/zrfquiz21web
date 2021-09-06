<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use App\Models\Stock;
use App\Models\Stockentry;
use App\Models\Stocktransfer;
use App\Models\Productarchive;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Supplierbill;
use App\Models\old_stock_entry;
use App\Models\Notification;

use App\Models\old_Item_Information as Olditem;
use App\Models\old_t_price as Oldprice;


use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BlueerpPusher implements ShouldBroadcast {
    public $message;
    public $url;
    public $user;

    public function __construct($message,$url,$uid)
    {
        $this->message = $message;
        $this->url = $url;
        $this->user = $uid;
    }
  
    public function broadcastOn()
    {
        $channel = 'blueerp-channel-'.$this->user;
        return [$channel];
    }
  
    public function broadcastAs()
    {
        return 'blueerp-event';
    }
}

class StockController extends Controller
{
    public function test(){
        DB::table('balance')
        ->where(['outlet' => 2, 'balance_method' => 1])
        ->increment('amount', -1);

        
    }
    public function oldentry(Request $req){
        DB::transaction(function () use ($req) {
            foreach($req['itemList'] as $item){
                // Stock Table
                $stocks = Stock::where(['product' => $item['id'], 'outlet' => $req['outlet']['id']])->get();
                if($stocks->count() > 0){
                    DB::table('stock')->where(['product' => $item['id'], 'outlet' => $req['outlet']['id']])
                                      ->increment('quantity', $item['qtt']);
                }
                else{
                    $nstock = new Stock();
                    $nstock->product = $item['id'];
                    $nstock->outlet = $req['outlet']['id'];
                    $nstock->quantity = (int) $item['qtt'];
                    $nstock->entry_by = Auth::user()->id;
                    $nstock->save();
                }
                //Product Archive Table
                for($i=0;$i<$item['qtt'];$i++){
                    $product = new Productarchive;
                    $product->product_id = $item['id'];
                    $product->name = $item['name'];
                    $product->fy = date("Y");
                    $product->barcode = $this->getBarcodeNumber($item['code']);
                    $product->code = $item['code'];
                    $product->price = $item['price'];
                    $product->purchase = round(((int)$item['price']*(100-(float)$req['profit']))/100,2);
                    //$product->purchase = round(((int)$item['price']*100)/(100+(float)$req['profit']),2);
                    $product->outlet_id = $req['outlet']['id'];
                    $product->outlet_name = $req['outlet']['name'];
                    $product->status = 3;
                    $product->ptype = 0;
                    $product->entry_by = Auth::user()->id;
                    $product->save();
                }

                // Product Table
                
                DB::table('products')->where(['id' => $item['id']])
                                      ->increment('current_stock', $item['qtt']);

            }

            $oldentry = new old_stock_entry();
            $oldentry->items = $req['itemList'];
            $oldentry->total = $req['total'];
            $oldentry->entry_by = Auth::user()->id;
            
            $oldentry->save();


        });
        return  ['status'=>'success', 'message'=>'Old Entry Has Been Confirmed'];
    }
    public function entry(Request $request){
        $statement = $this->getNewStatementNo();
        $entry = new Stockentry;
        //$this->array_sort_by_column($request['itemList'], 'supplierId');
        DB::transaction(function () use ($request,$statement,$entry) {
            $entry->fy = date("Y");
            $entry->statement = $statement;
            $entry->total = $request['total'];
            $entry->items = $request['itemList'];
            $entry->entry_by = Auth::user()->id;
            $entry->save();
            // Product process
            foreach($request['itemList'] as $item){
                $this->processSingleItem($item,$statement);
            }
            // Supplier Process
            $this->supplierBillprocess($request['itemList'],$statement );
        });
        return ['status'=>'success','data'=>['statement'=>$statement,'id'=>$entry->id],'message'=>'New Stock Entry '.$statement.' Has Been Confirmed'];
    }

    public function entryrollback($id)
    {
        DB::transaction(function () use ($id) {
            $entry = Stockentry::find($id);
            $items = $entry->items;
            $stt = $entry->statement;
            $entry->delete();

            foreach($items  as $item){
                $qtt = (int)$item['qtt'];
                 // Stock Table Update
                
                DB::table('stock')->where(['product' => $item['productId'], 'outlet' => 1])
                                      ->decrement('quantity',$qtt);
                
                // Remove from Product Archive Table
                Productarchive::where('entry_statement',$stt)->delete();

                // Product Table
                
                DB::table('products')->where(['id' => $item['productId']])
                                      ->decrement('current_stock',$qtt);

                // Supplier Table Update
                $bill = ((int) $item['qtt']*(float)$item['purchase']);
                DB::table('supplier')->where(['id' => $item['supplierId']])->decrement('dues', $bill);
            }
            // Supplier Bill Table Delete
            Supplierbill::where('stock_entry_statement',$id)->delete();
 
            
        });
        
    }

    public function transferinit(Request $request){
        //return $request;
        $transfercode = $this->getNewTransferNo($request['fromOutlet'],$request['toOutlet']);
        $entry = new Stocktransfer();
        DB::transaction(function () use ($request,$transfercode,$entry) {
            // Stock Transfer Table
            $entry->fy = date("Y");
            $entry->transfer_code = $transfercode;
            $entry->transfer_from = $request['fromOutlet'];
            $entry->transfer_to = $request['toOutlet'];
            $entry->from_name = $request['fromName'];
            $entry->to_name = $request['toName'];
            $entry->items = $request['itemList'];
            $entry->status = '0';
            $entry->initiated_by = Auth::user()->id;
            $entry->initiated_name = Auth::user()->first_name.' '.Auth::user()->last_name;
            $entry->initiated_date = date("Y-m-d"); 
            $entry->entry_by = Auth::user()->id;
            $entry->save();

            // Stock
            foreach($request['itemList'] as $item){
                //Stock Table
                DB::table('stock')->where(['product' => $item['product_id'], 'outlet' => $request['fromOutlet']])
                                      ->decrement('quantity',1);

                //Product_Archive Table
                $productarcv = Productarchive::where('barcode',$item['barcode'])->get()->first();
                $productarcv->status = 2;
                $productarcv->save();

            }
        });

        $userList = DB::table('tb_users')->where('outlet',$request['toOutlet'])->get();

        foreach ($userList as $key => $user) {
            DB::table('tb_notification')->insert(
                [
                    'userid' => $user->id,
                    'entry_by' => $user->id,
                    'postedBy' => Auth::user()->id,
                    'url'=>env('APP_URL').'productreceive/'.$entry->id,
                    'title'=>'New Products in Transfer Channel',
                    'note'=>'New Products With Transfercode : '.$transfercode,
                    'created'=>date("Y-m-d H:i:s"),
                    'icon'=>'fa fa-envelope',
                    'is_read'=>0,
                ]
            );
            
            //app('App\Http\Controllers\api\v1\notification\BlueerpPusher')->BlueerpPusher('Hello World');
            event(new BlueerpPusher('New Products in Transfer Channel. Transfercode : '.$transfercode,env('APP_URL').'productreceive/'.$entry->id,$user->id));
            
        }

        
        return ['status'=>'success','data'=>['statement'=>$transfercode,'id'=>$entry->id],'message'=>'New Stock Transfer Channel '.$transfercode.' Has Been Created'];
        
    }

    public function transferconfirm(Request $request){
        
        DB::transaction(function () use ($request) {
            $transfer = Stocktransfer::where('transfer_code',$request['transfercode'])
                                    ->where('status','0')->first();
            // Stock
            foreach($transfer->items as $item){
                //Stock Table
                $stocks = Stock::where(['product' => $item['product_id'], 'outlet' => $transfer->transfer_to])->get();
                if($stocks->count() > 0){
                    DB::table('stock')->where(['product' => $item['product_id'], 'outlet' => $transfer->transfer_to])
                                      ->increment('quantity',1);
                }
                else{
                    $nstock = new Stock();
                    $nstock->product = $item['product_id'];
                    $nstock->outlet = $transfer->transfer_to;
                    $nstock->quantity = 1;
                    $nstock->entry_by = Auth::user()->id;
                    $nstock->save();
                }

                //Product_Archive Table
                $productarcv = Productarchive::where('barcode',$item['barcode'])->first();
                $productarcv->status = 3; // Product is now sellable
                $productarcv->outlet_id = $transfer->transfer_to;
                $productarcv->outlet_name = $transfer->to_name;
                $eTrans = $productarcv->transfer;
                if($eTrans==null) $eTrans = []; 
                array_push($eTrans, ["transfer_code"=>$request['transfercode']]);
                $productarcv->transfer = $eTrans;
                $productarcv->transfer_statement = $request['transfercode'];
                $productarcv->update_by = Auth::user()->id;
                $productarcv->save();
            }
            // Update Stock_Transfer
            $transfer->status = '1';
            $transfer->update_by = Auth::user()->id;
            $transfer->confirmed_by = Auth::user()->id;
            $transfer->confirmed_name = Auth::user()->first_name.' '.Auth::user()->last_name;
            $transfer->confirmed_date = date("Y-m-d"); 
            $transfer->save();
        });

        return ['status'=>'success','data'=>['statement'=>$request['transfercode']],'message'=>'Product Receive From Transfer Channel '.$request['transfercode'].' Is Successful'];
    }

    private function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }
        array_multisort($sort_col, $dir, $arr);
    }
    

    public function supplierBillprocess($itemlist,$stt){
        //$this->array_sort_by_column($itemlist, 'supplierId');
        $sb = [];
        foreach($itemlist as $item){
            if (array_key_exists($item['supplierId'],$sb)){
                $sb[$item['supplierId']]['amount'] += ((int)$item['qtt']*(float)$item['purchase']);
            }
            else{
                $sb[$item['supplierId']]['id'] = $item['supplierId'];
                $sb[$item['supplierId']]['amount'] = ((int)$item['qtt']*(float)$item['purchase']);
            }
            
        }
        foreach($sb as $s){
            $s_name = Supplier::where('id',$s['id'])->pluck('name');
            //Supplier Bill table
            $sup_bill = new Supplierbill();
            $sup_bill->fy = date("Y");
            $sup_bill->date = date("Y-m-d");;
            $sup_bill->supplier_id = $s['id'];
            $sup_bill->supplier_name = $s_name[0];
            $sup_bill->bill = $s['amount'];
            $sup_bill->entry_by = Auth::user()->id;
            $sup_bill->statement = $stt;
            $sup_bill->save();

            // Supplier Table
            $amnt = $s['amount'];
            Supplier::where(['id' => $s['id']])->increment('due', $amnt);
        }
    }

    public function processSingleItem($item,$stt){
        
        $qtt = (int)$item['qtt'];
        // Stock Table
        $nstock = new Stock();
        $nstock->product = $item['productId'];
        $nstock->statement = $stt;
        $nstock->quantity = (int) $item['qtt'];
        $nstock->entry_by = Auth::user()->id;
        $nstock->save();

        // Product Table
        $product = Product::find($item['productId']);
        $product->stock = $product->stock + (int) $item['qtt'];

        $eTrans = $product->rpp;
        if($eTrans==null) $eTrans = [];
        array_push($eTrans, ["statement"=>$stt,"rpp"=>$item['purchase'],"remarks"=>$item['remarks']]);

        $product->rpp = $eTrans;
        $product->save();

    }
    public function getBarcodeNumber($product_code)
    {
        $serial = 1 + Productarchive::where('code',$product_code)->count();
        return $product_code.'-'.$serial;
    }
    public function statementDetails($id){
        $statement = Stockentry::find($id);
        return ['statement'=>$statement];
    }
    public function transferDetails($id){
        $statement = Stocktransfer::find($id);
        return ['statement'=>$statement];
    }
    public function getNewStatementNo(){
        $today = date("Y-m-d");
        $statements = Stockentry::where('statement','like', '%'.$today.'%')->get();
        $new = $statements->count() + 1;
        return $today.'-'.$new;
    }
    public function getNewTransferNo($from,$to){
        $likestt = date("Y-m-d") .'-'.$from.'-'.$to;
        $statements = Stocktransfer:: where('transfer_code','like', '%'.$likestt.'%')->get();
        $new = $statements->count() + 1;
        return $likestt.'-'.$new;
    }

    public function reset(){
        $products = Product::all();
        foreach($products as $product){
            $product->current_stock = 0;
            $product->save();
        }
        $suppliers = Supplier::all();
        foreach($suppliers as $supplier){
            $supplier->dues = 0.0;
            $supplier->save();
        }
        Productarchive::truncate();
        Supplierbill::truncate();
        Stock::truncate();
        Stockentry::truncate();
        Stocktransfer::truncate();
    }
    public function preventry(){
        $items =  Olditem::where('ID','!=',null)->leftJoin('old_t_price', 'old_Item_Information.itmCode', '=', 'old_t_price.Price_ItemOId')
        ->select('old_Item_Information.itmCode','old_Item_Information.itmName','old_t_price.Price_ListPrice')
        ->get();
        
        foreach ($items as $product)
        {
            $newproduct = new Product;
            $newproduct->code = $product->itmCode;
            $newproduct->name = $product->itmName;
            $newproduct->selling_price = (int)$product->Price_ListPrice;
            $newproduct->ecommerce = 'no';
            $newproduct->description = ' ';
            $newproduct->active = 'yes';
            $newproduct->entry_by = 1;
            $newproduct->save();
        }
        return Product::all();
        //return $items;
    }

    public function adjust_all_barcode_stock(){
        $product_archive = Productarchive::where('status',3)->get();
        foreach ($product_archive as $product) {
            $pid = $product->product_id;
            $outlet_id = $product->outlet_id;

            DB::table('stock')
            ->where(['product' => $pid, 'outlet' => $outlet_id])
            ->increment('quantity', 1);
        }
        echo 'Done';
    }

    public function adjust_products_current_stock(){
        //return 'comes';
        $products = Product::all();
        foreach($products as $product)
        {
            $stock = (int)Productarchive::where(['product_id'=>$product->id,'status'=>3])->count();
            DB::table('products')
                ->where('id',$product->id)
                ->update(['current_stock'=>$stock]);
        }
        return 'Done';
    }
    public function adjust_baseproduct_current_stock(){
        //return 'comes';
        $products = Product::where('ecommerce','yes')->get();
        foreach($products as $product)
        {
            $code = $product->code;
            $stock = (int)Productarchive::where(
                [
                    ['code','like', '%'.$code.'%'],
                    ['status','=',3]
                ]
                )->count();
            DB::table('products')
                ->where('id',$product->id)
                ->update(['current_stock'=>$stock]);
        }
        return 'Done';
    }
}
