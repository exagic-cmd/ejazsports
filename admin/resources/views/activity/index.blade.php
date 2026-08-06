@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Activities</h2>
            <p>User activity information.</p>
        </div>

    </div>

    @if(session()->has('message'))
        <div class="alert alert-success text-center">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="alert alert-success alert-div text-center" style="display: none;">

    </div>

    <div class="card mb-4">
        <header class="card-header">
            <div class="row gx-3">
                <div class="col-lg-2 col-6 col-md-3">
                    <small>By Type</small>
                    <select class="form-select" id="log_name" >
                        <option value=""><b>Show all</b></option>
                        <option value="View">View</option>
                        <option value="Create">Create</option>
                        <option value="Update">Update</option>
                        <option value="Delete">Delete</option>

                    </select>
                </div>
                <div class="col-lg-2 col-6 col-md-3">
                    <small>By User</small>
                    <select class="form-select" id="causer_id">
                        <option value=""><b>Show all</b></option>
                        @foreach($users as $u)
                            <option value="{{$u->id}}"> {{$u->name}}</option>
                        @endforeach

                    </select>
                </div>
                <div class="col-lg-2 col-6 col-md-3">
                    <small>By Limit</small>
                    <select class="form-select" id="limit">
                        <option value="">Show all</option>
                        <option value="100">Show 100</option>
                        <option value="250">Show 250</option>
                        <option value="500">Show 500</option>
                    </select>
                </div>

                <div class="col-lg-2 col-6 col-md-3 me-auto">
                    <small>By Date</small>
                    <input type="date" class="form-control" id="date">
                </div>

                <div class="col-lg-2 col-6 col-md-3" style="display: flex;align-items: end;">
                    <button class="btn btn-primary btn-padding btn-submit" >
                        Search Record</button>
                </div>

            </div>
        </header>

        <!-- card-header end// -->
        <div class="card-body" id="update-table">
            <div class="table-responsive" >
                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th scope="col">Log Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Causer Type</th>
                        <th scope="col">Causer Id</th>
                        <th scope="col">Date</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($activities as $activity)
                        @if($activity->log_name == 'Create')
                        <tr     style="background-color: #d8f1e5;">
                            @elseif($activity->log_name == 'Update')
                            <tr style="background-color: #f1e5d8;">
                                @elseif($activity->log_name == 'Delete')
                            <tr style="background-color: #f1d8dd">
                                    @else
                            <tr>
                                @endif
                            <td>{{$sr++}}</td>
                            <td><b>{{$activity->log_name}}</b></td>
                            <td><?php echo $activity->description;?></td>
                            <td>{{$activity->causer_type}}</td>

                            <td>{{$activity->causer ? $activity->causer->name : ''}}</td>
                            <td>{{date('M d, Y h:i',strtotime($activity->created_at))}}</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        <!-- card-body end// -->
    </div>

    @csrf



@stop


@section('js')
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': false, 'sorting' : false, 'paging' : false, 'info' : false, 'searching':false
            });
        } );

        $(document).ready(function() {
            $(".btn-submit").click(function(e){
                e.preventDefault();
                $('.btn-submit').ajaxStart().html('Loading...     ');


                var _token = $("input[name='_token']").val();;
                var type = $("#log_name").val();
                var user = $("#causer_id").val();
                var limit = $("#limit").val();
                var date = $("#date").val();

                $.ajax({
                    url: "{{ route('activity.search') }}",
                    type:'POST',
                    data: {_token:_token, type:type, user:user,limit:limit,date:date},
                    success: function(data) {
                        $('.alert-div').css('display','block').html('Record updated successfully.');
                        document.getElementById('update-table').innerHTML = data;
                        $('#myTable').DataTable({
                            'ordering': false, 'sorting' : false, 'paging' : false, 'info' : false, 'searching':false
                        });
                        $('.btn-submit').ajaxSuccess().html('Search Record');
                    }
                });
            });
        });
    </script>
@stop
