<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use DB;

class CustomerPayment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'customer_id',
        'amount',
        'date',
        'invoice_no',
        'payment_method',
        'depositor_bank',
        'cheque_no',
        'cheque_date',
        'notes',
        'document',
        'tax',
        'discount',
        'created_by',
        'approved_by',
        'received_by',
        'status'
    ];

    //status
    const APPROVAL_PENDING = 1;
    const APPROVED = 2;

    //Payment method
    const CASH = 1;
    const BANK_TRANSFER = 2;
    const CHEQUE = 3;

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class,'created_by');
    }
    public function approvedBy() {
        return $this->belongsTo(User::class,'approved_by');
    }

    public function store($request) {

        DB::beginTransaction();
        try {

            $customerPayment = new CustomerPayment();
            $customerPayment->customer_id = $request->customer_id;
            $customerPayment->amount = $request->paid_amount;
            $customerPayment->tax = $request->tax_amount;
            $customerPayment->discount = $request->discount;

            // $invoiceNo = '';$i=0;
            // foreach($request->invoice_id as $s) {
            //     if($i == 0) {
            //         $invoiceNo .= $s;
            //         $i = 1;
            //     }
            //     else
            //     $invoiceNo .= ',' . $s;
            // }
            // $customerPayment->invoice_no = $invoiceNo;

            $customerPayment->date = $request->date;
            $customerPayment->received_by = $request->received_by;
            $customerPayment->payment_method = $request->payment_method;
            $customerPayment->depositor_bank = $request->payment_method == self::BANK_TRANSFER ? $request->depositor_bank : '';
            $customerPayment->cheque_no = $request->payment_method == self::CHEQUE ? $request->cheque_no : '';
            $customerPayment->cheque_date = $request->payment_method == self::CHEQUE ? $request->cheque_date : null;
            $customerPayment->notes = $request->notes;
            $customerPayment->created_by = Auth::user()->id;
            $customerPayment->status = self::APPROVAL_PENDING;

            if($request->file) {

                 $name = time() . '-' . $request->file('file')->getClientOriginalName();
                 $path = $request->file('file')->storeAs('documents/payment',$name);

                 $customerPayment->document = $path;

            }

            $customerPayment->save();

            DB::commit();

            return $customerPayment;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updatePayment($request, $customerPayment) {

        DB::beginTransaction();
        try {

            $customerPayment->customer_id = $request->customer_id;
            $customerPayment->amount = $request->paid_amount;
            $customerPayment->tax = $request->tax_amount;
              $customerPayment->discount = $request->discount;

            // $invoiceNo = '';$i=0;
            // foreach($request->invoice_id as $s) {
            //     if($i == 0) {
            //         $invoiceNo .= $s;
            //         $i = 1;
            //     }
            //     else
            //         $invoiceNo .= ',' . $s;
            // }
            // $customerPayment->invoice_no = $invoiceNo;

            $customerPayment->date = $request->date;
            $customerPayment->received_by = $request->received_by;
            $customerPayment->payment_method = $request->payment_method;
            $customerPayment->depositor_bank = $request->payment_method == self::BANK_TRANSFER ? $request->depositor_bank : '';
            $customerPayment->cheque_no = $request->payment_method == self::CHEQUE ? $request->cheque_no : '';
            $customerPayment->cheque_date = $request->payment_method == self::CHEQUE ? $request->cheque_date : null;
            $customerPayment->notes = $request->notes;

            if($request->file) {

                $name = time() . '-' . $request->file('file')->getClientOriginalName();
                $path = $request->file('file')->storeAs('documents/payment',$name);

                $customerPayment->document = $path;

            }

            $customerPayment->save();

            DB::commit();

            return $customerPayment;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
