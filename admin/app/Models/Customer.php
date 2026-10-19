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
        'whatsapp_number',
        'whatsapp_same_as_phone',
        'gender',
        'dob',
        'address',
        'cargo_service',
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

    /** Return the customer's WhatsApp contact in wa.me international format. */
    public function getWhatsappInternationalNumberAttribute()
    {
        $number = $this->whatsapp_same_as_phone
            ? $this->phone_number
            : ($this->whatsapp_number ?: $this->phone_number);
        $digits = preg_replace('/\\D+/', '', (string) $number);

        if (substr($digits, 0, 2) === '00') {
            $digits = substr($digits, 2);
        }

        // Customer phone numbers are entered locally in Pakistan (e.g. 0312... or 312...).
        if (substr($digits, 0, 1) === '0') {
            $digits = '92' . substr($digits, 1);
        } elseif (strlen($digits) === 10 && substr($digits, 0, 1) === '3') {
            $digits = '92' . $digits;
        }

        return $digits;
    }
    
    public function orders() {
        return $this->hasMany(Order::class);
    }
    
    public function payments() {
        return $this->hasMany(CustomerPayment::class,'customer_id');
    }

    public function getRemainingBalanceAttribute()
    {
        $totalBillAmount = Order::where('customer_id', $this->id)->where('status', '!=', 6)->sum('total_amount');
        $totalReturnAmount = Order::where('customer_id', $this->id)->where('return_type', 1)->sum('return_amount');
        $totalPayment = CustomerPayment::where('customer_id', $this->id)->where('status', 2)->sum('amount');
        $totalDiscount = CustomerPayment::where('customer_id', $this->id)->where('status', 2)->sum('discount');

        return round(((($this->opening_balance + $totalBillAmount) - $totalReturnAmount) - $totalPayment) - $totalDiscount, 2);
    }
    
    public function storeCustomer($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $customer = new Customer();
            $customer->first_name = $request->first_name;
            // $customer->last_name = $request->last_name;
            $customer->phone_number = $request->phone_number;
            $customer->whatsapp_same_as_phone = $request->boolean('whatsapp_same_as_phone');
            $customer->whatsapp_number = $customer->whatsapp_same_as_phone ? $request->phone_number : $request->whatsapp_number;
            $customer->email = $request->email;
            $customer->city = $request->city;
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
            $customer->whatsapp_same_as_phone = $request->boolean('whatsapp_same_as_phone');
            $customer->whatsapp_number = $customer->whatsapp_same_as_phone ? $request->phone_number : $request->whatsapp_number;
            $customer->email = $request->email;
            $customer->city = $request->city;
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
