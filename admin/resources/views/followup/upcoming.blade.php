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
                        	
                            <h3 class="box-title">All Upcoming FollowUps</h3>
                            
                           

                            <div class="table-responsive">
                                <table id="myTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Created At</th>
                                            <th>Customer</th>
                                            <th>Remarks</th>
                                            <th>Next Follow up</th>
                                            <th>Action</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count =1;?>
                                        


                                        @foreach($followUps as $f)
                                        
                                    
                                        @if($f->next_followup_date == date('Y-m-d'))
                                        <tr style="color:green;">
                                            <td style="font-size:15px;"><b>{{$count++}}<b></td>
                                            <td style="font-size:15px;"><b>{{date('d-m-Y h:i',strtotime($f->created_at))}}</b></td>
                                            
                                             
                                            <td>{{$f->customer ? $f->customer->first_name : ''}}</td>
                                            
                                            <td style="font-size:15px;"><b>{{$f->detail[0]->remarks}}</b></td>
                                            <td style="font-size:15px;"><b>Today</b></td>
                                            
                                            <td class="text-nowrap">

                                                
                                                <a onclick="openHistoryModal({{$f->id}})" href="#!" data-toggle="tooltip" data-original-title="Follow up"><b> <i class="fa fa-eye text-success"></i> Show History </b></a>
                                                
                                            </td>
                                        </tr>

                                            @else
                                            <tr>
                                                <td>{{$count++}}</td>
                                            <td>{{date('d-m-Y h:i',strtotime($f->created_at))}}</td>
                                            
                                           
                                            <td>{{$f->customer ? $f->customer->first_name : ''}}</td>
                                            
                                            <td>{{$f->detail[0]->remarks}}</td>
                                            <td>{{date('d-m-Y',strtotime($f->next_followup_date))}}</td>
                                            
                                            <td class="text-nowrap">

                                                
                                                <a onclick="openHistoryModal({{$f->id}})" href="#!" data-toggle="tooltip" data-original-title="Follow up"> <i class="fa fa-eye text-success"></i> Show History </a>
                                                
                                            </td>
                                        </tr>

                                            @endif
                                            
                                           
                                            
                                        @endforeach

                                    </tbody>
                                </table>
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