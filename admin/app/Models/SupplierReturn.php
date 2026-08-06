<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use DB;

class SupplierReturn extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'invoice_no',
        'cargo_no',
        'date',
        'payment_method',
        'gross_amount',
        'tax',
        'discount',
        'net_amount',
        'total_products',
        'total_qty',
        'status',
        'created_by',
        'approved_by',
        'store_id',
        'comment',
        'file',
        'supplier_id'
    ];

    //status
    const APPROVAL_PENDING = 1;
    const APPROVED = 2;
    const PARTIALLY_PAID = 3;
    const PAID = 4;

    //payment method
    const CASH = 1;
    const CREDIT = 2;
    const SALE_BASIS = 3;

    public function products() {
        return $this->hasMany(SupplierReturnProduct::class)->with('product','variant');
    }


    public function createdBy() {
        return $this->belongsTo(User::class,'created_by');
    }
    public function approvedBy() {
        return $this->belongsTo(User::class,'approved_by');
    }

    public function receivedStore() {
        return $this->belongsTo(Store::class,'store_id');
    }
    
    public function supplier() {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }


    public function store($request) {

        DB::beginTransaction();
        try {

            //Insert The Basic Receiving Info
            $receiving = new SupplierReturn();
            $receiving->store_id = $request->store_id;
              $receiving->stock_type = $request->stock_type;    
            $receiving->cargo_no = $request->cargo_no;
            $receiving->date = $request->date;
            $receiving->status = Self::APPROVAL_PENDING;
            $receiving->created_by = Auth::user()->id;
            $receiving->comment = $request->comment;
            $receiving->payment_method = $request->payment_method;
            $receiving->gross_amount = 0;
            $receiving->tax = 0;
            $receiving->discount = 0;
            $receiving->net_amount = 0;
            $receiving->total_products = 0;
            $receiving->total_qty = 0;
          

            $receiving->save();

            //Update the Purchase Order Status
          
            
            $totalProducts = $totalQty =  0;

            //Insert the Receiving Products
            for($i = 0; $i < count($request->received_qty); $i++) {

                if($request->received_qty[$i] > 0) {

                    if($request->po_product[$i] == true) {
                        $receivingProduct = new SupplierReturnProduct();
                        $receivingProduct->product_id = $request->product_ids[$i];
                        $receivingProduct->supplier_return_id = $receiving->id;
                        $receivingProduct->qty = $request->received_qty[$i];
                        $receivingProduct->product_variant_id = $request->variant_ids[$i];
                        $receivingProduct->trade_price = 0;
                        $receivingProduct->gst = 0;
                        $receivingProduct->discount = 0;
                        $receivingProduct->cost_price = 0;
                        

                        $receivingProduct->save();
                        
                        $totalProducts++;
                        $totalQty += $request->received_qty[$i];

                    }
                    else {
                        $receivingProduct = new SupplierReturnProduct();
                        $variant = null;
                        $variant = ProductVariant::find($request->variant[$i]);
                        $product = Product::find($request->product[$i]);
                        $receivingProduct->product_id = $product->id;
                        $receivingProduct->supplier_return_id = $receiving->id;
                        $receivingProduct->qty = $request->received_qty[$i];
                        $receivingProduct->product_variant_id = $variant ? $variant->id : null;
                        $receivingProduct->trade_price = 0;
                        $receivingProduct->gst = 0;
                        $receivingProduct->discount = 0;
                        $receivingProduct->cost_price = 0;
                    
                        $totalProducts++;
                        $totalQty += $request->received_qty[$i];

                        $receivingProduct->save();
                    }
                }
            }


          
            $receiving->total_products = $totalProducts;
            $receiving->total_qty = $totalQty;
            
            $receiving->save();


            DB::commit();

            return $receiving;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateReceiving($request,$receiving) {

        DB::beginTransaction();
        try {

            //Insert The Basic Receiving Info

            $receiving->supplier_id = $request->supplier_id;
            $receiving->store_id = $request->store_id;
      
            $receiving->invoice_no = $request->invoice_no;
            $receiving->date = $request->date;
            $receiving->comment = $request->comment;
            $receiving->payment_method = $request->payment_method;
            $receiving->gross_amount = $request->gross_amount;
            $receiving->tax = $request->g_tax;
            $receiving->discount = $request->g_discount;
            $receiving->net_amount = $request->net_amount;
            $receiving->total_products = $request->total_products;
            $receiving->total_qty = $request->total_qty;

            $receiving->save();

            //delete previous added
            SupplierReturnProduct::where('supplier_return_id',$receiving->id)->delete();
            // StoreProductStock::where('receiving_id',$receiving->id)->delete();
            //Insert the Receiving Products
            for($i = 0; $i < count($request->r_qty); $i++) {

                if($request->r_qty[$i] > 0) {

                    
                        $receivingProduct = new SupplierReturnProduct();
                    
                        $receivingProduct->product_id = $request->product_ids[$i];
                        $receivingProduct->supplier_return_id = $receiving->id;
                        $receivingProduct->qty = $request->r_qty[$i];
                        $receivingProduct->product_variant_id = $request->variant_ids[$i];
                        $receivingProduct->trade_price = $request->t_price[$i];
                        $receivingProduct->gst = 0;
                        $receivingProduct->discount = 0;
                        $receivingProduct->cost_price = round($request->t_price[$i]);
                        $receivingProduct->sale_price = 0;

                        $receivingProduct->save();
                   


                }
            }

         
            if($receiving->stock_type == 1) {
            

        foreach ($receiving->products as $p) {

            //Add the stock in store
            // $storeProductStock = new StoreProductStock();
            // $storeProductStock->receiving_id = $receiving->id;
            // $storeProductStock->store_id = $receiving->store_id;
            // $storeProductStock->product_id = $p->product_id;
            // $storeProductStock->variant_id = $p->product_variant_id;
            // $storeProductStock->purchase_qty = -($p->qty);
            // $storeProductStock->expiry_date = $p->expiry_date;
            // $storeProductStock->cost = $p->cost_price;

            // $storeProductStock->save();

            //update the available quantity
            $result = Product::find($p->product_id);
            if($receiving->receivedStore->available_for_online)
                Product::where('id', $result->id)->update(['available_stock' => $result->available_stock - $p->qty,'online_available_stock' => $result->online_available_stock - $p->qty]);
            else
                Product::where('id', $result->id)->update(['available_stock' => $result->available_stock - $p->qty]);

            $result = ProductVariant::find($p->product_variant_id);
            
            if($result) {
            if($receiving->receivedStore->available_for_online)
                ProductVariant::where('id', $p->product_variant_id)->update(['available_stock' => ($result->available_stock - $p->qty) , 'online_available_stock' => $result->online_available_stock - $p->qty]);
            else
                ProductVariant::where('id', $p->product_variant_id)->update(['available_stock' => ($result->available_stock - $p->qty)]);
            }
            
            
            
            $storeStocks = StoreProductStock::where([['product_id',$p->product_id],['variant_id',($p->product_variant_id ? $p->product_variant_id : null)]])->whereRaw("sold_qty <  purchase_qty")->orderBy('id','ASC')->get();
                    
                   

                    $orderQty = $p->qty;
                    $receivingId= 0;
                    foreach($storeStocks as $stock) {
                        $availableQty = $stock->purchase_qty - $stock->sold_qty;

                        if($orderQty > $availableQty) {
                            $orderQty -= $availableQty;
                            StoreProductStock::where('id',$stock->id)->update(['sold_qty' => $orderQty]);
                        }
                        else {
                            StoreProductStock::where('id',$stock->id)->update(['sold_qty' => $stock->sold_qty + $orderQty]);
                            $orderQty = 0;
                        }
                        
                        if($orderQty == 0){
                            break;
                        }

                    }
        }
            }

        SupplierReturn::where('id', $receiving->id)->update(['status' => Receiving::APPROVED,'approved_by' => Auth::user()->id]);

            DB::commit();

            return $receiving;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function changeReceivingStatus($request) {

        $receiving = SupplierReturn::find($request->receiving_id);

        foreach ($receiving->products as $p) {

            //Add the stock in store
            $storeProductStock = new StoreProductStock();
            $storeProductStock->receiving_id = $receiving->id;
            $storeProductStock->store_id = $receiving->store_id;
            $storeProductStock->product_id = $p->product_id;
            $storeProductStock->variant_id = $p->product_variant_id;
            $storeProductStock->purchase_qty = -($p->qty);
            $storeProductStock->expiry_date = $p->expiry_date;
            $storeProductStock->cost = $p->cost_price;

            $storeProductStock->save();
            
         

            //update the available quantity
            $result = Product::find($p->product_id);
            if($receiving->receivedStore->available_for_online)
                Product::where('id', $result->id)->update(['available_stock' => $result->available_stock - $p->qty,'online_available_stock' => $result->online_available_stock - $p->qty]);
            else
                Product::where('id', $result->id)->update(['available_stock' => $result->available_stock - $p->qty]);

            $result = ProductVariant::find($p->product_variant_id);
            
            if($result) {
            if($receiving->receivedStore->available_for_online)
                ProductVariant::where('id', $p->product_variant_id)->update(['available_stock' => ($result->available_stock - $p->qty) , 'online_available_stock' => $result->online_available_stock - $p->qty]);
            else
                ProductVariant::where('id', $p->product_variant_id)->update(['available_stock' => ($result->available_stock - $p->qty)]);
            }
        }

        SupplierReturn::where('id', $request->receiving_id)->update(['status' => $request->status,'approved_by' => Auth::user()->id]);
        return ['status'=>true];
    }
    
    public function directReceiving($request)
    {

        DB::beginTransaction();
        try {

            //Insert The Basic Receiving Info

            $receiving = new SupplierReturn();

      
            $receiving->store_id = $request->store_id;
            $receiving->cargo_no = $request->cargo_no;
            $receiving->date = $request->date;
            $receiving->comment = '';
            $receiving->payment_method = 2;
            $receiving->gross_amount = 0;
            $receiving->tax = 0;
            $receiving->discount = 0;
            $receiving->net_amount = $request->net_amount;
            $receiving->total_products = 0;
            $receiving->total_qty = 0;
            $receiving->status = Self::APPROVAL_PENDING;
            $receiving->created_by = Auth::user()->id;

            $receiving->save();

            DB::commit();

            return $receiving;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function directReceivingProductSubmit($request) {


        DB::beginTransaction();
        try {

            $receiving = SupplierReturn::find($request->receiving_id);


            $receivingProduct = new SupplierReturnProduct();
            $receivingProduct->product_id = $request->product_id;
            $receivingProduct->supplier_return_id = $receiving->id;
            $receivingProduct->qty = $request->scan_qty;
            $receivingProduct->product_variant_id = $request->variant_id ?  $request->variant_id : NULL;
            $receivingProduct->trade_price = round($request->total_t_price / $request->scan_qty);
            $receivingProduct->gst = 0;
            $receivingProduct->discount = 0;
            $receivingProduct->cost_price = round($request->total_t_price / $request->scan_qty);

            $receivingProduct->save();


            $receiving->total_products = $receiving->total_products + 1;
            $receiving->total_qty = $receiving->total_qty + $request->scan_qty;

            $receiving->net_amount = $receiving->net_amount + ($request->total_t_price);

            $receiving->save();

            DB::commit();

            return $receiving;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }
}
