<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'type',
        'is_percent',
        'amount',
        'max_amount',
        'status',
        'start_date',
        'end_date',
    ];

    //Discount Types
    const PRODUCT = 1;
    const BRAND = 2;
    const CATEGORY = 3;

    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $discount = new Discount();
            $discount->name = $request->name;
            $discount->type = $request->type;
            $discount->is_percent = $request->is_percent ? $request->is_percent : 0;
            $discount->amount = $request->amount;
            $discount->max_amount = $request->is_percent ? $request->max_amount ? $request->max_amount : 50000 : 0;
            $discount->status = true;
            $discount->start_date = $request->start_date;
            $discount->end_date = $request->end_date;

            $discount->save();

            DB::commit();

            return $discount;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateDiscount($request,$discount) {

        DB::beginTransaction();
        try {//dd($request);

            //insert the basic information
            $discount->name = $request->name;
            $discount->type = $request->type;
            $discount->is_percent = $request->is_percent ? $request->is_percent : 0;
            $discount->amount = $request->amount;
            $discount->max_amount = $request->is_percent ? $request->max_amount ? $request->max_amount : 50000 : 0;
            $discount->status = $request->status;
            $discount->start_date = $request->start_date;
            $discount->end_date = $request->end_date;
            $discount->save();

            DB::commit();

            return $discount;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
