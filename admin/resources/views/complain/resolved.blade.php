@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Resolved Complains</h2>
            <p>Complain information.</p>
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

        <!-- card-header end// -->
        <div class="card-body" id="update-table">
            <div class="table-responsive" >
                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Order #</th>
                        <th scope="col">Type</th>
                        <th scope="col">Detail</th>
                        <th scope="col">Created Time</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($resolvedComplains as $complain)

                        <tr style="text-align: center;">

                            <td>{{$sr++}}</td>
                            <td><b>{{$complain->name}}</b></td>
                            <td>{{$complain->email}}
                            </td>
                            <td>{{$complain->phone_number}}
                            </td>
                            <td>{{$complain->order_no}}</td>
                            <td>
                                @if($complain->type == \App\Models\Complain::DAMAGE_PRODUCT)
                                    <span class="badge rounded-pill alert-info">Damage Product</span>
                                @elseif($complain->type == \App\Models\Complain::WRONG_PRODUCT)
                                    <span class="badge rounded-pill alert-primary">Wrong Product</span>
                                @elseif($complain->type == \App\Models\Complain::MISSING_PRODUCT)
                                    <span class="badge rounded-pill alert-danger">Missing Product</span>
                                @elseif($complain->type == \App\Models\Complain::NOT_DELIVERED)
                                    <span class="badge rounded-pill alert-success">Not Delivered</span>
                                @endif</td>
                            <td>{{$complain->detail}}</td>

                            <td>{{date('M d, Y',strtotime($complain->created_at))}}</td>


                            <td class="text-end">
                                @can('View Complain')
                                    <a href="{{route('complain.show',$complain->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                            @endcan

                            <!-- dropdown //end -->
                            </td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        <!-- card-body end// -->
    </div>





@stop


@section('js')
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': false, 'sorting' : false, 'paging' : true,'pageLength' : 50, 'info' : false, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>
@stop
