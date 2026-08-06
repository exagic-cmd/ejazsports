<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use DB;

class Supply extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'store_out_id',
        'store_in_id',
        'send_date',
        'received_date',
        'notes',
        'type',
        'created_by',
        'approved_by',
        'received_by',
        'status',
        'total_products',
        'total_product_qty',
        'brand_id'
    ];

    //Status
    const CREATED = 1;
    const ISSUED = 2;
    const IN_TRANSIT = 3;
    const DELIVERED = 4;
    const ADDED = 5;

    //Type
    const NEW_STOCK = 1;
    const RETURN_STOCK = 2;
    const ORDER_STOCK = 3;

    public function storeOut() {
        return $this->belongsTo(Store::class,'store_out_id');
    }
    public function storeIn() {
        return $this->belongsTo(Store::class,'store_in_id');
    }
    public function createdBy() {
        return $this->belongsTo(User::class,'created_by');
    }
    public function approvedBy() {
        return $this->belongsTo(User::class,'approved_by');
    }
    public function receivedBy() {
        return $this->belongsTo(User::class,'received_by');
    }
    public function supplyProducts() {
        return $this->hasMany(SupplyProduct::class);
    }
    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function store($request) {

        DB::beginTransaction();
        try {

            $supply = new Supply();
            $supply->store_out_id = $request->store_out_id;
            $supply->store_in_id = $request->store_in_id;
            $supply->send_date = $request->send_date;
            $supply->type = $request->type;
            $supply->status = Self::CREATED;
            $supply->created_by = Auth::user()->id;
            $supply->notes = $request->notes;
            $supply->total_products = $request->total_products;
            $supply->total_product_qty = $request->total_qty;
            $supply->brand_id = $request->brand_id;

            $supply->save();

            for($i = 0; $i < count($request->s_qty); $i++) {

                if($request->s_qty[$i] > 0) {
                    $supplyProduct = new SupplyProduct();
                    $supplyProduct->product_id = $request->product_ids[$i];
                    $supplyProduct->supply_id = $supply->id;
                    $supplyProduct->qty = $request->s_qty[$i];
                    $supplyProduct->variant_id = $request->variant_ids[$i];
                    $supplyProduct->save();
                }
            }

            DB::commit();

            return $supply;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateSupply($request,$supply) {
        DB::beginTransaction();
        try {

            $supply->store_out_id = $request->store_out_id;
            $supply->store_in_id = $request->store_in_id;
            $supply->send_date = $request->send_date;
            $supply->type = $request->type;
            $supply->notes = $request->notes;
            $supply->total_products = $request->total_products;
            $supply->total_product_qty = $request->total_qty;
            $supply->brand_id = $request->brand_id;

            $supply->save();

            //delete previous record
            SupplyProduct::where('supply_id',$supply->id)->delete();
            for($i = 0; $i < count($request->s_qty); $i++) {

                if($request->s_qty[$i] > 0) {
                    $supplyProduct = new SupplyProduct();
                    $supplyProduct->product_id = $request->product_ids[$i];
                    $supplyProduct->supply_id = $supply->id;
                    $supplyProduct->qty = $request->s_qty[$i];
                    $supplyProduct->variant_id = $request->variant_ids[$i];
                    $supplyProduct->save();
                }
            }

            DB::commit();

            return $supply;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function addSupplyReceiving($request) {

        DB::beginTransaction();
        try {
            $supply = Supply::find($request->supply_id);
            $supply->received_date = $request->date;
            $supply->received_by = Auth::user()->id;
            $supply->status = self::DELIVERED;
            $supply->save();

            $diff = 0;$i=0;
            foreach ($supply->supplyProducts as $d) {
                $d->received_qty = $request->received_qty[$i++];
                $d->save();
                if($d->received_qty < $d->qty){
                    $diff += $d->received_qty - $d->qty;
                }
            }

            if($diff == 0) {
                $supply->status = self::ADDED;
                $supply->save();

                foreach ($supply->supplyProducts as $p) {

                    $storeProductStock = new StoreProductStock();
                    $storeProductStock->receiving_id = $supply->id;
                    $storeProductStock->store_id = $supply->store_in_id;
                    $storeProductStock->product_id = $p->product_id;
                    $storeProductStock->variant_id = $p->variant_id;
                    $storeProductStock->purchase_qty = $p->qty;

                    $price = StoreProductStock::where([['product_id',$p->product_id],['variant_id',$p->variant_id],['cost', '>=', 0]])->orderBy('id','DESC')->first();

                    $storeProductStock->cost = $price ? $price->cost : 0;

                    $storeProductStock->save();

                    //update the overall Quantity
                    if($supply->storeIn->available_for_online) {
                        $result = Product::find($p->product_id);
                        Product::where('id',$p->product_id)->update(['online_available_stock' => $result->online_available_stock + $p->qty]);
                        $result = ProductVariant::find($p->variant_id);
                        ProductVariant::where('id',$p->variant_id)->update(['online_available_stock' => $result->online_available_stock + $p->qty]);
                    }
                }
            }

            DB::commit();

            return $supply;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }

    public function changeSupplyStatus($request) {

            //if status issue then restock store out
            if($request->status == Supply::ISSUED) {
                $supply = Supply::find($request->supply_id);

                //get the supply products
                foreach ($supply->supplyProducts as $supplyProduct) {
                    $notSoldInventory = StoreProductStock::where([['store_id',$supply->store_out_id],['variant_id',$supplyProduct->variant_id]])
                        ->whereRaw('sold_qty < purchase_qty')
                        ->get();

                    $soldQuantity = $supplyProduct->qty;
                    //get all the unsold products
                    foreach ($notSoldInventory as $ui) {

                        $unsoldQuantity = $ui->purchase_qty - $ui->sold_qty;

                        if ($soldQuantity > $unsoldQuantity) {
                            $ui->sold_qty += $unsoldQuantity;
                            $ui->save();
                            $soldQuantity -= $unsoldQuantity;
                        } else {
                            $ui->sold_qty += $soldQuantity;
                            $ui->save();
                            $soldQuantity -= $soldQuantity;
                            break;
                        }
                    }

                    //update the overall Quantity
                    if($supply->storeOut->available_for_online) {
                        $result = Product::find($supplyProduct->product_id);
                        Product::where('id',$supplyProduct->product_id)->update(['online_available_stock' => $result->online_available_stock - $supplyProduct->qty]);
                        $result = ProductVariant::find($supplyProduct->variant_id);
                        ProductVariant::where('id',$supplyProduct->variant_id)->update(['online_available_stock' => $result->online_available_stock - $supplyProduct->qty]);
                    }

                }

            }

            if($request->status == Supply::ADDED) {
                $supply = Supply::find($request->supply_id);

                foreach ($supply->supplyProducts as $p) {

                    if($p->received_qty < $p->qty) {

                        $storeProductStock = new StoreProductStock();
                        $storeProductStock->supply_id = $supply->id;
                        $storeProductStock->store_id = $supply->store_out_id;
                        $storeProductStock->product_id = $p->product_id;
                        $storeProductStock->variant_id = $p->variant_id;
                        $storeProductStock->purchase_qty = $p->received_qty - $p->qty;

                        $price = StoreProductStock::where([['product_id',$p->product_id],['variant_id',$p->variant_id],['cost', '>=', 0]])->orderBy('id','DESC')->first();

                        $storeProductStock->cost = $price ? $price->cost : 0;
                        $storeProductStock->expiry_date = $price ? $price->expiry_date : null;

                        $storeProductStock->save();
                    }

                    $storeProductStock = new StoreProductStock();
                    $storeProductStock->supply_id = $supply->id;
                    $storeProductStock->store_id = $supply->store_in_id;
                    $storeProductStock->product_id = $p->product_id;
                    $storeProductStock->variant_id = $p->variant_id;
                    $storeProductStock->purchase_qty = $p->received_qty;

                    $price = StoreProductStock::where([['product_id',$p->product_id],['variant_id',$p->variant_id],['cost', '>=', 0]])->orderBy('id','DESC')->first();

                    $storeProductStock->cost = $price ? $price->cost : 0;
                    $storeProductStock->expiry_date = $price ? $price->expiry_date : null;

                    $storeProductStock->save();

                    //update the overall Quantity
                    if($supply->storeIn->available_for_online) {
                        $result = Product::find($p->product_id);
                        Product::where('id',$p->product_id)->update(['online_available_stock' => $result->online_available_stock + $p->qty]);
                        $result = ProductVariant::find($p->variant_id);
                        ProductVariant::where('id',$p->variant_id)->update(['online_available_stock' => $result->online_available_stock + $p->qty]);
                    }
                }



            }

            Supply::where('id', $request->supply_id)->update(['status' => $request->status,'approved_by' => Auth::user()->id]);

            return ['status'=>true];

    }
}
