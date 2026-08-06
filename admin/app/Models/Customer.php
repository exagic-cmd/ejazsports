<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'gender',
        'dob',
        'address',
        'area_id',
        'status',
        'store_id',
        'opening_balance',
        'closing_balance',
        'is_website_order',
        'country',
        'state',
        'city',
        'zip',
        'street_address',
        'apt_suite',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_email',
        'shipping_phone_number',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_zip',
        'shipping_street_address',
        'shipping_apt_suite'
        ];

    

    public function store() {
        return $this->belongsTo(Store::class);
    }

    public function area() {
        return $this->belongsTo(Area::class);
    }
    
    public function orders() {
        return $this->hasMany(Order::class);
    }
    
    public function payments() {
        return $this->hasMany(CustomerPayment::class,'customer_id');
    }
    
    public function storeCustomer($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $customer = new Customer();
            $customer->first_name = $request->first_name;
            // $customer->last_name = $request->last_name;
            $customer->phone_number = $request->phone_number;
            // $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->cargo_service = $request->cargo_service;
            $customer->status = $request->status;
            $customer->opening_balance = $request->opening_balance;

            $customer->save();

           

            DB::commit();

            return $customer;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateCustomer($request,$customer) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $customer->first_name = $request->first_name;
            // $customer->last_name = $request->last_name;
            $customer->phone_number = $request->phone_number;
            // $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->cargo_service = $request->cargo_service;
            $customer->status = $request->status;
            $customer->opening_balance = $request->opening_balance;
           
          
           

            $customer->save();

           

            DB::commit();

            return $customer;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
