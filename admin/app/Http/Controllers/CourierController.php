<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\CourierHandoverOrder;
use Illuminate\Http\Request;
use App\Models\Courier;

class CourierController extends Controller
{
    protected $courier;

    public function __construct()
    {
        $this->courier = new Courier();
        $this->middleware('permission:List Couriers', ['only' => ['index']]);
        $this->middleware('permission:View Couriers', ['only' => ['show']]);
        $this->middleware('permission:Create Couriers', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Couriers', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Couriers', ['only' => ['destroy']]);
        $this->middleware('permission:List Courier Handovers',['only' => ['getHandoverList']]);
        $this->middleware('permission:Courier Handover Detail',['only' => ['getHandoverDetail']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['couriers'] = Courier::with('areas')->orderBy('updated_at','DESC')->get();

        activity('View')->log('List of Couriers.');
        return view('courier.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = Area::orderBy('name','DESC')->get();

        return view('courier.create',compact('areas'));
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
            'name' => ['required','unique:couriers'],
            'rate' => ['required','numeric'],
            'allow_weight_gm_ml' => ['nullable', 'integer'],
            'extra_charges_above_weight' => ['nullable', 'integer'],
            'status'=> ['required','boolean'],
            'area_id' => ['required','array','min:1'],
        ]);


        $courier = $this->courier->store($request);

        activity('Create')->log('New [ <b>' . $courier->name. ' </b> ] Courier is created');
        return redirect()->route('couriers.index')->with('message', 'Courier Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Courier $courier)
    {
        activity('View')->log(' Show the detail of [ <b>' . $courier->name. ' </b> ] Courier');
        return view('courier.show',compact('courier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Courier $courier)
    {
        $areas = Area::orderBy('name','DESC')->get();

        $selectedAreas = array();
        foreach ($courier->areas as $a) {
            array_push($selectedAreas, $a->area_id);
        }


        return view('courier.edit',compact('courier','areas','selectedAreas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Courier $courier)
    {
        $request->validate([
            'name' => ['required','unique:couriers,name,'.$courier->id],
            'rate' => ['required','numeric'],
            'allow_weight_gm_ml' => ['nullable', 'integer'],
            'extra_charges_above_weight' => ['nullable', 'integer'],
            'status'=> ['required','boolean'],
            'area_id' => ['required','array','min:1'],
        ]);


        $courier = $this->courier->updateCourier($request,$courier);

        activity('Update')->log('[ <b>' . $courier->name. ' </b> ] Courier is updated');
        return redirect()->route('couriers.index')->with('message', 'Courier Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Courier $courier)
    {
        $courier->delete();

        activity('Delete')->log('<b> ' . $courier->name. '</b>  Courier is deleted');
        return redirect()->route('couriers.index')
            ->with('message','Courier deleted successfully');
    }

    public function getHandoverList() {

        $handovers = CourierHandoverOrder::with('courier')->orderBy('created_at','DESC')->get();

        activity('View')->log('Courier Handover List');
        return view('courier.handover',compact('handovers'));
    }

    public function getHandoverDetail(CourierHandoverOrder $handover) {

        return view('courier.handover-detail',compact('handover'));
    }

}
