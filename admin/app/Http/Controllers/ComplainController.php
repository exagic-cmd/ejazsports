<?php

namespace App\Http\Controllers;


use App\Models\ComplainNote;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Complain;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Boolean;

class ComplainController extends Controller
{
    protected $complain;

    public function __construct()
    {
        $this->complain = new Complain();
        $this->middleware('permission:List Complain', ['only' => ['index']]);
        $this->middleware('permission:View Complain', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pendingComplains()
    {
        $data['pendingComplains'] = Complain::where('status',Complain::PENDING)->orderBy('id','ASC')->get();

        activity('View')->log('List of Pending Complains');
        return view('complain.pending',$data);
    }

    public function inprogressComplains()
    {
        $data['inprogressComplains'] = Complain::where('status',Complain::InPROGRESS)->orderBy('id','ASC')->get();

        activity('View')->log('List of InProgress Complains');
        return view('complain.inprogress',$data);
    }

    public function resolvedComplains()
    {
        $data['resolvedComplains'] = Complain::where('status',Complain::RESOLVED)->orderBy('id','ASC')->get();

        activity('View')->log('List of Resolved Complains');
        return view('complain.resolved',$data);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $complain = Complain::find($id);

        //find order
        $orderNo = preg_split('/(?<=[a-z])(?=[0-9]+)/i',$complain->order_no);

        $order = null;
        if (array_key_exists(1, $orderNo))
            $order = Order::where('order_no',$orderNo[1])->first();
        elseif(array_key_exists(1, $orderNo))
            $order = Order::where('order_no',$orderNo[0])->first();

        return view('complain.show',compact('complain','order'));

    }

    public function addNote(Request $request) {

        $note = new ComplainNote();
        $note->complain_id = $request->complain_id;
        $note->user_id = Auth::user()->id;
        $note->note = $request->note;
        $note->save();

        return ['status' => true];
    }

    public function changeStatus(Request $request) {

        $complain = Complain::find($request->complain_id);
        if($complain) {
            $complain->status = $request->status;
            if($request->status == Complain::RESOLVED || $request->status == Complain::CANCELED) {
                $complain->close_date = Carbon::now();
                $request->entertain_date = Carbon::now();
                $note = new ComplainNote();
                $note->complain_id = $request->complain_id;
                $note->user_id = Auth::user()->id;
                $note->note = 'Ticket Close....';
                $note->save();
            }
            if($request->status == Complain::InPROGRESS)
            {
                $request->entertain_date = Carbon::now();
                $note = new ComplainNote();
                $note->complain_id = $request->complain_id;
                $note->user_id = Auth::user()->id;
                $note->note = 'Start investigating the ticket..';
                $note->save();
            }
            $complain->save();

            return ['status' => true];
        }

        return ['status' => false];

    }






}
