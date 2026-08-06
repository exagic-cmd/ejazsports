<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::orderBy('updated_at','DESC')->get();

        activity('View')->log('List Of All Permissions');
        return view('account/permissions.index', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Show form for creating permissions
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('account/permissions.create');
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
            'name' => 'required|unique:permissions,name',
            'system_name' => 'required',
            'module' => 'required',
            'sub_module' => 'required'
        ]);

        $permission = new Permission();
        $permission->name = $request->name;
        $permission->guard_name = 'web';
        $permission->system_name = $request->system_name;
        $permission->module = $request->module;
        $permission->sub_module = $request->sub_module;
        $permission->save();


        activity('Create')->log('New [ <b>' . $permission->name. ' </b> ] Permission is created');
        return redirect()->route('permissions.index')->with('message', 'Permission Created Successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Permission  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        return view('account/permissions.edit', [
            'permission' => $permission
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'system_name' => 'required',
            'module' => 'required',
            'sub_module' => 'required'
        ]);

        $permission->name = $request->name;
        $permission->system_name = $request->system_name;
        $permission->module = $request->module;
        $permission->sub_module = $request->sub_module;
        $permission->save();

        activity('Update')->log(' <b>' . $permission->name. ' </b> Permission is updated');
        return redirect()->route('permissions.index')->with('message', 'Permission Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        activity('Delete')->log('<b> ' . $permission->name. '</b>  Account is deleted');
        return redirect()->route('permissions.index')
            ->with('message','Role deleted successfully');
    }
}
