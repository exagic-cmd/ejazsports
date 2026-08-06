<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use DB;

class SupplierPayment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'supplier_id',
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
        'status',
        'is_clear'
    ];

    //status
    const APPROVAL_PENDING = 1;
    const APPROVED = 2;

    //Payment method
    const CASH = 1;
    const BANK_TRANSFER = 2;
    const CHEQUE = 3;

    public function supplier() {
        return $this->belongsTo(Supplier::class);
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

            $supplierPayment = new SupplierPayment();
            $supplierPayment->supplier_id = $request->supplier_id;
            $supplierPayment->amount = $request->paid_amount - $request->tax_amount;
            $supplierPayment->tax = $request->tax_amount;
            $supplierPayment->discount = $request->discount;

            // $invoiceNo = '';$i=0;
            // foreach($request->invoice_id as $s) {
            //     if($i == 0) {
            //         $invoiceNo .= $s;
            //         $i = 1;
            //     }
            //     else
            //     $invoiceNo .= ',' . $s;
            // }
            // $supplierPayment->invoice_no = $invoiceNo;

            $supplierPayment->date = $request->date;
            $supplierPayment->received_by = $request->received_by;
            $supplierPayment->payment_method = $request->payment_method;
            $supplierPayment->depositor_bank = $request->payment_method == self::BANK_TRANSFER ? $request->depositor_bank : '';
            $supplierPayment->cheque_no = $request->payment_method == self::CHEQUE ? $request->cheque_no : '';
            $supplierPayment->is_clear = $request->payment_method == self::CHEQUE ? false : true;
            $supplierPayment->cheque_date = $request->payment_method == self::CHEQUE ? $request->cheque_date : null;
            $supplierPayment->notes = $request->notes;
            $supplierPayment->created_by = Auth::user()->id;
            $supplierPayment->status = self::APPROVAL_PENDING;

            if($request->file) {

                 $name = time() . '-' . $request->file('file')->getClientOriginalName();
                 $path = $request->file('file')->storeAs('documents/payment',$name);

                 $supplierPayment->document = $path;

            }

            $supplierPayment->save();

            DB::commit();

            return $supplierPayment;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updatePayment($request, $supplierPayment) {

        DB::beginTransaction();
        try {

            $supplierPayment->supplier_id = $request->supplier_id;
            $supplierPayment->amount = $request->paid_amount - $request->tax_amount;
            $supplierPayment->tax = $request->tax_amount;
            $supplierPayment->discount = $request->discount;

            // $invoiceNo = '';$i=0;
            // foreach($request->invoice_id as $s) {
            //     if($i == 0) {
            //         $invoiceNo .= $s;
            //         $i = 1;
            //     }
            //     else
            //         $invoiceNo .= ',' . $s;
            // }
            // $supplierPayment->invoice_no = $invoiceNo;

            $supplierPayment->date = $request->date;
            $supplierPayment->received_by = $request->received_by;
            $supplierPayment->payment_method = $request->payment_method;
            $supplierPayment->depositor_bank = $request->payment_method == self::BANK_TRANSFER ? $request->depositor_bank : '';
            $supplierPayment->cheque_no = $request->payment_method == self::CHEQUE ? $request->cheque_no : '';
            $supplierPayment->cheque_date = $request->payment_method == self::CHEQUE ? $request->cheque_date : null;
            $supplierPayment->notes = $request->notes;
            
             $supplierPayment->is_clear = $request->is_clear;

            if($request->file) {

                $name = time() . '-' . $request->file('file')->getClientOriginalName();
                $path = $request->file('file')->storeAs('documents/payment',$name);

                $supplierPayment->document = $path;

            }

            $supplierPayment->save();

            DB::commit();

            return $supplierPayment;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
