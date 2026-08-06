<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use App\Models\Order;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use App\Models\Size;

class HomeController extends Controller
{
    protected $order;

    public function __construct() {
        $this->order = new Order();
    }
    public function index() {
        

        return view('dashboard');
    }

    /*
     * Get all the Activities
     */
    public function getActivityLog() {

        $data['users'] = User::all();
        $data['activities'] = Activity::where('created_at','>',Carbon::now()->subDays(2))->orderBy('id','DESC')->get();

        return view('activity.index',$data);
    }

    public function searchActivityLog(Request $request) {

        $query = Activity::query();

        //check filter attributes
        if($request->type)
            $query->where('log_name',$request->type);
        if($request->user)
            $query->where('causer_id',$request->user);
        if($request->date)
            $query->whereDate('created_at',$request->date);
        if($request->limit)
            $query->limit($request->limit);

        $data['activities'] = $query->orderBy('id','DESC')->get();

        return view('activity.search',$data);
    }

    public function editProfileForm() {

        $data['account'] = User::find(Auth::user()->id);

        return view('edit-profile',$data);
    }

    public function profileUpdate(Request $request, $id)
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

        if($request->password)
            $user->password = Hash::make($request->password);

        $user->save();

        activity('Update')->log('[ <b>' . $user->name. ' </b> ] Account is updated');
        return redirect()->route('home')->with('message', 'Account Updated Successfully!');
    }


}
