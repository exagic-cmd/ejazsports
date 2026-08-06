<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'discount_amount',
        'is_percent',
        'min_order_amount',
        'max_discount_amount',
        'usage',
        'limit_count',
        'type',
        'status',
        'start_date',
        'end_date',
        'customer_id',
        'order_id'
    ];

    //Coupon Types
    const PRODUCT = 1;
    const BRAND = 2;
    const CATEGORY = 3;
    const ORDER = 4;
    const DELIVERY = 5;

    //Coupon Usage
    const ONCE = 1;
    const EACH_CUSTOMER_ONCE = 2;
    const LIMITED = 3;
    const UNLIMITED = 4;

    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $coupon = new Coupon();
            $coupon->name = $request->name;
            $coupon->discount_amount = $request->discount_amount;
            $coupon->is_percent = $request->is_percent ? $request->is_percent : 0;
            $coupon->min_order_amount = $request->min_order_amount ? $request->min_order_amount : 0;
            $coupon->max_discount_amount = $request->is_percent ? $request->max_discount_amount : 0;
            $coupon->type = $request->type;
            $coupon->usage = $request->usage;
            $coupon->limit_count = $request->limit_count;
            $coupon->status = true;
            $coupon->start_date = $request->start_date;
            $coupon->end_date = $request->end_date;
            $coupon->customer_id = $request->customer_id;
            $coupon->order_id = $request->order_id;
            $coupon->save();

            DB::commit();

            return $coupon;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateCoupon($request,$coupon) {

        DB::beginTransaction();
        try {//dd($request);

            //insert the basic information
            $coupon->name = $request->name;
            $coupon->discount_amount = $request->discount_amount;
            $coupon->is_percent = $request->is_percent ? $request->is_percent : 0;
            $coupon->min_order_amount = $request->min_order_amount ? $request->min_order_amount : 0;
            $coupon->max_discount_amount = $request->is_percent ? $request->max_discount_amount : 0;
            $coupon->type = $request->type;
            $coupon->usage = $request->usage;
            $coupon->limit_count = $request->limit_count;
            $coupon->status = true;
            $coupon->start_date = $request->start_date;
            $coupon->end_date = $request->end_date;
            $coupon->customer_id = $request->customer_id;
            $coupon->order_id = $request->order_id;
            $coupon->save();

            DB::commit();

            return $coupon;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
