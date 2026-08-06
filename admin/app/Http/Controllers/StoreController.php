<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

class StoreController extends Controller
{
    protected $store;

    public function __construct()
    {
        $this->store = new Store();
        $this->middleware('permission:List Store', ['only' => ['index']]);
        $this->middleware('permission:View Store', ['only' => ['show']]);
        $this->middleware('permission:Create Store', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Store', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Store', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['stores'] = Store::orderBy('updated_at','DESC')->get();

        activity('View')->log('List of Store');
        return view('store.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('store.create');
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
            'name' => ['required', 'string', 'max:255','unique:stores'],
         
            'phone_number' => ['nullable', 'string', 'max:255'],

            'status'=> ['required','boolean'],
            'address' => ['nullable','string'],
            'map_address' => ['string']
        ]);


        $store = $this->store->store($request);

        activity('Create')->log('New [ <b>' . $store->name. ' </b> ] Store is created');
        return redirect()->route('stores.index')->with('message', 'Store Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        activity('View')->log(' Show the detail of [ <b>' . $store->name. ' </b> ] Store');
        return view('store.show',compact('store'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['store'] = Store::find($id);

        return view('store.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255','unique:stores,name,'.$store->id],
      
            'phone_number' => ['nullable', 'string', 'max:255'],

            'status'=> ['required','boolean'],
            'address' => ['nullable','string'],
            'map_address' => ['string']
        ]);


        $store = $this->store->updateStore($request,$store);

        activity('Update')->log('[ <b>' . $store->name. ' </b> ] Store is updated');
        return redirect()->route('stores.index')->with('message', 'Store info Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
