<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'order_no',
        'customer_id',
        'name',
        'email',
        'phone_number',
        'address',
        'city',
        'status',
        'delivery_charges',
        'discount_amount',
        'total_amount',
        'coupon_id',
        'payment_method',
        'paid_amount',
        'fbr_invoice_id',
        'fbr_return_invoice_id',
        'store_id',
        'total_products',
        'total_quantity',
        'cn_no',
        'courier_id',
        'booking_time',
        'scanned',
        'handover',
        'handover_id',
        'dispatch_time',
        'return_date',
        'return_amount',
        'return_type',
        'additional_notes',
        'employee_id',
        'margin',
        'pay_amount',
        'is_website_order',
        'adjust_type'
        ];

    //Order Satus
    const PENDING = 1;
    const BOOKED = 2;
    const SCANNED = 3;
    const DISPATCHED = 4;
    const DELIVERED = 5;
    const RETURNED = 6;
    const CANCELED = 7;
    const COMPLETED = 8;
    const PARTIALLY_RETURNED = 9;

    //Payment Method
    const CASH = 1;
    const ONLINE = 2;
    const QISTPAY = 3;
    const EASYPAISA = 4;
    const JAZZCASH = 5;

    public function products() {
        return $this->hasMany(OrderProduct::class)->with('product','variant');
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function area() {
        return $this->belongsTo(Area::class,'city');
    }

    public function courier() {
        return $this->belongsTo(Courier::class);
    }



}
