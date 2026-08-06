<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAudit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'brand_id',
        'audit_By',
        'approve_by',
        'store_id',
        'status',
        'remarks',
        'date'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function storeId()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function auditBy()
    {
        return $this->belongsTo(User::class, 'audit_by');
    }

    public function approveBy()
    {
        return $this->belongsTo(User::class, 'approve_by');
    }

    public function products()
    {
        return $this->hasMany(StockAuditDetail::class);
    }

    const PENDING = 1;
    const APPROVED = 2;

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $stockAudit = new StockAudit();
            $stockAudit->brand_id = $request->brand_id;
            $stockAudit->audit_by = Auth::user()->id;
            $stockAudit->store_id = $request->store_id;
            $stockAudit->status = StockAudit::PENDING;
            $stockAudit->date = Carbon::now();
            $stockAudit->save();

            DB::commit();

            return $stockAudit;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateAudit($request, $stockAudit) {
        
        

        DB::beginTransaction();
        try {


            $stockAuditDetail = new StockAuditDetail();
            $stockAuditDetail->stock_audit_id = $stockAudit->id;
            $stockAuditDetail->product_id = $request->product_id;
            $stockAuditDetail->variant_id = $request->variant_id;
            $stockAuditDetail->system_qty = $request->available_qty;
            $stockAuditDetail->in_hand_qty = $request->in_hand_qty;
            $stockAuditDetail->adjust_in_stock = $request->adjust_in_stock;
            $stockAuditDetail->adjust_in_damage = $request->adjust_in_damage;
            $stockAuditDetail->adjust_in_expiry = $request->adjust_in_expiry;
            $stockAuditDetail->adjust_in_missing = $request->adjust_in_missing;
            $stockAuditDetail->adjust_in_tester = $request->adjust_in_tester;
            $stockAuditDetail->difference_qty = $request->in_hand_qty - $request->available_qty;
            
            $stockAuditDetail->reason = $request->reason;

            $stockAuditDetail->save();
            
            



            if($request->adjust_in_stock) {

                //quantity adjust in product and variant table
                $product = Product::where('id',$request->product_id)->first();
                
                Product::where('id',$product->id)->update(['available_stock' =>$product->available_stock
                        + $request->adjust_in_stock]);
                        
                if($request->variant_id) {
                    $variant = ProductVariant::where('id',$request->variant_id)->first();
                
                ProductVariant::where('id',$variant->id)->update(['available_stock' =>$variant->available_stock
                        + $request->adjust_in_stock]);
                }
                  

                //adjust in store stock
                
                if($request->variant_id) {
                    $records = StoreProductStock::where([['store_id',$stockAudit->store_id],['product_id',$request->product_id],['sold_qty','>',0],['variant_id',$request->variant_id]])->orderBy('id','DESC')->get();
                }
                else {
                    $records = StoreProductStock::where([['store_id',$stockAudit->store_id],['product_id',$request->product_id],['sold_qty','>',0]])->orderBy('id','DESC')->get();
                }
                
                if($records->isEmpty() && $request->in_hand_qty != 0) {
                   
                  
                    
                      $receiving = new Receiving();
                    $receiving->po_id = null;
                    $receiving->store_id = $stockAudit->store_id;
                    $receiving->cargo_no = 'System Added';
                    $receiving->date = Carbon::now();
                    $receiving->status = 2;
                    $receiving->created_by = Auth::user()->id;
                    $receiving->comment = 'Added by Audit';
                    $receiving->payment_method = 1;
                    $receiving->gross_amount = 0;
                    $receiving->tax = 0;
                    $receiving->packing_charges = 0;
                    $receiving->discount = 0;
                    $receiving->net_amount = 0;
                    $receiving->total_products = 1;
                    $receiving->total_qty = $request->adjust_in_stock;
          

                    $receiving->save();
            
            
                        $receivingProduct = new ReceivingProduct();
                        $receivingProduct->product_id = $request->product_id;
                        $receivingProduct->receiving_id = $receiving->id;
                        $receivingProduct->qty = $request->adjust_in_stock;
                        $receivingProduct->product_variant_id = $request->variant_id ? $request->variant_id : NULL;
                        $receivingProduct->trade_price = 0;
                        $receivingProduct->gst = 0;
                        $receivingProduct->discount = 0;
                        $receivingProduct->cost_price = 0;
                        

                        $receivingProduct->save();
                        
                         $storeProductStock = new StoreProductStock();
                    
                    $storeProductStock->store_id = $stockAudit->store_id;
                    $storeProductStock->product_id = $request->product_id;
                    $storeProductStock->variant_id = $request->variant_id ? $request->variant_id : NULL;
                    $storeProductStock->purchase_qty = $request->adjust_in_stock;
                    
                    $storeProductStock->receiving_id = $receiving->id;

                    $storeProductStock->cost =  0;

                    $storeProductStock->save(); 
                }
                
                else {

                $orderQty = $request->adjust_in_stock;
                foreach ($records as $stock) {
                    $availableQty = $stock->sold_qty;

                    if ($orderQty > $availableQty) {
                        $orderQty -= $availableQty;
                        StoreProductStock::where('id', $stock->id)->update(['sold_qty' => 0]);
                    } else {
                        StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->sold_qty - $orderQty]);
                        $orderQty = 0;
                    }

                    if ($orderQty == 0) {
                        break;
                    }

                }
                
                 
                
                if($orderQty > 0 && $request->in_hand_qty != 0) {
                   
                     
                    
                    
                    //Insert The Basic Receiving Info
                    $receiving = new Receiving();
                    $receiving->po_id = null;
                    $receiving->store_id = $stockAudit->store_id;
                    $receiving->cargo_no = 'System Added';
                    $receiving->date = Carbon::now();
                    $receiving->status = 2;
                    $receiving->created_by = Auth::user()->id;
                    $receiving->comment = 'Added by Audit';
                    $receiving->payment_method = 1;
                    $receiving->gross_amount = 0;
                    $receiving->tax = 0;
                    $receiving->packing_charges = 0;
                    $receiving->discount = 0;
                    $receiving->net_amount = 0;
                    $receiving->total_products = 1;
                    $receiving->total_qty = $orderQty;
          

                    $receiving->save();
            
            
                        $receivingProduct = new ReceivingProduct();
                        $receivingProduct->product_id = $request->product_id;
                        $receivingProduct->receiving_id = $receiving->id;
                        $receivingProduct->qty = $orderQty;
                        $receivingProduct->product_variant_id = $request->variant_id ? $request->variant_id : NULL;
                        $receivingProduct->trade_price = 0;
                        $receivingProduct->gst = 0;
                        $receivingProduct->discount = 0;
                        $receivingProduct->cost_price = 0;
                        

                        $receivingProduct->save();
                        
                        $storeProductStock = new StoreProductStock();
                    
                    $storeProductStock->store_id = $stockAudit->store_id;
                    $storeProductStock->product_id = $request->product_id;
                    $storeProductStock->variant_id = $request->variant_id ? $request->variant_id : NULL;
                    $storeProductStock->purchase_qty = $orderQty;

                    $storeProductStock->cost =  0;
                     $storeProductStock->receiving_id = $receiving->id;

                    $storeProductStock->save(); 
                        
            
                }
                
                }

            }
            if($request->adjust_in_damage || $request->adjust_in_expiry || $request->adjust_in_missing || $request->adjust_in_tester)  {

                $tqty = $request->adjust_in_damage + $request->adjust_in_expiry + $request->adjust_in_missing + $request->adjust_in_tester;

                //quantity adjust in product and variant table
                $product = Product::where('id',$request->product_id)->first();
                
                Product::where('id',$product->id)->update(['available_stock' =>$product->available_stock
                        - $tqty]);
                        
                if($request->variant_id) {
                    $variant = ProductVariant::where('id',$request->variant_id)->first();
                
                ProductVariant::where('id',$variant->id)->update(['available_stock' =>$variant->available_stock
                        - $tqty]);
                }
                //adjust in store stock
                if($request->variant_id) {
                    $records = StoreProductStock::where([['store_id', $stockAudit->store_id], ['product_id', $request->product_id],['variant_id',$request->variant_id]])->whereRaw("sold_qty <  purchase_qty")->orderBy('id', 'ASC')->get();
                }
                else {
                    $records = StoreProductStock::where([['store_id', $stockAudit->store_id], ['product_id', $request->product_id]])->whereRaw("sold_qty <  purchase_qty")->orderBy('id', 'ASC')->get();
                }

                $orderQty = $tqty;
                foreach ($records as $stock) {
                    $availableQty = $stock->purchase_qty - $stock->sold_qty;

                    if ($orderQty > $availableQty) {
                        $orderQty -= $availableQty;
                        StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->purchase_qty]);
                    } else {
                        StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->sold_qty + $orderQty]);
                        $orderQty = 0;
                    }

                    if ($orderQty == 0) {
                        break;
                    }

                }


            }
            
            
            
            if($request->in_hand_qty == 0) {
                if($request->variant_id) {
                    StoreProductStock::where([['store_id', $stockAudit->store_id], ['variant_id', $request->variant_id]])->update(['sold_qty' => DB::raw('`purchase_qty`')]);
                }
                else {
                    
                    StoreProductStock::where([['store_id', $stockAudit->store_id], ['product_id', $request->product_id]])->update(['sold_qty' => DB::raw('`purchase_qty`')]);
                
                }
            }
            
           
            // $pStockQty = StoreProductStock::where([['product_id',$request->product_id]])->sum(DB::raw('purchase_qty - sold_qty'));
            
            // Product::where('id',$request->product_id)->update(['available_stock' => $pStockQty]);
            
            
            



            DB::commit();

            return $stockAudit;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
