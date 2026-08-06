<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\CustomerPayment;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    protected $employee;

    public function __construct()
    {
        $this->employee = new Employee();
        $this->middleware('permission:List Employee', ['only' => ['index']]);
        $this->middleware('permission:View Employee', ['only' => ['show']]);
        $this->middleware('permission:Create Employee', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Employee', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Employee', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['employees'] = Employee::orderBy('updated_at','DESC')->get();

        activity('View')->log('List of Employees');
        return view('employee.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('employee.create');
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
            'name' => ['required', 'string', 'max:255','unique:employees'],
            'mobile_number' => ['required', 'string', 'max:255'],
            'com_per_retail' => ['required'],
            'com_per_whole' => ['required'],
            'status'=> ['required','boolean']
        ]);

        $employee = $this->employee->store($request);

        activity('Create')->log('New [ <b>' . $employee->name. ' </b> ] Employee is created');
        return redirect()->route('employees.index')->with('message', 'Employee Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        $payments = CustomerPayment::where('date',Carbon::today())->get();
        
        $returnOrders = Order::whereDate('return_date',Carbon::today())->where('employee_id',$employee->id)->get();

        activity('View')->log(' Show the detail of [ <b>' . $employee->name. ' </b> ] Employee');
        return view('employee.show',compact('employee','payments','returnOrders'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['employee'] = Employee::find($id);
        return view('employee.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255','unique:employees,name,'.$employee->id],
            'mobile_number' => ['required', 'string', 'max:255'],
            'com_per_retail' => ['required'],
            'com_per_whole' => ['required'],
            'status'=> ['required','boolean']
        ]);

        $employee = $this->employee->updateEmployee($request,$employee);

        activity('Update')->log('[ <b>' . $employee->name. ' </b> ] Employee is updated');
        return redirect()->route('employees.index')->with('message', 'Employee Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);
        Employee::where('id',$id)->delete();
        
        activity('Update')->log('[ <b>' . $employee->name. ' </b> ] Employee delete');
        return redirect()->back()->with('message', 'Employee deleted Successfully!');
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

        $orders = Order::where('employee_id', $request->employee_id)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $returnOrders = Order::where('employee_id', $request->employee_id)
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween(DB::raw('DATE(return_date)'), [$from->toDateString(), $to->toDateString()])
                  ->orWhere(function ($sub) use ($from, $to) {
                      $sub->whereIn('status', [Order::RETURNED, Order::PARTIALLY_RETURNED])
                          ->whereBetween('updated_at', [$from, $to]);
                  });
            })
            ->get();

        $employee = Employee::find($request->employee_id);

        $payments = CustomerPayment::whereBetween(DB::raw('DATE(date)'), [$from->toDateString(), $to->toDateString()])
            ->get();

        return view('employee.update-report', compact('orders', 'employee', 'payments', 'returnOrders'));
    }
}
