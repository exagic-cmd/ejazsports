<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;
use Spatie\Activitylog\Models\Activity;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['allAccounts']= User::orderBy('updated_at','DESC')->get();

        activity('View')->log('List Of All Accounts');
        return view('account.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['roles'] = Role::all();
        $data['stores'] = Store::all();

        return view('account.create',$data);
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'store_id' => $request->store_id
        ]);

        $user->syncRoles($request->get('role'));

        activity('Create')->log('New [ <b>' . $user->name. ' </b> ] Account is created');
        return redirect()->route('accounts.index')->with('message', 'Account Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $account)
    {
        $roles = Role::all();
        $accountRole = $account->roles->pluck('name')->toArray();
        $stores = Store::all();

        return view('account.edit',compact('roles','account','accountRole','stores'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'password' => ['nullable', Rules\Password::defaults()],
        ]);

        $user = User::find($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->status = $request->status;
        $user->store_id = $request->store_id;


        if($request->password)
            $user->password = Hash::make($request->password);

        $user->syncRoles($request->get('role'));
        $user->save();

        activity('Update')->log('[ <b>' . $user->name. ' </b> ] Account is updated');
        return redirect()->route('accounts.index')->with('message', 'Account Created Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $account)
    {
        $account->delete();

        activity('Delete')->log('<b> ' . $account->name. '</b>  Account is deleted');
        return redirect()->route('accounts.index')
            ->with('message','Account deleted successfully');
    }
}
