@extends('layouts.app')




@section('content')


            <div class="container-fluid">
                <!-- /row -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="white-box">
                            
                            <div class="pull-right">
                                 <a href="{{route('followup.create')}}" class="btn  btn-primary">
									<i class="fa fa-plus-square"></i>&nbsp; Generate Follow up
								</a>
								</div>
                        	
                            <h3 class="box-title">Last 15 Days not changed Ledger </h3>
                            
                           

                            <div class="table-responsive">
                                <table id="myTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Customer</th>
                                            <th>Phone Number</th>
                                            <th>Balance</th>
                                            <th>Last Order</th>
                                            <th>Last Payment</th>
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count =1;?>
                                        


                                        @foreach($customers as $c)
                                        
                                    
                                      
                                        <tr >
                                            <td style="font-size:15px;"><b>{{$count++}}<b></td>
                                  
                                            
                                             
                                            <td>{{$c->first_name}}</td>
                                            
                                            <td>{{$c->phone_number}}</td>
                                            
                                           
                                            
                                             <td>{{number_format($balance[$c->id])}}</td>
                                             
                                             <td>@if($lastOrder[$c->id])
                                             <b>Last Order : </b> {{$lastOrder[$c->id]->total_amount}}
                                             <br>
                                             <b>Date :{{date('d-m-Y',strtotime($lastOrder[$c->id]->created_at))}} </b>
                                             
                                             @endif
                                             
                                             </td>
                                             
                                             
                                             <td>@if($lastPayment[$c->id])
                                             <b> Last Payment: </b> {{$lastPayment[$c->id]->amount}}
                                             <br>
                                             <b>Date :{{date('d-m-Y',strtotime($lastPayment[$c->id]->date))}} </b>
                                             
                                             @endif
                                             
                                             </td>
                                            
                                          
                                        </tr>

                                            
                                           
                                            
                                        @endforeach

                                    </tbody>
                                </table>
                                
                                <br>
                                <br>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
              </div>
            <!-- /.container-fluid -->

            <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  
</div>




@stop

@section('js')
<script>
    $(function() {
        $('#myTable').DataTable({
            'pageLength':50
        });
    });

    function openHistoryModal(followup_id) {

        var data={'followup_id':followup_id};
        $.get('/show_followup_history', data, function (data) {
            document.getElementById('exampleModal1').innerHTML = data;
            $('#exampleModal1').modal('show');
        });
    }
    
    
    function closeChat() {

            $('#exampleModal1').modal('hide');
        
    }
    
    function updateChat() {

    data = {'followup_id':$('#followup_id').val(),'remarks':$('#remarks').val(),'next_followup_date':$('#next_followup_date').val()};

    $.get('/update_followup', data, function (data) {

        $('#exampleModal1').modal('toggle');

        toastr.success("Follow up Updated.");

    });
}

function completeFollowup(followup_id) {

    data = {'followup_id':followup_id};

    $.get('/complete_followup', data, function (data) {

        document.getElementById('exampleModal1').innerHTML = data;

        toastr.success("Follow up Completed Successfully.");

    });
}

        
    </script>
@stop