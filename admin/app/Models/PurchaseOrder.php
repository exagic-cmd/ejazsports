<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use DB;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes ;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'supplier_id',
        'po_no',
        'date',
        'total_amount',
        'tax',
        'total_products',
        'total_product_qty',
        'status',
        'created_by',
        'approved_by',
        'store_id',
        'comment'
    ];

    //status
    const APPROVAL_PENDING = 1;
    const APPROVED = 2;
    const PO_SENT = 3;
    const RECEIVED = 4;

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class,'created_by');
    }
    public function approvedBy() {
        return $this->belongsTo(User::class,'approved_by');
    }

    public function shipStore() {
        return $this->belongsTo(Store::class,'store_id');
    }

    public function products() {
        return $this->hasMany(PurchaseOrderProduct::class);
    }

    public function receiving() {
        return $this->hasMany(Receiving::class,'po_id');
    }
    
      public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function store($request) {

        DB::beginTransaction();
        try {

        $purchaseOrder = new PurchaseOrder();
        $purchaseOrder->supplier_id = $request->supplier_id;
        $purchaseOrder->store_id = $request->store_id;
        $count = PurchaseOrder::where('supplier_id',$request->supplier_id)->count();
        $purchaseOrder->po_no = 'PO'.$request->supplier_id.'-'.++$count.'-'.date('m').date('y');
        $purchaseOrder->date = $request->date;
        $purchaseOrder->status = Self::APPROVAL_PENDING;
        $purchaseOrder->created_by = Auth::user()->id;
        $purchaseOrder->comment = $request->comment;

        $purchaseOrder->save();

        $total_products = 0;
        $total_qty = 0;
        $total_amount = 0;
        
        $pQty = preg_split ("/\,/", $request->s_p_qty[0]);
        $proId = preg_split ("/\,/", $request->s_product_ids[0]);
        $varId = preg_split ("/\,/", $request->s_variant_ids[0]);
        for($i = 0; $i < count($pQty); $i++) {

            if($pQty[$i] > 0) {
                $purchaseOrderProduct = new PurchaseOrderProduct();
                $purchaseOrderProduct->product_id = $proId[$i];
                $purchaseOrderProduct->purchase_order_id = $purchaseOrder->id;
                $purchaseOrderProduct->quantity = $pQty[$i];
                $purchaseOrderProduct->product_variant_id = $varId[$i] ? $varId[$i] : null;
                $purchaseOrderProduct->trade_price = 0;
                $total_products++;
                $total_qty += $pQty[$i];
                $total_amount += (0 * $pQty[$i]);

                $purchaseOrderProduct->save();
            }
        }

        $purchaseOrder->total_amount = $total_amount;
        $purchaseOrder->total_products = $total_products;
        $purchaseOrder->total_product_qty = $total_qty;
        $purchaseOrder->save();
        DB::commit();
        return $purchaseOrder;
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            throw $e; // instead of return null
        }
    }

   
    public function updatePurchase($request, $purchaseOrder) {
      
        DB::beginTransaction();
        try {

            $purchaseOrder->supplier_id = $request->supplier_id;
            $purchaseOrder->store_id = $request->store_id;
            $purchaseOrder->date = $request->date;
            $purchaseOrder->comment = $request->comment;

            $purchaseOrder->save();

            $total_products = 0;
            $total_qty = 0;
            $total_amount = 0;

            PurchaseOrderProduct::where('purchase_order_id',$purchaseOrder->id)->delete();

            for($i = 0; $i < count($request->p_qty); $i++) {

                if($request->p_qty[$i] > 0) {
                    $purchaseOrderProduct = new PurchaseOrderProduct();
                    $purchaseOrderProduct->product_id = $request->product_ids[$i];
                    $purchaseOrderProduct->purchase_order_id = $purchaseOrder->id;
                    $purchaseOrderProduct->quantity = $request->p_qty[$i];
                    $purchaseOrderProduct->product_variant_id = $request->variant_ids[$i];
                    $purchaseOrderProduct->trade_price = $request->p_tps[$i];
                    $total_products++;
                    $total_qty += $request->p_qty[$i];
                    $total_amount += ($request->p_tps[$i] * $request->p_qty[$i]);

                    $purchaseOrderProduct->save();
                }
            }

            $purchaseOrder->total_amount = $total_amount;
            $purchaseOrder->total_products = $total_products;
            $purchaseOrder->total_product_qty = $total_qty;

            $purchaseOrder->save();

            DB::commit();

            return $purchaseOrder;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
