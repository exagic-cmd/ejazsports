<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\PurchaseOrderProduct;
use App\Models\ReceivingProduct;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Carbon\Carbon;

class SupplierController extends Controller
{
    protected $supplier;

    public function __construct()
    {
        $this->supplier = new Supplier();
        $this->middleware('permission:List Supplier', ['only' => ['index']]);
        $this->middleware('permission:View Supplier', ['only' => ['show']]);
        $this->middleware('permission:Create Supplier', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Supplier', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Supplier', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['suppliers'] = Supplier::orderBy('updated_at','DESC')->get();
        
        $balance = array();
        
        foreach($data['suppliers'] as $s) {
            $balance[$s->id] = $s->receivings->sum('net_amount') + $s->opening_balance - $s->payments->sum('amount') - $s->payments->sum('discount') - $s->returns->sum('net_amount');
        }
        
        $data['balance'] = $balance;

        activity('View')->log('List of Suppliers');
        return view('catalog/supplier.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['brands'] = Brand::select('id','title')->get();

        return view('catalog/supplier.create',$data);
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
            'name' => ['required', 'string', 'max:255','unique:suppliers'],
          //  'email' => ['required', 'string', 'max:255'],
   
            'phone_number' => ['required', 'string', 'max:255'],
         
            'status'=> ['required','boolean']
        ]);


        $supplier = $this->supplier->store($request);

        activity('Create')->log('New [ <b>' . $supplier->name. ' </b> ] Supplier is created');
        return redirect()->route('suppliers.index')->with('message', 'Supplier Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        $ledger = array();
        
        
        $temp['date'] = '';
        $temp['invoice/voucher'] = '';
        $temp['narration'] = 'Opening Balance';
        $temp['debit'] = 0;
        $temp['credit'] = $supplier->opening_balance;
        $temp['link'] = 'opening';
        $temp['id'] = 0;
$c=1;
        array_push($ledger,collect($temp));

        
        foreach ($supplier->receivings as $r) {
                $temp['date'] = $r->date;
                $temp['invoice/voucher'] = 'Invoice-'.$c++;
                $temp['narration'] = $r->comment;
                $temp['debit'] = 0;
                $temp['credit'] = $r->net_amount;
                $temp['link'] = 'receiving';
                $temp['id'] = $r->id;
                $temp['method'] = 1;
                $temp['clear'] = true;

                array_push($ledger,collect($temp));
            }
            
        $c=1;
        foreach ($supplier->payments as $r) {
                $temp['date'] = $r->date;
                $temp['invoice/voucher'] = 'Payment-'.$c++;
                $temp['narration'] = $r->notes;
                $temp['debit'] = $r->amount;
                $temp['credit'] = 0;
                $temp['link'] = 'payment';
                $temp['id'] = $r->id;
                $temp['method'] = $r->payment_method;
                $temp['clear'] = $r->is_clear;

                array_push($ledger,collect($temp));
            }
            
            foreach ($supplier->returns as $r) {
                $temp['date'] = $r->date;
                $temp['invoice/voucher'] = 'Return-'.$r->id;
                $temp['narration'] = $r->comment;
                $temp['debit'] = $r->net_amount;
                $temp['credit'] = 0;
                $temp['link'] = 'return';
                $temp['id'] = $r->id;
                $temp['method'] = 1;
                $temp['clear'] = true;

                array_push($ledger,collect($temp));
            }
            
        foreach ($supplier->payments->where('discount','>',0) as $r) {
                $temp['date'] = $r->date;
                $temp['invoice/voucher'] = 'Discount agianst Payment-'.$c++;
                $temp['narration'] = $r->notes;
                $temp['debit'] = $r->discount;
                $temp['credit'] = 0;
                $temp['link'] = 'payment';
                $temp['id'] = $r->id;
                $temp['method'] = 1;
                $temp['clear'] = true;

                array_push($ledger,collect($temp));
            }
            
        $ledger = collect($ledger);
            
        $ledger = $ledger->sortBy('date');
    

        activity('View')->log(' Show the detail of [ <b>' . $supplier->name. ' </b> ] Supplier');
        return view('catalog/supplier.show',compact('supplier','ledger'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['supplier'] = Supplier::find($id);
        $data['brands'] = Brand::select('id','title')->get();

        return view('catalog/supplier.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255','unique:suppliers,name,'.$supplier->id],
          //  'email' => ['required', 'string', 'max:255'],
          
            'phone_number' => ['required', 'string', 'max:255'],
        
            'status'=> ['required','boolean']
        ]);


        $supplier = $this->supplier->updateSupplier($request,$supplier);

        activity('Update')->log('[ <b>' . $supplier->name. ' </b> ] Supplier is updated');
        return redirect()->route('suppliers.index')->with('message', 'Supplier Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        Supplier::where('id',$id)->delete();
        
         activity('Update')->log('[ <b>' . $supplier->name. ' </b> ] Supplier delete');
        return redirect()->back()->with('message', 'Supplier deleted Successfully!');
    }

    /**
     * Get the brand details of specific supplier.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSupplierBrandDetail(Request $request) {

        $data['supplier'] = Supplier::find($request->supplier_id);

        return view('purchase-order.update-supplier-brand-detail',$data);
    }

    /**
     * Get the Product details of specific supplier.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSupplierProductDetail(Request $request) {

        $data['supplier'] = Supplier::find($request->supplier_id);

        $lastPurchasePrice = array();
        foreach($data['supplier']->brands as $brand){
            foreach ($brand->brand->products as $product) {
                $result = ReceivingProduct::where('product_id',$product->id)->orderBy('id','DESC')->first();
                if($result)
                    $lastPurchasePrice[$product->id] = $result->cost_price;
                else
                    $lastPurchasePrice[$product->id] = 0;

            }
        }

        $data['lastPurchasePrice'] = $lastPurchasePrice;

        return view('purchase-order.update-supplier-product-detail',$data);
    }

    /**
     * Get the Product details of specific supplier.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSupplierProductSelectedDetail(Request $request) {

        $data['supplier'] = Supplier::find($request->supplier_id);


        $data['selectedProducts'] = PurchaseOrderProduct::where('purchase_order_id',$request->po_id)->get();

        return view('purchase-order.update-supplier-product-selected-detail',$data);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateReport(Request $request)
    {
        $date = explode('-', $request->date_range);
        $from = Carbon::parse($date[0])->startOfDay();
        $to = Carbon::parse($date[1])->endOfDay();
    
        $supplier = Supplier::find($request->supplier_id);
    
        $ledger = array();
    
        // Calculate opening balance before the 'from' date
        $openingDebit = $supplier->payments->where('date', '<', $from->toDateString())->sum('amount');
        $openingCredit = $supplier->receivings->where('date', '<', $from->toDateString())->sum('net_amount');
        $calculatedOpeningBalance = $supplier->opening_balance + $openingCredit - $openingDebit;
    
        // Add the opening balance to the ledger
        $temp['date'] = '';
        $temp['invoice/voucher'] = '';
        $temp['narration'] = 'Opening Balance';
        $temp['debit'] = 0;
        $temp['credit'] = $calculatedOpeningBalance;
        $temp['link'] = 'opening';
        $temp['id'] = 0;
        array_push($ledger, collect($temp));
    
        // Add transactions within the date range
        $c = 1;
        foreach ($supplier->receivings->whereBetween('date', [$from->toDateString(), $to->toDateString()]) as $r) {
            $temp['date'] = $r->date;
            $temp['invoice/voucher'] = 'Invoice-' . $c++;
            $temp['narration'] = $r->comment;
            $temp['debit'] = 0;
            $temp['credit'] = $r->net_amount;
            $temp['link'] = 'receiving';
            $temp['id'] = $r->id;
            array_push($ledger, collect($temp));
        }
    
        $c = 1;
        foreach ($supplier->payments->whereBetween('date', [$from->toDateString(), $to->toDateString()]) as $r) {
            $temp['date'] = $r->date;
            $temp['invoice/voucher'] = 'Payment-' . $c++;
            $temp['narration'] = $r->notes;
            $temp['debit'] = $r->amount;
            $temp['credit'] = 0;
            $temp['link'] = 'payment';
            $temp['id'] = $r->id;
            array_push($ledger, collect($temp));
        }
    
        // Sort ledger by date
        $ledger = collect($ledger)->sortBy('date');
        
        $date_range = $request->date_range;
    
        return view('catalog/supplier.update-ledger', compact('ledger', 'supplier','date_range'));
    }

    public function nameSuggestions(Request $request)
    {
        $q = trim($request->get('q', ''));
        $currentId = $request->get('current_id');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $words = array_filter(explode(' ', $q));

        $query = Supplier::query();

        if ($currentId) {
            $query->where('id', '!=', $currentId);
        }

        $query->where(function ($group) use ($q, $words) {
            $group->where('name', 'LIKE', '%' . $q . '%')
                ->orWhere('mobile_number', 'LIKE', '%' . $q . '%')
                ->orWhere('office_number', 'LIKE', '%' . $q . '%')
                ->orWhere('email', 'LIKE', '%' . $q . '%')
                ->orWhere('ntn_number', 'LIKE', '%' . $q . '%');

            foreach ($words as $word) {
                if (strlen($word) >= 2) {
                    $group->orWhere('name', 'LIKE', '%' . $word . '%')
                        ->orWhereRaw("SOUNDEX(name) = SOUNDEX(?)", [$word]);
                }
            }
        });

        $suppliers = $query->limit(10)->get();

        $results = $suppliers->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'phone' => $s->mobile_number ?? $s->office_number,
                'email' => $s->email,
                'opening_balance' => number_format($s->opening_balance ?? 0, 2),
                'edit_url' => route('suppliers.edit', $s->id)
            ];
        });

        return response()->json($results);
    }
}

