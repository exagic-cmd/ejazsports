<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\TopBarContent;
use phpDocumentor\Reflection\Types\Boolean;

class TopBarContentController extends Controller
{
    protected $topBarContent;

    public function __construct()
    {
        $this->topBarContent = new TopBarContent();
        $this->middleware('permission:List Top Bar Content', ['only' => ['index']]);
        $this->middleware('permission:View Top Bar Content', ['only' => ['show']]);
        $this->middleware('permission:Create Top Bar Content', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Top Bar Content', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Top Bar Content', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['topBarContents'] = TopBarContent::orderBy('updated_at','DESC')->get();


        activity('View')->log('List of Top Bar Content.');
        return view('content/top-bar.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['topBarContents'] = TopBarContent::select('id','text','serial_no')->orderBy('serial_no','ASC')->get();

        return view('content/top-bar.create',$data);
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
            'text' => ['required', 'string', 'max:255'],
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer']
        ]);

        $topBarContent = $this->topBarContent->store($request);

        activity('Create')->log('New [ <b>' . $topBarContent->text. ' </b> ] Top Bar is created');
        return redirect()->route('top-bar.index')->with('message', 'Top Bar Content Created Successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $topBarContent = TopBarContent::find($id);
        $topBarContents = TopBarContent::where('id','!=',$topBarContent->id)->select('id','text','serial_no')->orderBy('serial_no','ASC')->get();


        return view('content/top-bar.edit',compact('topBarContent','topBarContents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'text' => ['required', 'string', 'max:255'],
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer']
        ]);

        $topBarContents = $this->topBarContent->updateTopBarContent($request,$id);

        activity('Update')->log('[ <b>' . $topBarContents->text. ' </b> ] Top Bar Content is updated');
        return redirect()->route('top-bar.index')->with('message', 'Top Bar Content Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $topBarContent = TopBarContent::find($id);
        TopBarContent::where('id',$id)->delete();

        activity('Delete')->log('<b> ' . $topBarContent->text . '</b>  Top Bar Content is deleted');
        return redirect()->route('top-bar.index')
            ->with('message','Top Bar Content deleted successfully');
    }


}
