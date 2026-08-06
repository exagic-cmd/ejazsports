<?php

namespace App\Http\Controllers;


use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerPayment;
use DB;
use Carbon\Carbon;

class CustomerController extends Controller
{
    protected $customer;

    public function __construct()
    {
        $this->customer = new Customer();
        $this->middleware('permission:List Customer', ['only' => ['index']]);
        $this->middleware('permission:View Customer', ['only' => ['show']]);
        $this->middleware('permission:Create Customer', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Customer', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Customer', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['customers'] = Customer::orderBy('updated_at','DESC')->limit(50)->get();

        $totalOrders = array();
        $orderAmount = array();
        $balance  = array();
        foreach ($data['customers'] as $customer) {
            $totalOrders[$customer->id] = Order::where('customer_id',$customer->id)->count();
            $orderAmount[$customer->id] = Order::where('customer_id',$customer->id)->where('status','!=',6)->sum(DB::raw('total_amount - return_amount'));
            $totalBillAmount = Order::where('customer_id',$customer->id)->where('status','!=',6)->sum('total_amount');
            $totalReturnAmount = Order::where('customer_id',$customer->id)->where('return_type',1)->sum('return_amount');
            
            $totalPayment = CustomerPayment::where('customer_id',$customer->id)->where('status',2)->sum('amount');
            $totalDiscount = CustomerPayment::where('customer_id',$customer->id)->where('status',2)->sum('discount');
            
            $balance[$customer->id] = (((($customer->opening_balance + $totalBillAmount) - $totalReturnAmount) - $totalPayment) - $totalDiscount);
        }
        $data['totalOrders'] = $totalOrders;
        $data['orderAmount'] = $orderAmount;
        $data['balance']  = $balance;

        activity('View')->log('List of Customers');
        return view('customer.index',$data);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        return view('customer.create');
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
            'first_name' => ['required', 'string', 'max:255','unique:customers'],
         //   'email' => ['required', 'string', 'max:255'],
   
            'phone_number' => ['required', 'string', 'max:255'],
         
            'status'=> ['required','boolean']
        ]);


        $customer = $this->customer->storeCustomer($request);

        activity('Create')->log('New [ <b>' . $customer->first_name. ' </b> ] Customer is created');
        return redirect()->route('customers.index')->with('message', 'Customer Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $ledger = array();
        
        
        $temp['date'] = '';
        $temp['invoice/voucher'] = '';
        $temp['narration'] = 'Opening Balance';
        $temp['debit'] = 0;
        $temp['credit'] = $customer->opening_balance;
        $temp['link'] = 'opening';

        array_push($ledger,collect($temp));
        
        $returnOrders = Order::where('customer_id',$customer->id)->where('return_amount' ,'>',0)->get();

        
        foreach ($customer->orders as $r) {
            if($r->status != 6) {
                $temp['date'] = Carbon::parse($r->created_at)->toDateString();
                $temp['invoice/voucher'] = 'Bill-'.$r->order_no;
                $temp['narration'] = '';
                $temp['debit'] = 0;
                $temp['credit'] = $r->total_amount;
                $temp['link'] = 'order';
                $temp['id'] = $r->id;

                array_push($ledger,collect($temp));
        }
            }
            
            
            
            foreach ($customer->payments->where('status',2) as $r) {
                $temp['date'] = $r->date;
                $temp['invoice/voucher'] = 'Payment-'.$r->id;
                $temp['narration'] = $r->notes;
                $temp['debit'] = $r->amount;
                $temp['credit'] = 0;
                $temp['link'] = 'payment';
                $temp['id'] = $r->id;

                array_push($ledger,collect($temp));
            }
            
            //discounts
            foreach ($customer->payments->where('status',2)->where('discount','>',0) as $r) {
                $temp['date'] = $r->date;
                $temp['invoice/voucher'] = 'Discount against Payment-'.$r->id;
                $temp['narration'] = $r->notes;
                $temp['debit'] = $r->discount;
                $temp['credit'] = 0;
                $temp['link'] = 'payment';
                $temp['id'] = $r->id;

                array_push($ledger,collect($temp));
            }
            
            foreach ($returnOrders as $r) {
                if($r->return_type == 1) {
                    $temp['date'] = $r->return_date;
                    $temp['invoice/voucher'] = 'Return-'.$r->order_no;
                    $temp['narration'] = '';
                    $temp['debit'] = $r->return_amount;
                    $temp['credit'] = 0;
                    $temp['link'] = 'return';
                    $temp['id'] = $r->id;
    
                    array_push($ledger,collect($temp));
                }
            }
            
            $ledger = collect($ledger);
            
            $ledger = $ledger->sortBy('date');
    

        activity('View')->log(' Show the detail of [ <b>' . $customer->first_name. ' </b> ] Customer');
        return view('customer.show',compact('customer','ledger'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['customer'] = Customer::find($id);
      

        return view('customer.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255','unique:customers,first_name,'.$customer->id],
         //   'email' => ['required', 'string', 'max:255'],
          
            'phone_number' => ['required', 'string', 'max:255']
        ]);


        $customer = $this->customer->updateCustomer($request,$customer);

        activity('Update')->log('[ <b>' . $customer->first_name. ' </b> ] Customer is updated');
        return redirect()->route('customers.index')->with('message', 'Customer Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        Customer::where('id',$id)->delete();
        
         activity('Update')->log('[ <b>' . $customer->first_name. ' </b> ] Customer delete');
        return redirect()->back()->with('message', 'Customer deleted Successfully!');
    }
    
    public function searchCustomer(Request $request) {

        $query = Customer::query();
        
        if($request->searchbox) {
            $query = $query->where('first_name', 'LIKE', '%'.$request->searchbox.'%')->orWhere('last_name', 'LIKE', '%'.$request->searchbox.'%')
                ->orWhere('phone_number', 'LIKE', '%'.$request->searchbox.'%');
        }
       
        $data['customers'] = $query->get();
        
        $totalOrders = array();
        $orderAmount = array();
        $balance  = array();
        foreach ($data['customers'] as $customer) {
            $totalOrders[$customer->id] = Order::where('customer_id',$customer->id)->count();
            $orderAmount[$customer->id] = Order::where('customer_id',$customer->id)->where('status','!=',6)->sum(DB::raw('total_amount - return_amount'));
            $totalBillAmount = Order::where('customer_id',$customer->id)->where('status','!=',6)->sum('total_amount');
            $totalReturnAmount = Order::where('customer_id',$customer->id)->where('return_type',1)->sum('return_amount');
            
            $totalPayment = CustomerPayment::where('customer_id',$customer->id)->where('status',2)->sum('amount');
            $totalDiscount = CustomerPayment::where('customer_id',$customer->id)->where('status',2)->sum('discount');
            
            $balance[$customer->id] = (((($customer->opening_balance + $totalBillAmount) - $totalReturnAmount) - $totalPayment) - $totalDiscount);
        }
        $data['totalOrders'] = $totalOrders;
        $data['orderAmount'] = $orderAmount;
        $data['balance']  = $balance;

        return view('customer.search',$data);
    }
    
    public function updateReport(Request $request)
    {
       
        // Parse the date range from the request
        $date = explode('-', $request->date_range);
        $from = Carbon::parse($date[0])->startOfDay();
        $to = Carbon::parse($date[1])->endOfDay();
        
        $customer = Customer::find($request->customer_id);
        
       
    
        $ledger = [];
    
        // Calculate opening balance before the start date
        $openingBalance = $customer->opening_balance;
    
        // Add all debits and credits before the start date
        $priorTransactions = collect();
    
        // Orders (Credits)
        $priorTransactions = $priorTransactions->merge(
            $customer->orders->where('created_at', '<', $from)->map(function ($r) {
                return [
                    'debit' => 0,
                    'credit' => $r->total_amount,
                ];
            })
        );
        
       
    
        // Payments (Debits and Discounts)
        $priorTransactions = $priorTransactions->merge(
            $customer->payments
                ->where('status', 2)
                ->where('date', '<', $from->toDateString())
                ->map(function ($r) {
                    return [
                        'debit' => $r->amount + $r->discount,
                        'credit' => 0,
                    ];
                })
        );
        
       // dd($priorTransactions);
    
        // Return Orders (Debits)
        $priorTransactions = $priorTransactions->merge(
            Order::where('customer_id', $customer->id)
                ->where('return_amount', '>', 0)
                ->where('return_date', '<', $from)
                ->get()
                ->map(function ($r) {
                    return [
                        'debit' => $r->return_amount,
                        'credit' => 0,
                    ];
                })
        );
    
        // Sum prior transactions to calculate the cumulative opening balance
        foreach ($priorTransactions as $transaction) {
            $openingBalance += ($transaction['credit'] - $transaction['debit']);
        }
    
        // Add opening balance entry
        $temp['date'] = '';
        $temp['invoice/voucher'] = '';
        $temp['narration'] = 'Opening Balance';
        $temp['debit'] = 0;
        $temp['credit'] = $openingBalance;
        $temp['link'] = 'opening';
    
        array_push($ledger, collect($temp));
    
        // Add transactions within the selected date range
        $returnOrders = Order::where('customer_id', $customer->id)
            ->where('return_amount', '>', 0)
            ->whereBetween('return_date', [$from, $to])
            ->get();
    
        foreach ($customer->orders->whereBetween('created_at', [$from, $to]) as $r) {
            if ($r->status != 6) {
                $temp['date'] = $r->created_at;
                $temp['invoice/voucher'] = 'Bill-' . $r->order_no;
                $temp['narration'] = '';
                $temp['debit'] = 0;
                $temp['credit'] = $r->total_amount;
                $temp['link'] = 'order';
                $temp['id'] = $r->id;
    
                array_push($ledger, collect($temp));
            }
        }
    
        foreach ($customer->payments->where('status', 2)->whereBetween('date', [$from->toDateString(), $to->toDateString()]) as $r) {
            $temp['date'] = $r->date;
            $temp['invoice/voucher'] = 'Payment-' . $r->id;
            $temp['narration'] = $r->notes;
            $temp['debit'] = $r->amount;
            $temp['credit'] = 0;
            $temp['link'] = 'payment';
            $temp['id'] = $r->id;
    
            array_push($ledger, collect($temp));
        }
    
        foreach ($customer->payments->where('status', 2)->where('discount', '>', 0)->whereBetween('date', [$from->toDateString(), $to->toDateString()]) as $r) {
            $temp['date'] = $r->date;
            $temp['invoice/voucher'] = 'Discount against Payment-' . $r->id;
            $temp['narration'] = $r->notes;
            $temp['debit'] = $r->discount;
            $temp['credit'] = 0;
            $temp['link'] = 'payment';
            $temp['id'] = $r->id;
    
            array_push($ledger, collect($temp));
        }
    
        foreach ($returnOrders as $r) {
            if ($r->return_type == 1) {
                $temp['date'] = $r->return_date;
                $temp['invoice/voucher'] = 'Return-' . $r->order_no;
                $temp['narration'] = '';
                $temp['debit'] = $r->return_amount;
                $temp['credit'] = 0;
                $temp['link'] = 'return';
                $temp['id'] = $r->id;
    
                array_push($ledger, collect($temp));
            }
        }
    
        // Sort the ledger by date
        $ledger = collect($ledger)->sortBy('date');
        
         $date_range = $request->date_range;
    
         return view('customer.update-ledger', compact('ledger', 'customer','date_range'));
    }

    public function nameSuggestions(Request $request)
    {
        $q = trim($request->get('q', ''));
        $currentId = $request->get('current_id');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $words = array_filter(explode(' ', $q));

        $query = Customer::query();

        if ($currentId) {
            $query->where('id', '!=', $currentId);
        }

        $query->where(function ($group) use ($q, $words) {
            $group->where(DB::raw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,''))"), 'LIKE', '%' . $q . '%')
                ->orWhere('first_name', 'LIKE', '%' . $q . '%')
                ->orWhere('last_name', 'LIKE', '%' . $q . '%')
                ->orWhere('phone_number', 'LIKE', '%' . $q . '%')
                ->orWhere('email', 'LIKE', '%' . $q . '%');

            foreach ($words as $word) {
                if (strlen($word) >= 2) {
                    $group->orWhere('first_name', 'LIKE', '%' . $word . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $word . '%')
                        ->orWhereRaw("SOUNDEX(first_name) = SOUNDEX(?)", [$word])
                        ->orWhereRaw("SOUNDEX(last_name) = SOUNDEX(?)", [$word]);
                }
            }
        });

        $customers = $query->limit(10)->get();

        $results = $customers->map(function ($c) {
            $fullName = trim($c->first_name . ' ' . $c->last_name);
            return [
                'id' => $c->id,
                'name' => $fullName,
                'first_name' => $c->first_name,
                'last_name' => $c->last_name,
                'phone' => $c->phone_number,
                'email' => $c->email,
                'opening_balance' => number_format($c->opening_balance ?? 0, 2),
                'show_url' => route('customers.show', $c->id)
            ];
        });

        return response()->json($results);
    }
}

