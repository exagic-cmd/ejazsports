<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Area extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'delivery_charges',
        'min_order_amount',
        'delivery_charges_above',
        'min_weight_allow',
        'extra_charges_per_g_ml',
        'status',
        'serial_no'
    ];

    public function store($request) {
        DB::beginTransaction();
        try {

            //insert the basic information
            $area = new Area();
            $area->name = $request->name;
            $area->delivery_charges = $request->delivery_charges;
            $area->min_order_amount = $request->min_order_amount;
            $area->delivery_charges_above = $request->delivery_charges_above;
            $area->min_weight_allow = $request->min_weight_allow;
            $area->extra_charges_per_g_ml = $request->extra_charges_per_g_ml;
            $area->status = $request->status;
            $area->serial_no = $request->serial_no;

            $area->save();

            DB::commit();

            return $area;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateArea($request,$area) {

        DB::beginTransaction();
        try {

            //update the basic information
            $area->name = $request->name;
            $area->delivery_charges = $request->delivery_charges;
            $area->min_order_amount = $request->min_order_amount;
            $area->delivery_charges_above = $request->delivery_charges_above;
            $area->min_weight_allow = $request->min_weight_allow;
            $area->extra_charges_per_g_ml = $request->extra_charges_per_g_ml;
            $area->status = $request->status;
            $area->serial_no = $request->serial_no;

            $area->save();

            DB::commit();

            return $area;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
