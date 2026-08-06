<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;

class AreaController extends Controller
{
    protected $banner;

    public function __construct()
    {
        $this->area = new Area();
        $this->middleware('permission:List Areas', ['only' => ['index']]);
        $this->middleware('permission:View Areas', ['only' => ['show']]);
        $this->middleware('permission:Create Areas', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Areas', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Areas', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['areas'] = Area::orderBy('updated_at','DESC')->get();

        activity('View')->log('List of Areas.');
        return view('area.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = Area::orderBy('name','ASC')->get();

        return view('area.create',compact('areas'));
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
            'name' => ['required','unique:areas'],
            'delivery_charges' => ['required','integer'],
            'min_order_amount' => ['nullable', 'integer'],
            'delivery_charges_above' => ['nullable', 'integer'],
            'min_weight_allow' => ['nullable', 'integer'],
            'extra_charges_per_g_ml' => ['nullable', 'integer'],
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer'],
        ]);


        $area = $this->area->store($request);

        activity('Create')->log('New [ <b>' . $area->name. ' </b> ] Area is created');
        return redirect()->route('areas.index')->with('message', 'Area Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Area $area)
    {
        activity('View')->log(' Show the detail of [ <b>' . $area->name. ' </b> ] Area');
        return view('area.show',compact('area'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Area $area)
    {
        $areas = Area::where('id','!=',$area->id)->orderBy('name','ASC')->get();

        return view('area.edit',compact('area','areas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Area $area)
    {
        $request->validate([
            'name' => ['required','unique:areas,name,'.$area->id],
            'delivery_charges' => ['required','integer'],
            'min_order_amount' => ['nullable', 'integer'],
            'delivery_charges_above' => ['nullable', 'integer'],
            'min_weight_allow' => ['nullable', 'integer'],
            'extra_charges_per_g_ml' => ['nullable', 'integer'],
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer'],
        ]);


        $area = $this->area->updateArea($request,$area);

        activity('Update')->log('[ <b>' . $area->name. ' </b> ] Area is updated');
        return redirect()->route('areas.index')->with('message', 'Area Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $area)
    {
        $area->delete();

        activity('Delete')->log('<b> ' . $area->name. '</b>  Area is deleted');
        return redirect()->route('areas.index')
            ->with('message','Area deleted successfully');
    }

}
