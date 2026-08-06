<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use DB;

class Receiving extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'invoice_no',
        'cargo_no',
        'po_id',
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
        'supplier_id',
        'packing_charges'
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
        return $this->hasMany(ReceivingProduct::class)->with('product','variant');
    }

    public function documents() {
        return $this->hasMany(ReceivingDocument::class);
    }

    public function purchaseOrder() {
        return $this->belongsTo(PurchaseOrder::class,'po_id','id');
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
				//dd($request);
        DB::beginTransaction();
        try {
            
            
            if($request->rr_id != 0) {
                Receiving::where('id',$request->rr_id)->delete();
            }

            //Insert The Basic Receiving Info
            $receiving = new Receiving();
            $receiving->po_id = $request->po_id ? $request->po_id : null;
            $receiving->store_id = $request->store_id;
            $receiving->cargo_no = $request->cargo_no;
            $receiving->date = $request->date;
            $receiving->status = Self::APPROVAL_PENDING;
            $receiving->created_by = Auth::user()->id;
            $receiving->comment = $request->comment;
            $receiving->payment_method = $request->payment_method;
            $receiving->gross_amount = 0;
            $receiving->tax = 0;
            $receiving->packing_charges = 0;
            $receiving->discount = 0;
            $receiving->net_amount = 0;
            $receiving->total_products = 0;
            $receiving->total_qty = 0;
          

            $receiving->save();
            
            //delete if re edit order
            // Step 1: Fetch all existing receiving products for this receiving_id
            $existingReceivingProducts = ReceivingProduct::where('receiving_id', $request->rr_id)->get();
            
            // Step 2: Reverse stock adjustments before deleting old records
            foreach ($existingReceivingProducts as $p) {
                // Reduce stock in Product table
                $product = Product::find($p->product_id);
                if ($product) {
                    $product->available_stock -= $p->qty; // Add back old quantity
                    $product->save();
                }
            
                // Reduce stock in ProductVariant table
                $variant = ProductVariant::find($p->product_variant_id);
                if ($variant) {
                    $variant->available_stock -= $p->qty; // Add back old quantity
                    $variant->save();
                }
            }
            
            // Step 3: Delete old ReceivingProduct records
            ReceivingProduct::where('receiving_id', $receiving->id)->delete();
            
            StoreProductStock::where('receiving_id',$receiving->id)->delete();
            
            
            

            //Update the Purchase Order Status
            if($request->po_id)
            PurchaseOrder::where('id',$request->po_id)->update(['status'=>PurchaseOrder::RECEIVED]);
            
            $totalProducts = $totalQty =  0;

            //Insert the Receiving Products
            for($i = 0; $i < count($request->received_qty); $i++) {

                if($request->received_qty[$i] > 0) {

                    if($request->po_product[$i] == true) {
                        $receivingProduct = new ReceivingProduct();
                        $receivingProduct->product_id = $request->product_ids[$i];
                        $receivingProduct->receiving_id = $receiving->id;
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
                        $receivingProduct = new ReceivingProduct();
                        $variant = null;
                        $variant = ProductVariant::find($request->variant[$i]);
                        $product = Product::find($request->product[$i]);
                        $receivingProduct->product_id = $product->id;
                        $receivingProduct->receiving_id = $receiving->id;
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


            //Insert the documents
            if($request->file) {
                for($i = 0; $i<count($request->file);$i++) {

                    $receivingDocument = new ReceivingDocument();
                    $receivingDocument->receiving_id = $receiving->id;

                    $name = time() . '-'.$i . '-' . $request->file('file')[$i]->getClientOriginalName();
                    $path = $request->file('file')[$i]->storeAs('documents/po',$name);

                    $receivingDocument->file = $path;
                    $receivingDocument->save();
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

            $receiving->po_id = $request->po_id ? $request->po_id : null;
            $receiving->supplier_id = $request->supplier_id;
            $receiving->store_id = $request->store_id;
            $receiving->invoice_no = $request->invoice_no;
            $receiving->date = $request->date;
            $receiving->comment = $request->comment;
            $receiving->payment_method = $request->payment_method;
            $receiving->gross_amount = $request->gross_amount;
            $receiving->tax = $request->g_tax;
            $receiving->packing_charges = $request->packing_charges;
            $receiving->discount = $request->g_discount;
            $receiving->net_amount = $request->net_amount;
            $receiving->total_products = $request->total_products;
            $receiving->total_qty = $request->total_qty;

            $receiving->save();
           
            
               if($receiving->status == Receiving::APPROVED){
            $res = ReceivingProduct::where('receiving_id',$receiving->id)->get();
            
            foreach ($res as $p) {
            
                //delete the quantity
                $result = Product::find($p->product_id);
                Product::where('id', $result->id)->update(['available_stock' => $result->available_stock - $p->qty]);
                $result = ProductVariant::find($p->product_variant_id);
                if($result) {
                    ProductVariant::where('id', $p->product_variant_id)->update(['available_stock' => $result->available_stock - $p->qty]);
                }
            
            }
               }

            //delete previous added
            ReceivingProduct::where('receiving_id',$receiving->id)->delete();
            
            $sRecord = StoreProductStock::where('receiving_id',$receiving->id)->get();
            StoreProductStock::where('receiving_id',$receiving->id)->delete();
            //Insert the Receiving Products
            for($i = 0; $i < count($request->r_qty); $i++) {

                if($request->r_qty[$i] > 0) {

                    if($request->po_product[$i] == true) {
                        $receivingProduct = new ReceivingProduct();
                        $receivingProduct->product_id = $request->product_ids[$i];
                        $receivingProduct->receiving_id = $receiving->id;
                        $receivingProduct->qty = $request->r_qty[$i];
                        $receivingProduct->product_variant_id = $request->variant_ids[$i];
                        $receivingProduct->trade_price = $request->t_price[$i];
                        $receivingProduct->gst = round($request->gst[$i] / $request->r_qty[$i]);
                        $receivingProduct->discount = round($request->discount[$i] / $request->r_qty[$i]);
                        $receivingProduct->cost_price = round($request->t_price[$i] - ($request->discount[$i] / $request->r_qty[$i]) + ($request->gst[$i] / $request->r_qty[$i]));
                        $receivingProduct->sale_price = $request->retail_price[$i];
                        $receivingProduct->dz_price = $request->dz_price[$i];
                        
                        

                        $receivingProduct->save();
                        
                        if($request->retail_price[$i] > 0) {
                            
                            if( !$request->variant_ids[$i]) {
                                
                               $product =  Product::where('id',$request->product_ids[$i])->first();
                               if($request->retail_price[$i] > $product->price) {
                            Product::where('id',$request->product_ids[$i])->update(['price' => $request->retail_price[$i],'dz_price'=>$request->dz_price[$i],'purchase_price' => $request->t_price[$i]]);
                               }
                            }
                            
                            else {
                                
                                $productVariant=  ProductVariant::where('id', $request->variant_ids[$i])->first();
                                
                                if($request->retail_price[$i] > $productVariant->additional_price) {
                    
                        ProductVariant::where('id', $request->variant_ids[$i])->update(['additional_price' =>$request->retail_price[$i],'dz_price'=>$request->dz_price[$i],'purchase_price' => $request->t_price[$i]]);
                                }
                            }
                        
                        }

                      }
                    else {
                        $receivingProduct = new ReceivingProduct();
                        $variant = ProductVariant::find($request->product[$i]);
                        $receivingProduct->product_id = $variant->product_id;
                        $receivingProduct->receiving_id = $receiving->id;
                        $receivingProduct->qty = $request->r_qty[$i];
                        $receivingProduct->product_variant_id = $variant->id;
                        $receivingProduct->trade_price = $request->t_price[$i];
                        $receivingProduct->gst = round($request->gst[$i] / $request->r_qty[$i]);
                        $receivingProduct->discount = round($request->discount[$i] / $request->r_qty[$i]);
                        $receivingProduct->cost_price = round($request->t_price[$i] - ($request->discount[$i] / $request->r_qty[$i]) + ($request->gst[$i] / $request->r_qty[$i]));
                        $receivingProduct->sale_price = $request->retail_price[$i];

                        $receivingProduct->save();
                        
                        if($request->retail_price[$i] > 0) {
                            
                            if( !$request->variant_ids[$i]) {
                            Product::where('id',$request->product_ids[$i])->update(['price' => $request->retail_price[$i],'dz_price'=>$request->dz_price[$i],'purchase_price' => $request->t_price[$i]]);
                            }
                            
                            else {
                    
                        ProductVariant::where('id', $request->variant_ids[$i])->update(['additional_price' =>$request->retail_price[$i],'dz_price'=>$request->dz_price[$i],'purchase_price' => $request->t_price[$i]]);
                            }
                        
                        }

                       }


                }
            }

            //Insert the documents
            if($request->file) {
                for($i = 0; $i<count($request->file);$i++) {

                    $receivingDocument = new ReceivingDocument();
                    $receivingDocument->receiving_id = $receiving->id;

                    $name = time() . '-'.$i . '-' . $request->file('file')[$i]->getClientOriginalName();
                    $path = $request->file('file')[$i]->storeAs('documents/po',$name);

                    $receivingDocument->file = $path;
                    $receivingDocument->save();
                }
            }
            
            
            

        foreach ($receiving->products as $p) {

            //Add the stock in store
            $storeProductStock = new StoreProductStock();
            $storeProductStock->receiving_id = $receiving->id;
            $storeProductStock->store_id = $receiving->store_id;
            $storeProductStock->product_id = $p->product_id;
            $storeProductStock->variant_id = $p->product_variant_id;
            $storeProductStock->purchase_qty = $p->qty;
            $storeProductStock->expiry_date = $p->expiry_date;
            $storeProductStock->cost = $p->cost_price;
            
            //update sold qty
            if($sRecord->where('product_id',$p->product_id)->where('variant_id',$p->product_variant_id)->first()) {
            $storeProductStock->sold_qty = $sRecord->where('product_id',$p->product_id)->where('variant_id',$p->product_variant_id)->first()->sold_qty;
            }

            $storeProductStock->save();
            
        
            //update the available quantity
            $result = Product::find($p->product_id);
            if($receiving->receivedStore->available_for_online)
                Product::where('id', $result->id)->update(['available_stock' => $result->available_stock + $p->qty,'online_available_stock' => $result->online_available_stock + $p->qty]);
            else
                Product::where('id', $result->id)->update(['available_stock' => $result->available_stock + $p->qty]);

            $result = ProductVariant::find($p->product_variant_id);
            
            if($result) {
            if($receiving->receivedStore->available_for_online)
                ProductVariant::where('id', $p->product_variant_id)->update(['available_stock' => ($result->available_stock + $p->qty) , 'online_available_stock' => $result->online_available_stock + $p->qty]);
            else
                ProductVariant::where('id', $p->product_variant_id)->update(['available_stock' => ($result->available_stock + $p->qty)]);
            }
        }

        Receiving::where('id', $receiving->id)->update(['status' => Receiving::APPROVED,'approved_by' => Auth::user()->id]);

            DB::commit();

            return $receiving;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function changeReceivingStatus($request) {

        $receiving = Receiving::find($request->receiving_id);

        foreach ($receiving->products as $p) {

            //Add the stock in store
            $storeProductStock = new StoreProductStock();
            $storeProductStock->receiving_id = $receiving->id;
            $storeProductStock->store_id = $receiving->store_id;
            $storeProductStock->product_id = $p->product_id;
            $storeProductStock->variant_id = $p->product_variant_id;
            $storeProductStock->purchase_qty = $p->qty;
            $storeProductStock->expiry_date = $p->expiry_date;
            $storeProductStock->cost = $p->cost_price;

            $storeProductStock->save();

            //update the available quantity
            $result = Product::find($p->product_id);
            if($receiving->receivedStore->available_for_online)
                Product::where('id', $result->id)->update(['available_stock' => $result->available_stock + $p->qty,'online_available_stock' => $result->online_available_stock + $p->qty]);
            else
                Product::where('id', $result->id)->update(['available_stock' => $result->available_stock + $p->qty]);

            $result = ProductVariant::find($p->product_variant_id);
            
            if($result) {
            if($receiving->receivedStore->available_for_online)
                ProductVariant::where('id', $p->product_variant_id)->update(['available_stock' => ($result->available_stock + $p->qty) , 'online_available_stock' => $result->online_available_stock + $p->qty]);
            else
                ProductVariant::where('id', $p->product_variant_id)->update(['available_stock' => ($result->available_stock + $p->qty)]);
            }
        }

        Receiving::where('id', $request->receiving_id)->update(['status' => $request->status,'approved_by' => Auth::user()->id]);
        return ['status'=>true];
    }
    
    public function directReceiving($request)
    {

        DB::beginTransaction();
        try {

            //Insert The Basic Receiving Info

            $receiving = new Receiving();

            $receiving->po_id = null;
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

            $receiving = Receiving::find($request->receiving_id);


            $receivingProduct = new ReceivingProduct();
            $receivingProduct->product_id = $request->product_id;
            $receivingProduct->receiving_id = $receiving->id;
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
    
    
    public function deleteRec($receiving) {

        StoreProductStock::where('receiving_id',$receiving->id)->delete();
        
        foreach ($receiving->products as $p) {

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

        $receiving->delete();
        return ['status'=>true];
    }
}
