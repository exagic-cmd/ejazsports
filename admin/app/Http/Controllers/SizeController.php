<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;

class SizeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:List Product', ['only' => ['index']]);
        $this->middleware('permission:Create Product', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Product', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Product', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data['sizes'] = Size::all();
        return view('catalog/size.index', $data);
    }

    public function create()
    {
        return view('catalog/size.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sizes'],
        ]);

        Size::create($request->all());

        return redirect()->route('sizes.index')->with('message', 'Size Created Successfully!');
    }

    public function edit(Size $size)
    {
        return view('catalog/size.edit', compact('size'));
    }

    public function update(Request $request, Size $size)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sizes,name,'.$size->id],
        ]);

        $size->update($request->all());

        return redirect()->route('sizes.index')->with('message', 'Size Updated Successfully!');
    }

    public function destroy(Size $size)
    {
        $size->delete();
        return redirect()->route('sizes.index')->with('message', 'Size deleted successfully');
    }
}
