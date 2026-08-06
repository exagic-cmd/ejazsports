<div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="text-align: center;">Follow up History</h5>
        <button type="button"  onclick="closeChat()" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form >
        
      <div class="modal-body" style="height: auto">
        <input type="hidden" id="followup_id" name="followup_id" value="{{$followup->id}}">

        @if(!$followup->detail->isEmpty())
        <ul style="overflow-y: scroll;
    height: 300px;
    border: 1px solid;">

        @foreach($followup->detail as $d)
        <li style="    line-height: 25px;">

        
          
           
                <label><b>{{date('d:m:Y h:i',strtotime($d->created_at))}}</b>&nbsp;&nbsp;&nbsp;</label>
          
          
                <label>{{$d->remarks}}</label><br>
           
            
              <label style="margin-left: 30px;"><b>Follow up : </b>{{date('d:m:Y',strtotime($d->next_followup_date))}}</label>

              <label style="margin-left:20px;"><b>added by : </b>{{$d->user ? $d->user->name : ''}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

              
          
      </li>


        @endforeach
        
        
      </ul>

        @else

        <div class="form-group form-group-sm" style="margin-top: 30px;">
            <div class="col-xs-12" style="text-align: center;">
                <label >No History!..</label>
            </div>
            
        </div>


        @endif
        
        @if($followup->status == 1) 
        <a  class='btn btn-success' href="javascript:;" onclick="completeFollowup({{$followup->id}})"><i class="fa fa-check"> &nbsp;</i>Complete</a>

        <br><hr>
        @else
        <a disabled class='btn btn-primary' href="javascript:;" ><i class="fa fa-check"> &nbsp;</i>Followup Completed</a>
        
        @endif




@if($followup->status == 1)
        <div class="form-group form-group-sm" style="margin-top: 30px;">
            <div class="col-xs-4">
                <label><b>Add New</b></label>
            </div>
            <div class="col-xs-8">
                <textarea class="form-control" id="remarks" rows="5" name="remarks" ></textarea>
            </div>
            
        </div>

        <div class="form-group form-group-sm" style="margin-top: 5px;">
            <div class="col-xs-4">
                <label><b>Next Follow up</b></label>
            </div>
            
            <div class="col-xs-8" style="margin-bottom: 10px;">
                <input type="date" class="form-control" name="next_followup_date" id="next_followup_date">
            </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" onclick="closeChat()" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" onclick="updateChat()" class="btn btn-primary">Update</button>
      </div>
      
      
      @endif
      </form>
    </div>
  </div>