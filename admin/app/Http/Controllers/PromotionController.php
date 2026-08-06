<?php

namespace App\Http\Controllers;


use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\Promotion;
use phpDocumentor\Reflection\Types\Boolean;

class PromotionController extends Controller
{
    protected $promotion;

    public function __construct()
    {
        $this->promotion = new Promotion();
        $this->middleware('permission:List Promotion', ['only' => ['index']]);
        $this->middleware('permission:View Promotion', ['only' => ['show']]);
        $this->middleware('permission:Create Promotion', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Promotion', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Promotion', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['promotions'] = Promotion::orderBy('updated_at','DESC')->get();

        activity('View')->log('List of Promotion Banners');
        return view('content/promotion.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('content/promotion.create');
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
            'web_count' => ['required'],
            'mobile_count' => ['required'],
            'status' => ['required'],
            'images' => ['required','array','min:1']
        ]);

        $promotion = $this->promotion->store($request);

        activity('Create')->log('New [ <b>' . $promotion->detail. ' </b> ] Promotion banner is created');
        return redirect()->route('promotion.index')->with('message', 'Promotion Banner Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Promotion $promotion)
    {
        activity('View')->log(' Show the detail of [ <b>' . $promotion->name. ' </b> ] promotion banner');
        return view('content/promotion.show',compact('promotion'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Promotion $promotion)
    {

        return view('content/promotion.edit',compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Promotion $promotion)
    {
        $request->validate([
            'web_count' => ['required'],
            'mobile_count' => ['required'],
            'status' => ['required']
        ]);

        $promotion = $this->promotion->updatePromotion($request,$promotion);

        activity('Update')->log('[ <b>' . $promotion->detail. ' </b> ] Promotion Banner is updated');
        return redirect()->route('promotion.index')->with('message', 'Promotion Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        activity('Delete')->log('<b> ' . $promotion->detail. '</b>  Promotion Banner is deleted');
        return redirect()->route('promotion.index')
            ->with('message','Promotion deleted successfully');
    }


}
