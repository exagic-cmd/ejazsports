<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;

class ColorController extends Controller
{
    public function __construct()
    {
        // Adding middlewares for permissions if needed, but keeping it simple for now or using similar ones
        $this->middleware('permission:List Product', ['only' => ['index']]);
        $this->middleware('permission:Create Product', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Product', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Product', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data['colors'] = Color::all();
        return view('catalog/color.index', $data);
    }

    public function create()
    {
        return view('catalog/color.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:colors'],
        ]);

        Color::create($request->all());

        return redirect()->route('colors.index')->with('message', 'Color Created Successfully!');
    }

    public function edit(Color $color)
    {
        return view('catalog/color.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:colors,name,'.$color->id],
        ]);

        $color->update($request->all());

        return redirect()->route('colors.index')->with('message', 'Color Updated Successfully!');
    }

    public function destroy(Color $color)
    {
        $color->delete();
        return redirect()->route('colors.index')->with('message', 'Color deleted successfully');
    }
}
