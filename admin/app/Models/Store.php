<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use DB;

class Store extends Model
{
    use HasFactory,SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'address',
        'status',
        'map_address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = ['deleted_at'];

    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $store = new Store();
            $store->name = $request->name;
            $store->phone_number = $request->phone_number;
            $store->status = $request->status;
            $store->address = $request->address;
            $store->map_address = $request->map_address;

            $store->save();

            DB::commit();

            return $store;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateStore($request,$store) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $store->name = $request->name;
            $store->phone_number = $request->phone_number;
            $store->status = $request->status;
            $store->address = $request->address;
            $store->map_address = $request->map_address;

            $store->save();


            DB::commit();

            return $store;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
