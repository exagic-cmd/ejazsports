<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Receiving;
use App\Models\CustomerPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use DB;

class CustomerPaymentController extends Controller
{
    protected $customerPayment;

    public function __construct()
    {
        $this->customerPayment = new CustomerPayment();
        $this->middleware('permission:List Customer Payment', ['only' => ['index']]);
        $this->middleware('permission:View Customer Payment', ['only' => ['show']]);
        $this->middleware('permission:Create Customer Payment', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Customer Payment', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Customer Payment', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['customerPayments'] = CustomerPayment::orderBy('date','DESC')->paginate(50);
        
        $data['allPayments'] = CustomerPayment::where('status',CustomerPayment::APPROVED)->sum('amount');

        activity('View')->log('List of Customer Payments');
        return view('payment/customer.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['customers'] = Customer::orderBy('first_name','ASC')->get();
        $data['today'] = Carbon::today()->format('Y-m-d');

        return view('payment/customer.create',$data);
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
            'customer_id' => ['required'],
            'paid_amount' => ['required', 'numeric'],
            'payment_method' => ['required']
        ]);


        $customerPayment = $this->customerPayment->store($request);

        activity('Create')->log('New [ <b>' . $customerPayment->customer->name. ' </b> ] Customer Payment is created');
        return redirect()->route('customer-payments.index')->with('message', 'Customer Payment Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\customerPayment  $customerPayment
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerPayment $customerPayment)
    {
        // $digit = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
        // $words = $digit->format($customerPayment->amount);
        $words = $this->numberToWords($customerPayment->amount);
        $customerId = $customerPayment->customer_id;
        
         $totalBillAmount = Order::where('customer_id',$customerId)->where('status','!=',6)->sum('total_amount');
            $totalReturnAmount = Order::where('customer_id',$customerId)->where('return_type',1)->sum('return_amount');
            
            $totalPayment = CustomerPayment::where('id','!=',$customerPayment->id)->where('customer_id',$customerId)->where('status',2)->sum('amount');
            $totalDiscount = CustomerPayment::where('id','!=',$customerPayment->id)->where('customer_id',$customerId)->where('status',2)->sum('discount');
            
            $outstandingBalance = (((($customerPayment->customer->opening_balance + $totalBillAmount) - $totalReturnAmount) - $totalPayment) - $totalDiscount);
        
        // $outstandingBalance = $customerPayment->customer->opening_balance + Order::where('customer_id',$customerId)->sum(DB::raw('total_amount - return_amount')) - CustomerPayment::where('status',2)->where('customer_id',$customerId)->where('id','!=',$customerPayment->id)->sum(DB::raw('amount + discount'));

        return view('payment/customer.show',compact('customerPayment','words','outstandingBalance'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\customerPayment  $customerPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerPayment $customerPayment)
    {
        $data['customerPayment'] = $customerPayment;
        $data['customers'] = Customer::orderBy('first_name','ASC')->get();
        // $data['invoices'] = explode(',', $customerPayment->invoice_no);

        return view('payment/customer.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customerPayment  $customerPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerPayment $customerPayment)
    {
        $request->validate([
            'customer_id' => ['required'],
            'paid_amount' => ['required', 'numeric','gt:0'],
            'payment_method' => ['required']
        ]);


        $customerPayment = $this->customerPayment->updatePayment($request,$customerPayment);

        activity('Update')->log('New [ <b>' . $customerPayment->customer->name. ' </b> ] Customer Payment is updated.');
        return redirect()->route('customer-payments.index')->with('message', 'Customer Payment Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\customerPayment  $customerPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(customerPayment $customerPayment)
    {
        
        activity('Delete')->log('New [ <b>' . $customerPayment->customer->name. ' </b> ] Customer Payment is deleted.');
        $customerPayment->delete();
        
        return redirect()->route('customer-payments.index')->with('message', 'Customer Payment Deleted Successfully!');
    }

   

    public function changePaymentStatus(Request $request) {

        if($request->status && $request->payment_id) {
            CustomerPayment::where('id', $request->payment_id)->update(['status' => $request->status,'approved_by' => Auth::user()->id]);

            if($request->status == CustomerPayment::APPROVED) {

            }
            return ['status'=>true];
        }
        else
            return ['status'=>false];
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
