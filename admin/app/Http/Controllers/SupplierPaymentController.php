<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Receiving;
use App\Models\SupplierPayment;
use App\Models\SupplierReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use DB;

class SupplierPaymentController extends Controller
{
    protected $supplierPayment;

    public function __construct()
    {
        $this->supplierPayment = new SupplierPayment();
        $this->middleware('permission:List Supplier Payment', ['only' => ['index']]);
        $this->middleware('permission:View Supplier Payment', ['only' => ['show']]);
        $this->middleware('permission:Create Supplier Payment', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Supplier Payment', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Supplier Payment', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['supplierPayments'] = SupplierPayment::orderBy('id','DESC')->get();
        $data['allPayments'] = SupplierPayment::where('status',SupplierPayment::APPROVED)->sum(DB::raw('amount - discount'));

        activity('View')->log('List of Supplier Payments');
        return view('payment/supplier.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['suppliers'] = Supplier::orderBy('name','ASC')->get();
        $data['today'] = Carbon::today()->format('Y-m-d');

        return view('payment/supplier.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => ['required'],
            'paid_amount' => ['required', 'numeric','gt:0'],
            'payment_method' => ['required'],
            'received_by' => ['required']
        ]);


        $supplierPayment = $this->supplierPayment->store($request);

        activity('Create')->log('New [ <b>' . $supplierPayment->supplier->name. ' </b> ] Supplier Payment is created');
        return redirect()->route('supplier-payments.index')->with('message', 'Supplier Payment Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupplierPayment  $supplierPayment
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierPayment $supplierPayment)
    {
        // $digit = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
        // $words = $digit->format($supplierPayment->amount);
        $words = $this->numberToWords($supplierPayment->amount);
        $supplierId = $supplierPayment->supplier_id;
        $outstandingBalance = Receiving::where('supplier_id',$supplierId)->sum('net_amount') + $supplierPayment->supplier->opening_balance - SupplierPayment::where('supplier_id',$supplierId)->where('status',SupplierPayment::APPROVED)->where('id','!=',$supplierPayment->id)->sum(DB::raw('amount + discount')) - SupplierReturn::where('supplier_id',$supplierId)->sum('net_amount') ;

        return view('payment/supplier.show',compact('supplierPayment','words','outstandingBalance'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SupplierPayment  $supplierPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(SupplierPayment $supplierPayment)
    {
        $data['supplierPayment'] = $supplierPayment;
        $data['suppliers'] = Supplier::orderBy('name','ASC')->get();
        // $data['invoices'] = explode(',', $supplierPayment->invoice_no);

        return view('payment/supplier.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupplierPayment  $supplierPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierPayment $supplierPayment)
    {
        $request->validate([
            'supplier_id' => ['required'],
            'paid_amount' => ['required', 'numeric','gt:0'],
            'payment_method' => ['required'],
            'received_by' => ['required']
        ]);


        $supplierPayment = $this->supplierPayment->updatePayment($request,$supplierPayment);

        activity('Update')->log('New [ <b>' . $supplierPayment->supplier->name. ' </b> ] Supplier Payment is updated.');
        return redirect()->route('supplier-payments.index')->with('message', 'Supplier Payment Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupplierPayment  $supplierPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierPayment $supplierPayment)
    {
        activity('Delete')->log('New [ <b>' . $supplierPayment->supplier->name. ' </b> ] Supplier Payment is deleted.');
        $supplierPayment->delete();
        
        return redirect()->route('supplier-payments.index')->with('message', 'Supplier Payment Deleted Successfully!');
    }

    public function getSupplierUnPaidInvoices(Request $request) {

        $supplier_id = $request->supplier_id;

        $invoices = Receiving::whereIn('status',[Receiving::APPROVED,Receiving::PARTIALLY_PAID])->select('id','status','po_id','invoice_no')->get();

        return response()->json($invoices);
    }

    public function changePaymentStatus(Request $request) {

        if($request->status && $request->payment_id) {
            SupplierPayment::where('id', $request->payment_id)->update(['status' => $request->status,'approved_by' => Auth::user()->id]);

            if($request->status == SupplierPayment::APPROVED) {

            }
            return ['status'=>true];
        }
        else
            return ['status'=>false];
    }
    
    public function getCheaqueList() {
        
        $data['payments'] = SupplierPayment::where([['payment_method',3],['is_clear',false]])->orderBy('cheque_date','ASC')->get();
        
        $data['allPayments'] = SupplierPayment::where([['payment_method',3],['is_clear',false]])->sum(DB::raw('amount + discount'));
        
        return view('payment/supplier.cheaque',$data);
    }
    
    private function numberToWords($number)
    {
        $words = array(
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
        );

        $levels = array(
            100 => 'hundred',
            1000 => 'thousand',
            100000 => 'lakh',      // Added lakh
            10000000 => 'crore',   // Added crore
        );

        if ($number == 0) {
            return $words[0];
        }

        if (strpos($number, '.') !== false) {
            // Handle decimals
            $parts = explode('.', $number);
            $wholePart = intval($parts[0]);
            $decimalPart = intval($parts[1]);

            $wholePartWords = $this->numberToWords($wholePart);
            $decimalPartWords = $decimalPart > 0 ? $this->numberToWords($decimalPart) . ' cents' : '';

            return trim($wholePartWords . ($decimalPartWords ? ' and ' . $decimalPartWords : '') . ' only');
        }

        $output = '';

        foreach (array_reverse($levels, true) as $value => $label) {
            if ($number >= $value) {
                $count = floor($number / $value);
                $output .= $this->numberToWords($count) . ' ' . $label . ' ';
                $number %= $value;
            }
        }

        if ($number > 0) {
            if ($number < 21) {
                $output .= $words[$number];
            } elseif ($number < 100) {
                $output .= $words[floor($number / 10) * 10];
                if ($number % 10) {
                    $output .= '-' . $words[$number % 10];
                }
            }
        }

        return trim($output);
    }
    

}
