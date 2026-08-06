<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Models\Followup;
use App\Models\FollowupDetail;
use App\Models\CustomerPayment;

use Carbon\Carbon;
use DB;

class FollowupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function __construct()
    {
    }
    
    public function getAutoFollowUp() {
        
        
        //customers whoese orders last 15 days ago 92
        
        $totalCustomerIds = Customer::where('id','!=',1)->where('opening_balance','>',0)->pluck('id')->toArray();
        
        $orderIds = Order::whereDate('created_at','>',Carbon::now()->subDays(15))->groupBy('customer_id')->pluck('customer_id')->toArray();
        
        $remIds = array_diff($totalCustomerIds,$orderIds);
        
        
        $result = CustomerPayment::whereDate('date','>',Carbon::now()->subDays(15))->pluck('customer_id')->toArray();
        
        $remIds = array_diff($remIds,$result);
        
        
        $data['customers'] = Customer::whereIn('id',$remIds)->get();
        
        $lastOrder = array();
        $lastPayment = array();
        $balance = array();
        
        foreach($data['customers'] as $c) {
            
            $result = Order::where('customer_id',$c->id)->orderBy('id','DESC')->first();
            
            $lastOrder[$c->id] = $result;
            
            $result = CustomerPayment::where('customer_id',$c->id)->orderBy('id','DESC')->first();
            
            $lastPayment[$c->id] = $result;
            
            $balance[$c->id] = Order::where('customer_id',$c->id)->sum('total_amount') + $c->opening_balance - CustomerPayment::where('customer_id',$c->id)->sum('amount');
        }
        
        $data['lastOrder'] = $lastOrder;
        $data['lastPayment'] = $lastPayment;
        $data['balance'] = $balance;
      
      return view('followup.auto',$data);
    }
    
    
    public function createFollowupView() {
        
        $data['customers'] = Customer::where('status',true)->get();
        
        return view('followup.create',$data);
        
    }

    
    public function createFollowup(Request $request) {
       
       
                $followup = new Followup();
                $followup->customer_id = $request->customer_id;
                $followup->status = Followup::INPROCESS;
                $followup->next_followup_date = $request->date;
                $followup->save();
                
                $followupDetail = new FollowupDetail();
                $followupDetail->followup_id = $followup->id;
                $followupDetail->next_followup_date = $request->date;
                $followupDetail->user_id = Auth::user()->id;
                $followupDetail->remarks = $request->remarks;
                $followupDetail->save();
                
                
            
        
        
        activity('Create')->log('Generate New Follow up.');
        
        return redirect()->route('followup.upcoming')->with([
                'message'    => 'Followup generated Successfully!',
                'alert-type' => 'success',
            ]);
        
    }
    
    public function getUpComingFollowUp() {
        
        $data['followUps'] = Followup::where('status',Followup::INPROCESS)->with('detail','customer')->whereDate('next_followup_date', '>=', date('Y-m-d'))->orderBy('next_followup_date','ASC')->get();

      
      return view('followup.upcoming',$data);
    }
    
    public function getCompleteFollowUp() {
        
        $data['followUps'] = Followup::where('status',Followup::COMPLETE)->with('detail')->orderBy('next_followup_date','ASC')->get();

      
      return view('followup.complete',$data);
    }

    public function getExpiredFollowUp() {
        
        $data['followUps'] = Followup::where('status',Followup::INPROCESS)->with('detail')->whereDate('next_followup_date', '<', date('Y-m-d'))->orderBy('next_followup_date','DESC')->get();

      return view('followup.expired',$data);
    }

    public function showFollowUpHistory(Request $request) {


        $data['followup'] = Followup::where('id',$request->followup_id)->with('detail')->first();
        

        return view('followup.chat-history-modal',$data);

    }
    
    public function completeFollowUp(Request $request) {

        Followup::where('id',$request->followup_id)->update(['status'=>Followup::COMPLETE]);

        $data['followup'] = Followup::where('id',$request->followup_id)->with('detail')->first();

        return view('followup.chat-history-modal',$data);
    }
    
    
    public function updateFollowupHistory(Request $request) {
        
        $followup = Followup::find($request->followup_id);
    
                $followup->next_followup_date = $request->next_followup_date;
                $followup->save();
                
                $followupDetail = new FollowupDetail();
                $followupDetail->followup_id = $followup->id;
                $followupDetail->next_followup_date = $request->next_followup_date;
                $followupDetail->user_id = Auth::user()->id;
                $followupDetail->remarks = $request->remarks;
                $followupDetail->save();

        activity('Followup History')->log('Followup remarks  added ');
    }
    
    public function deleteFollowUp(Request $request) {

        $followup = PropertyChatHistory::where('id',$request->id)->first();

        PropertyChatHistory::where('id',$request->id)->delete();

        $data['property'] = CustomerProperty::with('chatHistory')->where('id',$followup->property_id)->first();

        return view('followup.chat-history-modal',$data);
    }
    
    public function getCustomerRegister(Request $request) {
        dd($request);
    }
 
    
}
