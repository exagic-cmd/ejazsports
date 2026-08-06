<?php

namespace App\Models;

use App\Http\Controllers\SupplierController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use DB;

class Supplier extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'mobile_number',
        'email',
        'status',
        'ntn_number',
        'opening_balance',
        'closing_blance',
        'office_number'
    ];

    
    

    const SUPPLIER_MANAGE = 1;
    const VEGAS_MANAGE = 2;

    protected $dates = ['deleted_at'];

    
    public function purchaseOrders() {
        return $this->hasMany(PurchaseOrder::class);
    }
    
    public function receivings() {
        return $this->hasMany(Receiving::class);
    }
    
    public function returns() {
        return $this->hasMany(SupplierReturn::class);
    }
    
    public function payments() {
        return $this->hasMany(SupplierPayment::class);
    }

    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $supplier = new Supplier();
            $supplier->name = $request->name;
            $supplier->mobile_number = $request->phone_number;
            // $supplier->office_number = $request->office_number;
            // $supplier->ntn_number = $request->ntn_number;
            // $supplier->email = $request->email;
            $supplier->address = $request->address;
            $supplier->cargo_service = $request->cargo_service;
            
            $supplier->status = $request->status;
            $supplier->opening_balance = $request->opening_balance;

            $supplier->save();

           

            DB::commit();

            return $supplier;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateSupplier($request,$supplier) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $supplier->name = $request->name;
            $supplier->mobile_number = $request->phone_number;
            // $supplier->office_number = $request->office_number;
            // $supplier->ntn_number = $request->ntn_number;
            // $supplier->email = $request->email;
            $supplier->address = $request->address;
            $supplier->cargo_service = $request->cargo_service;
            $supplier->opening_balance = $request->opening_balance;
           
          
            $supplier->status = $request->status;

            $supplier->save();

           

            DB::commit();

            return $supplier;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
