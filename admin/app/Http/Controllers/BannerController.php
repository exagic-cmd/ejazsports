<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\File;
use phpDocumentor\Reflection\Types\Boolean;

class BannerController extends Controller
{
    protected $banner;

    public function __construct()
    {
        $this->banner = new Banner();
        $this->middleware('permission:List Banner', ['only' => ['index']]);
        $this->middleware('permission:View Banner', ['only' => ['show']]);
        $this->middleware('permission:Create Banner', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Banner', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Banner', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
        $data['banners'] = Banner::orderBy('updated_at','DESC')->get();
    
        activity('View')->log('List of Banners.');
        return view('content/banner.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('content/banner.create');
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
            'web_image' => ['required','image','mimes:jpeg,jpg,png,svg', 'max:500'],
            'mobile_image' => ['required','image','mimes:jpeg,jpg,png,svg', 'max:500'],
            'web_heading' => ['nullable', 'string', 'max:255'],
            'web_sub_heading' => ['nullable', 'string', 'max:255'],
            'mobile_heading' => ['nullable', 'string', 'max:255'],
            'mobile_sub_heading' => ['nullable', 'string', 'max:255'],
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer'],
        ]);


        $banner = $this->banner->store($request);
        activity('Create')->log('New [ <b>' . $banner->web_heading. ' </b> ] Banner is created');
        return redirect()->route('banners.index')->with('message', 'Banner Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Banner $banner)
    {
        activity('View')->log(' Show the detail of [ <b>' . $banner->heading. ' </b> ] Brand');
        return view('content/banner.show',compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Banner $banner)
    {

        return view('content/banner.edit',compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Banner $banner)
    {
        $request->validate([
            'web_image' => ['nullable','image','mimes:jpeg,jpg,png,svg', 'max:500'],
            'mobile_image' => ['nullable','image','mimes:jpeg,jpg,png,svg', 'max:500'],
            'web_heading' => ['nullable', 'string', 'max:255'],
            'web_sub_heading' => ['nullable', 'string', 'max:255'],
            'mobile_heading' => ['nullable', 'string', 'max:255'],
            'mobile_sub_heading' => ['nullable', 'string', 'max:255'],
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer'],
        ]);


        $banner = $this->banner->updateBanner($request,$banner);

        activity('Update')->log('[ <b>' . $banner->web_heading. ' </b> ] Banner is updated');
        return redirect()->route('banners.index')->with('message', 'Banner Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Brand $banner)
    // {
    //     dd($banner);
    //     File::delete('storage/' . $banner->web_image);
    //     File::delete('storage/' . $banner->mobile_image);
    //     $banner->delete();

    //     activity('Delete')->log('<b> ' . $banner->title. '</b>  Banner is deleted');
    //     return redirect()->route('banners.index')
    //         ->with('message','Banner deleted successfully');
    // }
    public function destroy(Banner $banner)
{
    File::delete('storage/' . $banner->web_image);
    File::delete('storage/' . $banner->mobile_image);
    $banner->delete();

    activity('Delete')->log('<b>' . $banner->title . '</b> Banner is deleted');

    return redirect()->route('banners.index')
        ->with('message','Banner deleted successfully');
}

}
